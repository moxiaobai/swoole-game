<?php

require_once 'JsonProtocol.php';

//// 建立与服务端的链接
$socket = stream_socket_client("tcp://192.168.1.248:9500", $err_no, $err_msg);
if(!$socket) {
   exit($err_msg);
}

// 设置为阻塞模式
stream_set_blocking($socket, true);

$class  = 'Member';
//$method = 'userInfoByMid';
//$params = array('mid'=>13392);

$method = 'authLogin';
$params = array('username'=>'moxiaobai', 'password'=>'rxg622124');
$data   = array('class'=>$class, 'method'=>$method, 'params'=>$params);

$buffer = JsonProtocol::encode($data);

stream_socket_sendto($socket, $buffer);

// 读取服务端返回的数据
$result =  stream_socket_recvfrom($socket, 65535);


$rs = JsonProtocol::decode($result);
echo '<pre>';
print_r($rs);
echo '</pre>';

// 关闭链接
//fclose($socket);



exit;

class Client
{
    private $client;

    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on('Connect', array($this, 'onConnect'));
        $this->client->on('Receive', array($this, 'onReceive'));
        $this->client->on('Close', array($this, 'onClose'));
        $this->client->on('Error', array($this, 'onError'));
    }

    public function connect() {
        $fp = $this->client->connect("127.0.0.1", 9500 , 1);
        if( !$fp ) {
            echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
            return;
        }
    }

    public function onConnect( $cli) {
        $class  = 'Member';
        $method = 'userInfoByMid';
        $params = array('mid'=>13392);
        $data   = array('class'=>$class, 'method'=>$method, 'params'=>$params);

        $buffer = JsonProtocol::encode($data);
        $cli->send( $buffer );
    }

    public function onReceive( $cli, $data ) {
        $rs = JsonProtocol::decode($data);
        echo '<pre>';
        print_r($rs);
        echo '</pre>';
    }

    public function onClose( $cli) {
        echo "Client close connection\n";
    }

    public function onError() {

    }

    public function send($data) {
        $this->client->send( $data );
    }

    public function isConnected() {
        return $this->client->isConnected();
    }
}
$cli = new Client();
$cli->connect();
