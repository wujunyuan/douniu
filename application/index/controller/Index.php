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
		dump(json_encode(unserialize('a:3:{i:0;a:1:{i:0;a:3:{i:0;i:2;i:1;i:3;i:2;i:2;}}i:1;a:1:{i:0;a:3:{i:0;i:3;i:1;i:8;i:2;i:2;}}i:2;a:3:{i:8;i:2;i:3;i:0;i:2;i:-2;}}')));

	}


}
