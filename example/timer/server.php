<?php

/**
 * 定时器
 *
 * User: renxiaogang
 * Date: 2014/12/12
 * Time: 15:16
 */


class TimerServer
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1 ,
        ));

        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        // bind callback
        $this->serv->on('Timer', array($this, 'onTimer'));
        $this->serv->start();
    }

    public function onWorkerStart( $serv , $worker_id) {
        // 在Worker进程开启时绑定定时器
        echo "onWorkerStart\n";

        // 只有当worker_id为0时才添加定时器,避免重复添加
        if( $worker_id == 0 ) {
            $serv->addtimer(60000);
            $serv->addtimer(300000);
            $serv->addtimer(600000);
        }
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

    public function onTimer($serv, $interval) {
        switch( $interval ) {
            case 60000: {	//
                echo "每分钟执行一次\n";
                break;
            }
            case 300000:{
                echo "每五分钟执行一次\n";
                break;
            }
            case 600000:{
                echo "每十分钟执行一次\n";
                break;
            }
        }
    }
}
new TimerServer();