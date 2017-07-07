<?php
/**
 * Created by PhpStorm.
 * User: wujunyuan
 * Date: 2017/7/3
 * Time: 13:26
 */

namespace app\game\controller;

use think\Controller;
use think\Loader;

class Douniuplay extends Common
{
    //显示玩家玩游戏的页面
    public function index()
    {
        return $this->fetch();
    }
    public function __construct()
    {
        parent::__construct();
        //加载斗牛类
        Loader::import('extend.Game.douniu');
        //创建一个斗牛实例
        $this->douniu = new \douniu(array());
    }

    /**
     * 创建房间
     */
    public function create_room()
    {
       
    }

    /**
     * 这里要引入斗牛类了
     * 开始游戏，洗牌，发牌生成N副牌
     * 这里是把数据直接传回前端的
     */
    public function init()
    {
        $p = $this->douniu -> create();
        dump($p);
    }
}