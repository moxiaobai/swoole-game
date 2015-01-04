<?php
/**
 * 异步Mysql
 *
 * User: renxiaogang
 * Date: 2015/1/4
 * Time: 10:07
 */

if(!function_exists('swoole_get_mysqli_sock')) {
    die("no async_mysql support\n");
}

$db = new mysqli;
$db->connect('127.0.0.1', 'root', '622124', 'crontab');
$db->query("show tables", MYSQLI_ASYNC);

swoole_event_add(swoole_get_mysqli_sock($db), function($__db_sock) {
    global $db;

    var_dump($__db_sock);
    $res = $db->reap_async_query();
    var_dump($res->fetch_all(MYSQLI_ASSOC));
    $db->query("show tables", MYSQLI_ASYNC);

    swoole_event_exit();
});

echo "Finish\n";