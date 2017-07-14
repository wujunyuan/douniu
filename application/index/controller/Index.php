<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Validate;
use think\Session;
use think\Loader;

class Index extends Common
{
	public function index()
	{
		$this->paihang(1);
	}

	public function paihang($roomid)
	{
		$roomdb = model('room');
		$memberdb = model('member');

		//从房间中查询得到当前游戏的规则内容，是一个序列化的数组，要反序列
		$rule = $roomdb -> where(array('id' => $roomid)) -> value('rule');
		$rule = unserialize($rule);
		//底分
		$score = $rule['score'];
		//获到庄家的信息
		$banker = $memberdb -> where(array('room_id' => $roomid, 'banker' => 1)) -> find();
		if($banker){
			$banker = $banker -> toArray();
		}
		$member = $memberdb->where(array('room_id' => $roomid, 'banker' => 0))->select();
		foreach ($member as $k => $v) {
			$v = $v -> toArray();
			if($v['pairet'] > $banker['pairet']){
				//庄赢
			}else{
				//闲赢
			}
			//这里接着写入库操作





		}
	}
}