<?php

/**
 * 用户模型
 *
 * @auther moxiaobai
 * @since  2014/11/13
 */

use \Lib\Store;
use \Lib\Db;


class Member {

    private $_store;
    private $_db;

    private $_result = array(
        'SUCCESS'                  =>  array('code'=>1,    'msg' => 'successful', 'data' => null),
        'NO_DATA_EXIST'            =>  array('code'=>100,  'msg' => 'No data exist', 'data' => null),
        'ERROR_USERNAME'           =>  array('code'=>400,  'msg' => '用户名为空', 'data' => null),
        'ERROR_PASSWORD'           =>  array('code'=>401,  'msg' => '用户密码为空', 'data' => null),
        'ERROR_USERNAME_PASSWORD'  =>  array('code'=>402,  'msg' => '用户或者密码错误', 'data' => null),
    );

    public function __Construct() {
        $this->_store    = Store::instance('game');
        $this->_db       = Db::instance('passport');
    }

    /**
     * 验证用户登录
     *
     * @param  array $params
     * @return mixed
     */
    public function authLogin($username, $password) {
        if(!isset($username)) {
            return $this->_result['ERROR_USERNAME'];
        }

        if(!isset($password)) {
            return $this->_result['ERROR_PASSWORD'];
        }

        $password = md5(md5($password));

        $sql = "SELECT m_id,m_nickname,m_email,m_regtime FROM `t_member`
                WHERE m_nickname='{$username}' and m_password = '{$password}'
                limit 1";
        $result = $this->_db->row($sql);
        if($result) {
            $this->_result['SUCCESS']['data'] = $result;
            return $this->_result['SUCCESS'];
        } else {
            return $this->_result['ERROR_USERNAME_PASSWORD'];
        }
    }

    /**
     * 注册用户
     * @param $username
     * @param $password
     * @return array
     */
    public function authReg($username, $password) {
        $password = md5(md5($password));

        $data = array(
            'm_nickname' => $username,
            'm_password' => $password,
            'm_email'    => 'tt@qq.com',
            'm_ip'       => '2147483647',
            'm_regtime'  => time()
        );
        $result = $this->_db->insert('t_member')->cols($data)->query();
        return $result;
    }

    /**
     * 获取用户信息
     *
     * @param $mid
     * @return mixed
     */
    public function userInfoByMid($mid) {
        $key = "GAME_USER_INFO_{$mid}";

        $userInfo = $this->_store->get($key);
        if($userInfo) {
            $this->_result['SUCCESS']['data'] = $userInfo;
            return $this->_result['SUCCESS'];
        }

        $sql = "SELECT m_id,m_nickname,m_email,m_regtime FROM `t_member`
                WHERE m_id='{$mid}'
                limit 1";
        $result = $this->_db->row($sql);
        if($result) {
            $this->_store->set($key, $result);
        }

        if($result) {
            $this->_result['SUCCESS']['data'] = $result;
            return $this->_result['SUCCESS'];
        } else {
            return $this->_result['NO_DATA_EXIST'];
        }
    }
}