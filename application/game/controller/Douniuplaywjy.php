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

class Douniuplaywjy extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->workermanurl = 'http://127.0.0.1:2121';
        $this->workermandata['type'] = 'publish';
        $this->workermandata['content'] = '';
        $this->workermandata['to'] = 0;
        //加载斗牛类
        Loader::import('extend.Game.douniu');
        //创建一个斗牛实例
        $this->douniu = new \douniu(array());
    }

    /**
     * 创建房间
     */
    public function roomcreate()
    {
        $rule = input("post.rule");
        $roomdb = model('room');
        $ret = $roomdb->roomcreate($this->memberinfo['id'], $rule);

        if ($ret) {
            model('member') -> comein($ret, array('id' => $this->memberinfo['id']));
            $this->success('创建成功', url('index'));
        } else {
            $this->error($roomdb->getError());
        }
    }

    /**
     * @param $url
     * @param string $data
     * @param string $method
     * @param string $cookieFile
     * @param string $headers
     * @param int $connectTimeout
     * @param int $readTimeout
     * @return mixed
     */
    private function curlRequest($url, $data = '', $method = 'POST', $cookieFile = '', $headers = '', $connectTimeout = 30, $readTimeout = 30)
    {
        $method = strtoupper($method);

        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT => $readTimeout
        );

        if ($data && strtolower($method) == 'post') {
            $option[CURLOPT_POSTFIELDS] = $data;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $option);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: '));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function sendmsg()
    {
        $memberid = $this->memberinfo['id'];
        $roommember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        foreach ($roommember as $v) {
            if ($memberid != $v['id']) {
                //发给除了当前会员之外的房间中所有人
                $this->workermandata['to'] = $v['id'];
                $data['info'] = $this->memberinfo['nickname'].'说：“'.input('post.data')."”";
                $data['type'] = 1;
                $this->workermandata['content']=json_encode($data);
                echo $this->curlRequest($this->workermanurl, $this->workermandata);
            }
        }
    }

    /**
     * 显示游戏界面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 这里要引入斗牛类了
     * 开始游戏，洗牌，发牌生成N副牌
     * 这里是把数据直接传回前端的
     */
    public function init()
    {
        $p = $this->douniu->create();
        dump($p);
    }
}