<?php

namespace Config;

/**
 * Game Server Config
 * User: renxiaogang
 * Date: 2014/12/19
 * Time: 10:33
 */

class Server {

    public static $game = array(
        //Worker进程数
        'worker_num' => 4,
        //task worker进程数
        'task_worker_num' => 8,
        //设置程序进入后台作为守护进程运行
        'daemonize' => false,
        //每个worker进程允许处理的最大任务数
        'max_request' => 10000,
        //'heartbeat_check_interval' => 60,
        'dispatch_mode' => 2,
        'debug_mode'=> 1
    );

    public static $chat = array(
        //Worker进程数
        'worker_num' => 4,
        //设置程序进入后台作为守护进程运行
        'daemonize' => false,
        //每个worker进程允许处理的最大任务数
        'max_request' => 10000,
        //'heartbeat_check_interval' => 60,
        'dispatch_mode' => 2,
        'debug_mode'=> 1
    );
}