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
		Loader::import('extend.Modules.Nav.Lib.Setting');
		$setting = new \app\Nav\Setting(1);
		//dump(Session::get('member_id'));
		return $this->fetch();
	}

}
