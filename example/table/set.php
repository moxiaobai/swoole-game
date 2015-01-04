<?php
/**
 * Created by PhpStorm.
 * User: renxiaogang
 * Date: 2014/12/31
 * Time: 15:44
 */


$worker = new swoole_process('child1', false, false);
$worker->start();


//child
function child1($worker){
    echo 111;
}