<?php
/**
 * Created by PhpStorm.
 * User: renxiaogang
 * Date: 2014/12/12
 * Time: 13:48
 */

class Client
{
    private $client;

    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect() {
        try {
            $fp = $this->client->connect("127.0.0.1", 9501 , 1);
        } catch(Exception $e) {
            echo "Error: {$e->getMessage()}[{$e->getCode()}]" . PHP_EOL;
            exit;
        }

        $message = $this->client->recv();
        echo "Get Message From Server:{$message}" . PHP_EOL;

        fwrite(STDOUT, "请输入消息：");
        $msg = trim(fgets(STDIN));
        $this->client->send( $msg );
    }
}

$client = new Client();
$client->connect();