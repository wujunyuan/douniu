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
		echo (int)'1499932725';
		model('room')->account(1);
	}

}
