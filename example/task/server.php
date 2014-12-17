<?php
/**
 * Task使用
 *
 * User: renxiaogang
 * Date: 2014/12/12
 * Time: 14:08
 */

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9502);
        $this->serv->set(array(
            'worker_num' => 4,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
            'task_worker_num' => 8
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        // bind task callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }
    public function onStart( $serv ) {
        cli_set_process_title('TaskMaster');
        echo "Start\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "工人: {$from_id}等待处理任务" . PHP_EOL;
        echo "Client {$fd} connect" . PHP_EOL;
    }

    public function onReceive( $serv, $fd, $from_id, $data ) {
        echo "工人: {$from_id} 开始处理任务" . PHP_EOL;
        echo "Get Message From Client {$fd}:{$data}\n";

        // send a task to task worker.
        $serv->task($data);


        echo "继续执行任务\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

    public function onTask($serv, $task_id, $from_id, $data) {
        echo "任务: {$task_id} 由任务工人: {$from_id} 执行\n";
        //echo "Data: {$data}\n";

        //处理业务逻辑
        for($i = 0 ; $i < 4 ; $i ++ ) {
            sleep(1);
            echo "Taks {$task_id} Handle {$i} times...\n";
        }

        return "$data -> OK";
    }

    public function onFinish($serv, $task_id, $data) {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }
}
$server = new Server();