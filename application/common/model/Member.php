<?php
namespace app\common\model;
use think\Model;
class Member extends Model{
	public function checklogin($data)
	{
		$map['Email'] = $data['Email'];
		if(empty($map['Email'])){
			$this->error = '账号不能为空';
			return false;
		}
		$result = $this->where($map) ->find();
		//dump($result);
		//dump($data);
		if($result){
			$result = $result->toArray();
			if($result['status'] == 0){
				$this->error = '用户已禁用';
				return false;
			}
			if($result['password'] != $data['password']){
				$this->error ='密码错误请重新输入';
				return false;
			}
			//更新用户登录信息，记录最后登录IP和最后登录时间
			$update['login_ip'] = get_client_ip();
			$update['login_time'] = time();
			$this-> save($update, array('id' => $result['id']));
			return $result;
		}
	}

	/**
	 * 用户进入房间
	 * @param $room_id
	 * @return $this
	 */
	public function comein($room_id){
		return $this -> update(array('room_id' => $room_id));
	}

	/**
	 * 会员退出房间
	 * @return $this
	 */
	public function comeout(){
		return $this -> update(array('room_id' => 0));
	}
}