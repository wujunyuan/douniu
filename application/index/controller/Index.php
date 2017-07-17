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
		$ret = model('room')->getrankinglist(3);
		dump($ret);
		dump(model('room') -> getError());

	}


}
