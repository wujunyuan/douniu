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
        if($room['gamestatus'] != 3){
            //游戏进行中，不能重置
            return false;
        }
        $map['room_id'] = $room['id'];
        Db::name('member')->where(array('room_id' => $room['id']))->update(array('pai' => '', 'gamestatus' => 0, 'banker' => 0, 'issetbanker' => 0, 'issetmultiple' => 0));
        model('room')->where(array('id' => $room['id']))->update(array('islock' => 0, 'gamestatus' => 0));
        if ($room['room_cards_num'] <= 0 && $room['playcount'] <= 0) {
            $this->error = '房卡耗完了';
            $this->account($room['id']);
            return false;
        }
        model('room')->where(array('id' => $room['id']))->setDec('playcount', 1);
        if ($room['room_cards_num'] > 0 && $room['playcount'] <= 1) {
            model('room')->where(array('id' => $room['id']))->setDec('room_cards_num', 1);
            model('room')->where(array('id' => $room['id']))->update(array('playcount' => 10, 'gamestatus' => 0));
            //这里10局完了
            $this->account($room['id']);
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
            $update['room_cards_num'] = $roomtype[1] + $room['room_cards_num'];
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
        $status = 0;
        foreach ($member as $v) {
            if ($v['gamestatus'] >= 1) {
                $status++;
            }
        }
        if (($status > 1 && $room['starttime'] <= time()) || $status == count($member)) {
            return true;
        }
        return false;
    }

    /**
     * 设置庄家返回一个庄家的设定结果，前端可能要写动画，到时返回前端一个数组，其中包含抢庄会员的ID，最后一个ID是庄家，前端遍历时可以每次高亮一个会员头像，做成抽奖的样式，这要求数据在返回时打乱，其实就是生成一个打乱的ID集合，确定最后一个是庄家，返回结果时把其它ID的会员的banker改成0，这样庄家就确定下来了
     * @param $roomid
     * @return array
     */
    public function setbanker($roomid)
    {
        $bankerid = array();
        $id = array();
        $room = $this->where(array('id' => $roomid)) -> find();
        if(!$room){
            $this->error = '房间不存在';
        }

        $room = $room -> toArray();
        if($room['gamestatus'] != 2){
            //$ret[] = model('member') -> where(array('room_id' => $roomid, 'gamestatus' => 1, 'banker' => 1)) -> value('id');
            return false;
        }
        $allmember = model('member') -> where(array('room_id' => $roomid, 'gamestatus' => 1)) -> select();
        $issetbanker = 0;
        if($allmember){
            foreach($allmember as $k => $v){
                $member = $v->toArray();
                if($member['banker'] == 1){
                    //抢庄的会员
                    $bankerid[] = $member['id'];
                }else{
                    //不抢庄的会员
                    $id[] = $member['id'];
                }
                if($v['issetbanker'] == 1){
                    $issetbanker ++;
                }
            }
        }else{
            $this->error = '房间没有人';
            return false;
        }
        //不只有两个人
        //dump(count($bankerid));
        //截止时间未到，有人没有抢并且已经有人抢了，这时不生成庄家
        if(count($allmember) > ($issetbanker) && (int)$room['qiangtime'] - time() > 0){
            //有人抢
            $this->error = '抢庄中';
            return false;
        }

        if(count($bankerid) > 0){
            //有人抢庄，把抢庄的人ID打乱，最后一个是庄家
            shuffle($bankerid);
            $ret = $bankerid;
        }else{
            //没有人抢庄，把所有人ID打乱，最后一个是庄家
            shuffle($id);
            $ret = $id;
        }
        //取最后一个ID
        $lastid = $ret[(count($ret)-1)];
        //把其它不是庄家的banker改成0
        model('member') -> where(array('id' => array('neq', $lastid), 'room_id' => $roomid)) -> update(array('banker' => 0));
        $time = time();
        $this->where(array('id' => $room['id'])) -> update(array('gamestatus' => 3,'qiangtime' => $time - 1,'taipaitime' => $time + 15, 'setbanker' => serialize($ret)));
        //返回结果
        return $ret;
    }

    public function bankerexist($roomid)
    {
        return model('member')->where(array('room_id' => $roomid, 'banker' => 1))->select();
    }

    /**
     *
     */
    public function account($roomid)
    {
        //游戏的结算方法，十局之后从日志表中读取统计结果进行结算，这里需要一个日志表，用来记录每局游戏的结果，结算后清空
        $memberdb = model('member');
        $result = Db::name('moneydetailtemp')->where(array('room_id' => $roomid))->select();
        // dump($result);
        foreach ($result as $k => $v) {
            //每一局都要改变玩家的金币数量
            $memberdb->where(array('id' => $v['member_id']))->setInc('money', $v['num']);
            unset($v['id']);
            //转存到moneydetail表
            Db::name('moneydetail')->insert($v);
        }
        //删除临时记录，再开始玩十局要重新累计
        Db::name('moneydetailtemp')->where(array('room_id' => $roomid))->delete();
    }
}
