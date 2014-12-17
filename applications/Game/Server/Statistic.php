<?php

/**
 * 数据接口统计
 *
 * User: renxiaogang
 * Date: 2014/12/17
 * Time: 15:00
 */

namespace Server;

use \Lib\Db;

class Statistic {

    public static function report($data) {
        $db   = Db::instance('passport');
        $result = $db->insert('t_statistic')->cols($data)->query();
        return $result;
    }
}

