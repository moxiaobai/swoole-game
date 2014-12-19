<?php

/**
 * 基于websocket在线聊天室
 * User: renxiaogang
 * Date: 2014/12/19
 * Time: 14:24
 */

require_once __DIR__ . '/Lib/Autoloader.php';

use \Protocols\WebSocket;
use \Config\Server;

class Chat {

    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9600);

        //通过配置获取
        $this->serv->set(Server::$chat);

        //注册Server的事件回调函数
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        //绑定任务
        $this->serv->start();
    }

    //主进程启动
    public function onStart($serv) {
        echo "Server is Running" . PHP_EOL;
    }

    public function onConnect($serv, $fd, $from_id ) {}

    /**
     * 服务端接收数据
     *
     * @param $serv      swoole_server对象
     * @param $fd        连接的描述符
     * @param $from_id   reactor的id，无用
     * @param $data      接收数据
     */
    public function onReceive(swoole_server $serv, $fd, $from_id, $data) {
        //检测数据完整性
        WebSocket::check($data);

    }

    public function onClose($serv, $fd, $from_id ) {
        echo "Client {$fd} close connection" . PHP_EOL;
    }
}

new Chat();