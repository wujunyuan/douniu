<?php
namespace app\common\model;
use think\Model;
class Room extends Model{
	/**
	 *
	 * @param $memberid
	 */
	public function roomcreate($memberid){
		$cards = model('member') ->getcardnum();
		if($cards == 0){
			//没有房卡
			$this->error = '当前会员没有房卡。';
			return false;
		}
		$this->where(array('member_id' => $memberid)) -> find();
		//会员的房间存在了，不要再创建了

	}
}