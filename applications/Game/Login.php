<?php
/**
 * 登录服务器
 * 1、登录注册  2、在线数据（任务Task）
 *
 * User: renxiaogang
 * Date: 2014/12/17
 * Time: 11:33
 */

class Login {

    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9500);

        //通过配置获取
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

    //主进程启动
    public function onStart($serv) {
        echo "Server is Running" . PHP_EOL;
    }

    //进程组启动
    public function onWorkerStart( $serv , $worker_id) {
        //cli_set_process_title('GroupWorker');
        // 在Worker进程开启时绑定定时器
        //echo "进程组:$worker_id" . PHP_EOL;
    }

    public function onConnect($serv, $fd, $from_id ) {

    }

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
        $class   = $data['class'];
        $method  = $data['method'];
        $params  = $data['params'];

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

            // 发送数据给客户端，调用成功，data下标对应的元素即为调用结果
            $serv->send($fd, JsonProtocol::encode($ret));
        } catch(Exception $e) {
            // 发送数据给客户端，发生异常，调用失败
            $code   = $e->getCode() ? $e->getCode() : 500;
            $result = array('code'=>$code, 'msg'=>$e->getMessage(), 'data'=>$e);

            $serv->send($fd, JsonProtocol::encode($result));
        }
    }

    public function onClose($serv, $fd, $from_id) {

    }
}