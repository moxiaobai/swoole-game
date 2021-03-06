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
            'heartbeat_check_interval' => 60,
            'dispatch_mode' => 2,
            'debug_mode'=> 1
        ));

        //注册Server的事件回调函数
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        $this->serv->start();
    }

    //主进程
    public function onStart($serv) {
        //cli_set_process_title('MainWorker');
        echo "Server Start" . PHP_EOL;

        //管理进程的PID，通过向管理进程发送SIGUSR1信号可实现柔性重启
        echo $serv->manager_pid . PHP_EOL;

        //主进程的PID，通过向主进程发送SIGTERM信号可安全关闭服务器
        echo $serv->master_pid . PHP_EOL;
    }

    //进程组
    public function onWorkerStart( $serv , $worker_id) {
        //cli_set_process_title('GroupWorker');
        // 在Worker进程开启时绑定定时器
        echo "Woker进程组: $worker_id" . PHP_EOL;
    }

    public function onConnect(swoole_server $serv, $fd, $from_id ) {
        //获取连接的客户端信息
        $fdInfo = $serv->connection_info($fd);
        echo '<pre>';
        print_r($fdInfo);
        echo '</pre>';

        $serv->send( $fd, "Welcome {$fd} Connect Server" );
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Hello，Get Message From Client {$fd}:{$data}" . PHP_EOL;
    }

    public function onClose(swoole_server $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection" . PHP_EOL;
    }
}

// 启动服务器
$server = new Server();