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
		$list = Db::name('paihistory') -> where(array('room_id' => 49)) -> order('id desc') -> select();
		//加载斗牛类
		Loader::import('extend.Game.douniu');
		//创建一个斗牛实例
		$douniu = new \douniu(array());
		foreach($list as $k =>$v){
			dump($douniu -> getniuname(unserialize($v['pai'])));
		}
	}


}
