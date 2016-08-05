<?php
/**
 * 应用基础模型（初始化分库访问）
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class MY_Model extends CI_Model {
    protected static $db_default;
    protected static $db_slaves;
    protected static $db_config;
    protected static $db_count;


    /**
     * 初始化分库访问静态变量
     */
    public function __construct() {
        parent::__construct();
    }


    /**
     * 标准化返回结果
     *
     * @param string $success
     * @param number $error
     * @param mixed $data
     * @return string array(success=>false, error=>0, data=>'')
     */
    protected function create_result($success=false, $error=0, $data='') {
        return array('success'=>$success, 'error'=>$error, 'data'=>$data);
    }


    /**
     * 获取会员分库
     *
     * @param $mem_id 会员ID
     * @return 返回会员ID所在的分库
     */
    protected function mdb($mem_id) {
        return $this->_get_db_slave($mem_id);
    }


    /**
     * 获取所有会员分库
     *
     * @return 返回所有会员分库
     */
    protected function mdbs() {
        return $this->_get_db_slaves();
    }


    /**
     * 获取活动分库
     *
     * @param int $event_id 活动ID
     * @return 返回活动ID所在分库
     */
    protected function edb($event_id) {
        return $this->_get_db_slave($event_id);
    }


    /**
     * 获取所有活动分库
     *
     * @return 返回所有活动分库
     */
    protected function edbs() {
        return $this->_get_db_slaves();
    }


    private function _get_db_slaves() {
        $this->_init_db();
        for($i=0; $i<self::$db_count; $i++) {
            if(isset(self::$db_slaves[$i])==false) {
                self::$db_slaves[$i]=$this->load->database(self::$db_config[$i], true);
            }
        }
        return self::$db_slaves;
    }


    private function _get_db_slave($id) {
        $this->_init_db();
        $division = $this->divide($id);
        if(isset(self::$db_slaves[$division])==false) {
            self::$db_slaves[$division]=$this->load->database(self::$db_config[$division], true);
        }
        return self::$db_slaves[$division];
    }


    protected function divide($id) {
        if(self::$db_count<=1) {
            return 0;
        } else {
            return ($id % (self::$db_count-1))+1;
        }
    }


    private function _init_db() {
        if(empty(self::$db_config)==true) {
            $this->config->load('database', true);
            self::$db_config    = $this->config->item('db', 'database');
            self::$db_default   = $this->load->database(self::$db_config[0], true);
            self::$db_slaves    = array();
            self::$db_count     = sizeof(self::$db_config);
        }
    }


    public function __get($name) {
        if($name=='db') {
            $this->_init_db();
            return self::$db_default;
        } else {
            return parent::__get($name);
        }
    }
}
