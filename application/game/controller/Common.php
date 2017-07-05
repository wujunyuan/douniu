<?php

/**
 * Created by PhpStorm.
 * User: wujunyuan
 * Date: 2017/7/3
 * Time: 13:20
 * 这里定义的是整个游戏的入口
 */
namespace app\game\controller;

use think\Db;
use think\Controller;
use think\Request;
use think\Session;

class Common extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct();
        //用户没有登录，让他去登录
        if (!Session::has('member_id')) {
            //跳转到登录页面
            $this->redirect(url('Login/index'));
        }
        //用户的所有信息
        $map['id'] = Session::get('member_id');
        $member = Db::name('member') -> where($map)->find();
        $this->assign('memberinfo', $member);
    }
}