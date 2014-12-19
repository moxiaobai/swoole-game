<?php

require_once __DIR__ . '/Lib/Autoloader.php';

class Server {
    private $http;

    public function __construct() {
        $this->http = new swoole_http_server("0.0.0.0", 8080);
        $this->http->set(
            array(
                'worker_num' => 16,
                'daemonize' => false,
                'max_request' => 10000,
                'dispatch_mode' => 1
            )
        );
        $this->http->on('Start', array($this, 'onStart'));
        $this->http->on('request' , array( $this , 'onRequest'));
        $this->http->on('message' , array( $this , 'onMessage'));
        $this->http->start();
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onRequest($request, $response) {
        if( isset($request->server) ) {
            $server = $request->server;

            echo $server['request_uri'] . PHP_EOL;
        }

        if( isset($request->header) ) {
            $header = $request->header;
        }

        if( isset($request->get) ) {
            $get = $request->get;
            print_r($get);
        }

        if( isset($request->post) ) {
            $post = $request->post;
        }

        $response->end(1);
    }

    public function onMessage($request, $response) {
        //echo $request->message;
        //$response->message(json_encode(array("data1", "data2")));
    }
}
new Server();