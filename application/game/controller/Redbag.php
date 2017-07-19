<?php
namespace app\game\controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 16:49
 */
use think\Controller;

class Redbag extends Common
{
    public  function  index()
    {
        //显示红包页面
        return $this->fetch();
    }
    //接收红包数量
    public function getdata()
    {
        $num = intval(input('post.roomcard'));
        $result =  model('redbag') -> create_bag($this -> memberinfo['id'] , $num);
        if($result){
            $this->success('创建成功');
        }else{
            $this->error('失败，房卡不够');
        }
    }
}