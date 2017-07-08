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
            model('member')->comein($ret, array('id' => $this->memberinfo['id']));
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
                $data['info'] = $this->memberinfo['nickname'] . '说：“' . input('post.data') . "”";
                $data['type'] = 1;
                $this->workermandata['content'] = json_encode($data);
                echo $this->curlRequest($this->workermanurl, $this->workermandata);
            }
        }
    }


    private function workermansend($to, $data)
    {
        $this->workermandata['to'] = $to;
        $this->workermandata['content'] = $data;
        return $this->curlRequest($this->workermanurl, $this->workermandata);
    }

    /**
     * 显示游戏界面
     * @return mixed
     */
    public function index()
    {
        //进入房间的ID
        $room_id = input('room_id');
        if ($room_id > 0) {
            $db = model('member');
            $db->comein($room_id, array('id' => $this->memberinfo['id']));
            //$this->memberinfo['room_id'] = $room_id;
        }
        return $this->fetch();
    }

    public function comeout()
    {
        //进入房间的ID
        $memberid = input('memberid') ? input('memberid') : $this->memberinfo['id'];

        $db = model('member');
        //$db->comeout(array('id' => $memberid));
    }

    /**
     * 通知一个会员更新他自己的玩家界面
     */
    public function allmember()
    {
        $db = model('member');
        //只查询在线的人，不在线的把人踢出房间
        $userlist = array_keys((array)json_decode(input('post.userlist')));
        //dump($userlist);
        //会员进入房间时通知所有人更新玩家
        $allmember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        //通知所有会员更新界面

        foreach ($allmember as $v) {
            if (in_array($v['id'], $userlist)) {
                $ret = $db->getothermember($v['id']);
                $return['data'] = ($ret);
                $return['type'] = 4;
                echo $this->workermansend($v['id'], json_encode($return));
            } else {
                //不在线的用户让他退出房间,这样做有问题，应该在用户断线时将他踢出房间
                //$db ->comeout(array('id' => $v['id']));
            }

        }
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