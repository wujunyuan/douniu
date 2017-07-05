<?php
/**
 * Created by PhpStorm.
 * User: wujunyuan
 * Date: 2017/7/3
 * Time: 13:27
 */

namespace app\game\controller;


use think\Controller;
use Hooklife\ThinkphpWechat\Wechat;
class Login extends Controller
{
    /**
     *显示用户登录界面
     */
    public function index()
    {
        //使用微信登录，直接跳转到微信授权地址，这里要用微信的开发包了
        return Wechat::app()->oauth->scopes(['snsapi_userinfo'])->redirect();
        //dump($list);
    }

    /**
     * 处理用户登录
     * 接收数据，然后验证
     */
    public function dologin()
    {

    }

    /**
     * 微信登录，这里发起微信登录请求，要设置一个回调地址
     */
    public function weixinlogin()
    {

    }

    /**
     * 微信登录回调地址，这里获取微信传回来的数据，然后查询数据库，验证用户信息登录
     */
    public function weixinloginback()
    {
        $user = Wechat::app()->oauth->user();
        $ret = $user->toArray();
		echo '授权正确，获取的用户信息如下：';
		dump($ret);
    }
}