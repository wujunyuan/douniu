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
        if(!$room){
            $this->error = '房间不存在！';
            return false;
        }
        $room = $room ->toArray();
        $num = $db -> where(array('room_id' => $room['id'])) -> count();
        return $num;
    }

    /**
     * 获取房间中的所有会员
     * @return int|string
     */
    public function getmember($where = array())
    {
        $room = $this->where($where) ->find();
        if(!$room){
            $this->error = '房间不存在！';
            return false;
        }
        $room = $room ->toArray();
        $map['room_id'] = $room['id'];
        $member = Db::name('member') -> where(array('room_id' => $room['id'])) -> select();
        return $member;
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
        $cards = $memberdb ->getcardnum(array('id' => $memberid));
        if($cards == 0){
            //没有房卡
            $this->error = '当前会员没有房卡。';
            return false;
        }
        $room = $this->where(array('member_id' => $memberid)) -> find();
        //会员的房间存在了，不要再创建了
        if(!$room){
            $data['member_id'] = $memberid;
            $data['open_time'] = time();
            $data['rule'] = serialize($rule);
            //房间号重复没有关系，好看就行了，A开头
            $data['room_num'] = 'A'.rand(10,99);
            $ret = $this->insert($data);
            if($ret){
                //成功后返回房间的ID，注意这不是房间号
                return $ret;
            }else{
                return false;
            }
        }else{
            $ret = $this->where(array('id' => $room['id'])) -> update(array('rule' => serialize($rule),'open_time' => time()));
            if($ret !== false){
                return $room['id'];
            }else{
                return false;
            }
        }
    }

}
