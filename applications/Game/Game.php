<?php
/**
 * 游戏服务器
 * 游戏内业务逻辑
 *
 * @auther moxiaobai
 * @since  2014/12/16
 */

require_once __DIR__ . '/Lib/Autoloader.php';

use \Protocols\JsonProtocol;
use \Server\Statistic;
use \Config\Server;

class Game {

    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9500);

        //通过配置获取
        $this->serv->set(Server::$game);

        //注册Server的事件回调函数
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        //绑定任务
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    //主进程启动
    public function onStart($serv) {
        echo "Server is Running" . PHP_EOL;

        //管理进程的PID，通过向管理进程发送SIGUSR1信号可实现柔性重启
        echo $serv->manager_pid . PHP_EOL;

        //主进程的PID，通过向主进程发送SIGTERM信号可安全关闭服务器
        echo $serv->master_pid . PHP_EOL;

        //print_r($serv->stats());

    }

    public function onWorkerStart($serv, $worker_id) {

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
        if(JsonProtocol::check($data) != 0) {
            return;
        }

        $data = JsonProtocol::decode($data);

        //接收参数
        $class       = $data['class'];
        $method      = $data['method'];
        $params      = $data['params'];
        $startTime   = $this->microtimeFloat();

        // 判断类对应文件是否载入
        if(!class_exists($class))
        {
            $include_file = ROOT_DIR . "Server/$class.php";
            if(is_file($include_file)) {
                require_once $include_file;
            }

            if(!class_exists($class)) {
                $code    = 404;
                $msg     = "class $class not found";

                $result  = array('code'=>$code, 'msg'=>$msg, 'data'=>null);

                $serv->send($fd, JsonProtocol::encode($result));
            }
        }

        // 调用类的方法
        try {
            $ret = call_user_func_array(array(new $class, $method), $params);
            $code   = $ret['code'];
            $msg    = $ret['msg'];

            // 发送数据给客户端，调用成功，data下标对应的元素即为调用结果
            $serv->send($fd, JsonProtocol::encode($ret));
        } catch(Exception $e) {
            // 发送数据给客户端，发生异常，调用失败
            $code   = $e->getCode() ? $e->getCode() : 500;
            $msg    = $e->getMessage();
            $result = array('code'=>$code, 'msg'=>$msg, 'data'=>$e);

            $serv->send($fd, JsonProtocol::encode($result));
        }

        //请求数据统计,放在task执行
        $executionTime = $this->microtimeFloat() - $startTime;
        $report = array(
            'class'       => $class,
            'method'      => $method,
            'params'      => json_encode($params),
            'code'        => $code,
            'msg'         => $msg,
            'execution'   => $executionTime,
            'time'        => time()
        );
        $serv->task(json_encode($report));
    }

    /**
     * @param $serv       swoole_server对象
     * @param $task_id    任务ID
     * @param $from_id    来自于哪个worker进程
     * @param $data       任务内容
     * @return int
     */
    public function onTask($serv, $task_id, $from_id, $data) {
        $data  = json_decode($data, true);
        Statistic::report($data);
        return 1;
    }

    /**
     * @param $serv      swoole_server对象
     * @param $task_id   任务ID
     * @param $data      任务结果
     */
    public function onFinish($serv, $task_id, $data) {
        echo "Task {$task_id} finish" . PHP_EOL;
    }

    public function onClose($serv, $fd, $from_id ) {
        echo "Client {$fd} close connection" . PHP_EOL;
    }

    private function microtimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}

new Game();