<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Session;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 15:43
 */
class Room extends Model
{
    //在模型中获取当前房间的人数,方便控制器调用
    public function get_num()
    {
        $db = model('member');
        $room = $this->find();
        if (!$room) {
            $this->error = '房间不存在！';
            return false;
        }
        $room = $room->toArray();
        $num = $db->where(array('room_id' => $room['id']))->count();
        return $num;
    }

    /**
     * 获取房间中的所有会员
     * @return int|string
     */
    public function getmember($where = array())
    {
        $room = $this->where($where)->find();
        if (!$room) {
            $this->error = '房间不存在！';
            return false;
        }
        $room = $room->toArray();
        $map['room_id'] = $room['id'];
        $member = Db::name('member')->where(array('room_id' => $room['id']))->select();
        return $member;
    }

    public function gameinit($where = array())
    {
        $room = $this->where($where)->find();

        if (!$room) {
            $this->error = '房间不存在！';
            return false;
        }


        $room = $room->toArray();
        $map['room_id'] = $room['id'];
        Db::name('member')->where(array('room_id' => $room['id']))->update(array('pai' => '', 'gamestatus' => 0));


        model('room') -> where(array('id' => $room['id'])) -> update(array('islock'=> 0));


        if ($room['room_cards_num'] <= 0 && $room['playcount'] <= 0) {
            $this->error = '房卡耗完了';
            $this->account();
            return false;
        }



        model('room')->where(array('id' => $room['id']))->setDec('playcount', 1);

        if ($room['room_cards_num'] > 0 && $room['playcount'] <= 1) {
            model('room')->where(array('id' => $room['id']))->setDec('room_cards_num', 1);
            model('room')->where(array('id' => $room['id']))->update(array('playcount' => 10));
            //这里10局完了
            $this->account();
        }
        return true;
    }


    /**
     * 创建房间
     * @param $memberid
     * @param $rule
     * @return $this|bool|int|mixed|string
     */
    public function roomcreate($memberid, $rule = array())
    {
        $memberdb = model('member');
        $cards = $memberdb->getcardnum(array('id' => $memberid));
        if ($cards == 0) {
            //没有房卡
            $this->error = '当前会员没有房卡。';
            return false;
        }
        //看看用户能不能开一个房间
        //用户要开的房间是什么类型呢，他要多少房卡
        $roomtype = explode(':', $rule['gamenum']);
        if ($roomtype[1] > $cards) {
            $this->error = '房卡不够，请重新选择！';
            return false;
        }

        $room = $this->where(array('member_id' => $memberid))->find();
        //会员的房间存在了，不要再创建了
        if (!$room) {
            $data['member_id'] = $memberid;
            $data['open_time'] = time();
            $data['rule'] = serialize($rule);
            $data['room_cards_num'] = $roomtype[1];
            //$data['playcount'] = 0;

            //房间号重复没有关系，好看就行了，A开头
            $data['room_num'] = 'A' . rand(10, 99);
            $ret = $this->insert($data);
            if ($ret) {
                //扣除会员房卡数量
                model('member')->where(array('id' => $memberid))->setDec('cards', $roomtype[1]);
                //成功后返回房间的ID，注意这不是房间号
                return $ret;
            } else {
                return false;
            }
        } else {
            $update['room_cards_num'] = $roomtype[1] +  $room['room_cards_num'];
            //$update['playcount'] = 0;
            $update['rule'] = serialize($rule);
            $update['open_time'] = time();
            $ret = $this->where(array('id' => $room['id']))->update($update);
            if ($ret !== false) {
                model('member')->where(array('id' => $memberid))->setDec('cards', $roomtype[1]);
                return $room['id'];
            } else {
                return false;
            }
        }
    }

    /**
     * 发现房间中有一个会员未准备就返回false
     * @param array $where
     * @return bool
     */
    public function getgamestatus($where = array())
    {
        $room = $this->where($where)->find();
        if (!$room) {
            $this->error = '房间不存在！';
            return false;
        }
        $room = $room->toArray();
        $map['room_id'] = $room['id'];
        $member = Db::name('member')->where(array('room_id' => $room['id']))->select();
        $status = true;
        foreach ($member as $v) {
            if ($v['gamestatus'] == 0) {
                $status = false;
            }
        }
        return $status;
    }

    /**
     *
     */
    public function account()
    {
        //游戏的结算方法，十局之后从日志表中读取统计结果进行结算，这里需要一个日志表，用来记录每局游戏的结果，结算后清空

    }
}
