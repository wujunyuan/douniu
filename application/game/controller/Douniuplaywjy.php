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
     * 底分：score【1,3,5,10,20】
     * 规则、牌型倍数：rule【1,2】，types【1,2,3】
     * 房卡游戏局数：gamenum【10:1,20:2】
     * 固定上庄：openroom【0,100,300,500】
     */
    public function roomcreate()
    {
        $rule['score'] = input('post.score');
        $rule['types'] = input('post.types');
        $rule['rule'] = input('post.rule');
        $rule['gamenum'] = input('post.gamenum');
        if (input('post.openroom')) {
            $rule['openroom'] = input('post.openroom');
        }

        $roomdb = model('room');
        $ret = $roomdb->roomcreate($this->memberinfo['id'], $rule);

        if ($ret) {
            model('member')->comein($ret, array('id' => $this->memberinfo['id']));
            $this->success('创建成功', url('index', array('room_id' => $ret)));
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
                $data['info'] = input('post.data');
                $data['from'] = $memberid;
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
            $ret = $db->comein($room_id, array('id' => $this->memberinfo['id']));
            if ($ret === false) {
                $this->error($db->getError());
            }
            //$this->memberinfo['room_id'] = $room_id;
            $room = model('room')->where(array('id' => $room_id))->find();
            if (!$room) {
                $this->error('房间不存在啊！！！');
            }
            $room = $room->toArray();
            $this->assign('gamerule', unserialize($room['rule']));
            $this->assign('room', $room);
        } else {
            $this->error('迷路了，找不到房间！！！');
        }
        return $this->fetch();
    }

    public function comeout()
    {
        //进入房间的ID
        $memberid = input('memberid') ? input('memberid') : $this->memberinfo['id'];

        $db = model('member');
        $db->comeout(array('id' => $memberid));
    }

    /**
     * 通知一个会员更新他自己的玩家界面
     */
    public function allmember()
    {
        $db = model('member');
        //会员进入房间时通知所有人更新玩家
        $allmember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        //通知所有会员更新界面
        foreach ($allmember as $v) {
            $ret = $db->getothermember($v['id']);
            foreach ($ret as $key => $val) {
                if($val['gamestatus'] == 2){
                    $ret[$key]['pai'] = unserialize($val['pai']);
                    $ret[$key]['info'] = $this->douniu->getniuname($ret[$key]['pai']);
                }else{
                    $ret[$key]['pai'] = array(0,0,0,0,0);
                    $ret[$key]['info'] = '未知';
                    //unset($ret[$key]['pai']);
                }
            }
            $return['data'] = $ret;
            $return['type'] = 4;
            $return['gamestatus'] = $v['gamestatus'];
            //如果会员摊牌状态，通知前端更新
            if ($return['gamestatus'] == 2) {
                $return['pai'] = unserialize($v['pai']);
                $return['info'] = $this->douniu->getniuname($return['pai']);
            }elseif($return['gamestatus'] == 1){
                $return['pai'] = unserialize($v['pai']);
                $return['pai'] = array($return['pai'][0],$return['pai'][1],$return['pai'][2],0,0);
            }else{
                $return['pai'] = array();

            }
            echo $this->workermansend($v['id'], json_encode($return));
        }
    }


    public function gameready()
    {
        if ($this->memberinfo['room_id'] == 0) {
            //都没有进房间，开始什么呀，有毛病
            $this->error('都还没有进房间呢');
        }
        $ret = model('member')->gameready(array('id' => $this->memberinfo['id']));
        $gameinit = true;
        //所有准备好的人数
        $roomdb = model('room');
        $map = array('id' => $this->memberinfo['room_id']);
        $allmember = $roomdb->getmember($map);
        foreach ($allmember as $v) {
            if ($v['gamestatus'] == 0) {
                //发现有人未准备，游戏不开始
                $gameinit = false;
            }
        }
        //游戏可以开始了，通知房间中所有会员
        if ($gameinit && count($allmember) > 1) {
            //这里发牌
            $this->init();
        }
        if ($ret) {
            $this->success('准备好了');
        } else {
            $this->error(model('member')->getError());
        }
    }


    /**
     * 这里要引入斗牛类了
     * 开始游戏，洗牌，发牌生成N副牌
     * 这里是把数据直接传回前端的
     */
    public function init()
    {
        //查询房间中所有会员， 这个动作是最后一个准备游戏的会员触发的
        $allmember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        //遍历所有会员，每人发一副牌，算好牌型，然后把数据存到数据库中的member表的pai字段
        $memberdb = model('member');
        foreach ($allmember as $v) {
            $pai = $this->douniu->create();
            $data['pai'] = serialize($pai);
            $map['id'] = $v['id'];
            $memberdb->where($map)->update($data);


        }
        $this -> allmember();
    }

    /**
     * 摊牌
     **/
    public function showall()
    {
        if ($this->memberinfo['room_id'] == 0) {
            //都没有进房间，开始什么呀，有毛病
            $this->error('都还没有进房间呢');
        }
        $ret = model('member')->gameshowall(array('id' => $this->memberinfo['id']));
        $gameshowall = true;
        //所有准备好的人数
        $roomdb = model('room');
        $map = array('id' => $this->memberinfo['room_id']);
        $allmember = $roomdb->getmember($map);
        foreach ($allmember as $v) {
            if ($v['gamestatus'] != 2) {
                //发现有人未摊牌
                $gameshowall = false;
            }
        }
        //
        $this->allmember();
        //游戏可以开始了，通知房间中所有会员
        if ($gameshowall) {
            //游戏结束
            $this->theend();
        }
        if ($ret) {
            $this->success('处理正确');
        } else {
            $this->error(model('member')->getError());
        }


    }

    /**
     * 一局结束，这里要重新来一局
     */
    public function theend()
    {
        //通知前端显示再来一局的准备按钮,这里要计算游戏结果
        //计算出游戏结果后，初始化，牌的数据和牌型全改为原始状态
        //查询房间中所有会员， 这个动作是最后一个准备游戏的会员触发的
        model('room') -> gameinit(array('id' => $this->memberinfo['room_id']));
    }

}