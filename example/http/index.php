<?php

/**
 * Created by PhpStorm.
 * User: renxiaogang
 * Date: 2014/12/13
 * Time: 15:17
 */

$http = new swoole_http_server("0.0.0.0", 8501);

$http->on('request', function ($request, $response) {
    echo '<pre>';
    print_r($request);
    echo '</pre>';

    echo '<pre>';
    print_r($response);
    echo '</pre>';

    $html = "<h1>Hello Swoole.</h1>";
    $response->end($html);
});

$http->start();