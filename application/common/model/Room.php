<?php
namespace app\common\model;
use think\Model;
use think\Db;
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
        $room = $this->find() -> toArray();
        $num = $db -> where(array('room_id' => $room['id'])) -> count();
        return $num;
    }


    /**
     * 创建房间
     */
    public function create_room()
    {

    }
}
