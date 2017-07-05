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
use think\Session;

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
        //数据模型
        $db = model('member');
        //查询用户的条件
        $map['openid'] = $ret['id'];
        //
        $member = $db->where($map)->find()->toArray();
        //用户存在了，设置session，直接登录
        if ($member) {
            Session::set('memberid', $member['id']);
            $this->redirect(url('Index/index'));
        } else {
            //用户不存在，写入数据，然后设置session再登录
            $data['openid'] = $ret['id'];
            $data['nickname'] = $ret['nickname'];
            //写入数据库信息，返回一个ID，注意$member是一个id
            $memberid = $db -> insert($data);
            if($memberid){
                Session::set('memberid', $memberid);
            }else{
                $this->error($db -> getError());
            }
        }
    }
}