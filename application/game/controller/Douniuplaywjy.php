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

    /**
     *进入房间
     */
    public function comein()
    {

        $db = model('member');
        $room_id = input('room_id');
        if ($room_id) {
            $ret = $db->comein($room_id, array('id' => $this->memberinfo['id']));
            $this->allmember();
            if ($ret) {
                $this->success('成功进入房间');
            } else {
                $this->error($db->getError());
            }
        } else {
            $this->error('迷路了，找不到房间！！！');
        }

    }

    public function comeout()
    {
        //进入房间的ID
        $memberid = input('memberid') ? input('memberid') : $this->memberinfo['id'];

        $db = model('member');
        $ret = $db->comeout(array('id' => $memberid));
        //$this->allmember();
        if ($ret) {
            $this->success('有人断线');
        } else {
            $this->error('错误');
        }

    }

    /**
     * 通知一个会员更新他自己的玩家界面
     */
    public function allmember()
    {

        $db = model('member');

        //会员进入房间时通知所有人更新玩家
        $allmember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        $room = model('room')->where(array('id' => $this->memberinfo['room_id']))->find();
        if ($room) {
            $room = $room->toArray();

            $room['taipaitime'] = (int)$room['taipaitime'] - time();
            $room['starttime'] = (int)$room['starttime'] - time();
            $room['qiangtime'] = (int)$room['qiangtime'] - time();
            $room['xiazhutime'] = (int)$room['xiazhutime'] - time();
            if ($room['taipaitime'] < 0) {
                $room['taipaitime'] = 0;
            }
            if ($room['starttime'] < 0) {
                $room['starttime'] = 0;
            }
            if ($room['qiangtime'] < 0) {
                $room['qiangtime'] = 0;
            }
            if ($room['xiazhutime'] < 0) {
                $room['xiazhutime'] = 0;
            }

        }


        if (!$allmember) {
            return;
        }

        //通知所有会员更新界面
        foreach ($allmember as $v) {
            $ret = $db->getothermember($v['id']);
            foreach ($ret as $key => $val) {
                if ($val['gamestatus'] == 2) {
                    $ret[$key]['pai'] = unserialize($val['pai']);
                    //if($ret[$key]['pai']){
                    $ret[$key]['info'] = $this->douniu->getniuname($ret[$key]['pai']);
                    //}

                } else {
                    $ret[$key]['pai'] = array(0, 0, 0, 0, 0);
                    $ret[$key]['info'] = '未知';
                }
            }
            $start = model('room')->getgamestatus(array('id' => $v['room_id']));
            $return['start'] = $start;
            $return['data'] = $ret;
            $return['room'] = $room;
            $return['issetbanker'] = $v['issetbanker'];
            $return['banker'] = unserialize($room['setbanker']);


            $return['playcount'] = $room['playcount'] % 10;
            $return['type'] = 4;
            $return['gamestatus'] = $v['gamestatus'];
            //如果会员摊牌状态，通知前端更新
            if ($return['gamestatus'] == 2) {
                $return['pai'] = unserialize($v['pai']);
                //if($return['pai']){
                $return['info'] = $this->douniu->getniuname($return['pai']);
                //}

            } elseif ($return['gamestatus'] == 1) {
                $return['pai'] = unserialize($v['pai']);
                $return['pai'] = array($return['pai'][0], $return['pai'][1], $return['pai'][2], 0, 0);
            } else {
                $return['pai'] = array();

            }
            $this->workermansend($v['id'], json_encode($return));
        }
    }

    /**
     * 闲家下注
     */
    public function setmultiple()
    {
        $multiple = intval(input('multiple'));
        model('member')->settimes($this->memberinfo['id'], $multiple);
        $this->allmember();
    }

    /**
     * 设置庄家
     */
    public function setbanker()
    {
        $multiple = intval(input('multiple'));
        //if($multiple > 0){
        model('member')->settimes($this->memberinfo['id'], $multiple);
        model('member')->where(array('id' => $this->memberinfo['id'], 'gamestatus' => 1))->update(array('banker' => 1));
        //}
        model('room') -> setbanker($this->memberinfo['room_id']);
        model('member')->where(array('id' => $this->memberinfo['id']))->update(array('issetbanker' => 1));
        $this->allmember();
    }

    public function gameready()
    {
        $islock = model('room')->where(array('id' => $this->memberinfo['room_id']))->value('islock');

        if ($islock == 1 && $this->memberinfo['gamestatus'] < 1) {
            $this->error('游戏进行中，不允许加入');
        }
        if ($this->memberinfo['room_id'] == 0) {
            //都没有进房间，开始什么呀，有毛病
            $this->error('都还没有进房间呢');
        }
        $ret = model('member')->gameready(array('gamestatus' => 0, 'id' => $this->memberinfo['id']));
        $gameinit = 0;
        //所有准备好的人数
        $roomdb = model('room');
        $map = array('id' => $this->memberinfo['room_id']);
        $allmember = $roomdb->getmember($map);
        foreach ($allmember as $v) {
            if ($v['gamestatus'] == 1) {
                //发现有人未准备，游戏不开始
                $gameinit++;
            }
        }
        if ($ret) {
            //两个准备就开始倒计时
            if ($gameinit == 2) {
                //两个人参与时有两种情况
                if ($gameinit == count($allmember)) {
                    //房间里就两个人，马上就开始了
                    model('room')->where(array('id' => $this->memberinfo['room_id']))->update(array('starttime' => time(), 'gamestatus' => 1));
                } else {
                    //房间里还有其他人，超过两个了，再等5秒
                    model('room')->where(array('id' => $this->memberinfo['room_id']))->update(array('starttime' => time() + 5, 'gamestatus' => 1));
                }

            }
        }

        //游戏可以开始了，通知房间中所有会员
        $starttime = (int)model('room')->where(array('id' => $this->memberinfo['room_id']))->value('starttime');
        //人齐了，或者人不齐时间到了，两者其一满足就发牌，开始游戏
        if (($gameinit > 1 && $starttime <= time()) || $gameinit == count($allmember)) {
            //这里发牌
            $this->init();
        }
        if ($ret) {

            $this->allmember();
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
    private function init()
    {
        //查询房间中所有会员， 这个动作是最后一个准备游戏的会员触发的
        $allmember = model('room')->getmember(array('id' => $this->memberinfo['room_id']));
        //遍历所有会员，每人发一副牌，算好牌型，然后把数据存到数据库中的member表的pai字段
        $memberdb = model('member');
        foreach ($allmember as $v) {
            if ($v['gamestatus'] == 1) {
                $pai = $this->douniu->create();
                $data['pai'] = serialize($pai);
                $map['id'] = $v['id'];
                //写入牌的大小
                $data['pairet'] = $this->douniu->ret($pai);
                $memberdb->where($map)->update($data);
            }

        }
        //摊牌时间
        $time = time();
        model('room')->where(array('id' => $this->memberinfo['room_id']))->update(array('islock' => 1, 'qiangtime' => $time + 15, 'taipaitime' => $time + 30, 'gamestatus' => 2));

        $this->allmember();
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
        $ret = model('member')->gameshowall(array('gamestatus' => 1, 'id' => $this->memberinfo['id']));
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
        $taipaitime = (int)model('room')->where(array('id' => $this->memberinfo['room_id']))->value('taipaitime');
        if ($taipaitime - time() <= 0) {
            model('member')->gameshowall(array('gamestatus' => 1, 'room_id' => $this->memberinfo['room_id']));
            $gameshowall = true;
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
        $return = model('room')->gameinit(array('id' => $this->memberinfo['room_id']));
        $this->allmember();

        if ($return) {

        } else {
            //游戏次数用完了，要加房卡
        }

    }

}
