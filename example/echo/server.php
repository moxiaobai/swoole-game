<?php
/**
 * 简单的Echo Server
 * User: renxiaogang
 * Date: 2014/12/12
 * Time: 13:37
 */

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            //Worker进程数
            'worker_num' => 4,
            //设置程序进入后台作为守护进程运行
            'daemonize' => false,
            //每个worker进程允许处理的最大任务数
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1
        ));

        //注册Server的事件回调函数
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        $this->serv->start();
    }

    public function onStart($serv) {
        echo "Server Start" . PHP_EOL;
    }

    public function onConnect(swoole_server $serv, $fd, $from_id ) {
        $serv->send( $fd, "Welcome {$fd} Connect Server" );
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}" . PHP_EOL;
    }

    public function onClose(swoole_server $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection" . PHP_EOL;
    }
}

// 启动服务器
$server = new Server();