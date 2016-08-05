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
    public function __construct(){
        parent::__construct();
    }


    /**
     * 标准化返回结果
     *
     * @param string $success
     * @param number $error
     * @param mixed $data
     * @return string array(success=>false,error=>0,data=>'')
     */
    protected function create_result($success=false, $error=0, $data='') {
        return array('success'=>$success, 'error'=>$error, 'data'=>$data);
        exit;
    }


    /**
     * 数据集与数据库表left join
     *
     * @param array $list
     * @param string $table
     * @param string $fields
     * @param string $join_key
     * @return array
     */
    protected function list_join_query($list, $table, $fields, $join_key='') {
        if(count($list)<=0) {
            return $list;
        }
        if($join_key=='') {
            if(strpos($table,'member')===0 || strpos($table,'sso_member')===0) {
                $join_key = 'mem_id';
            } elseif(strpos($table, 'event')===0 || strpos($table, 'sso_event')===0) {
                $join_key = 'event_id';
            } else {
                return $list;
            }
        }
        if(strpos($fields,$join_key)===false) {
            $fields = $join_key.','.$fields;
        }
        // 是否链接碎片库
        $sd = false;
        if(strpos($table, 'member')===0 || strpos($table, 'event')===0) {
            $sd = true;
        }
        // 获取连接ID
        $ids = array();
        foreach($list as $item) {
            if(!in_array($item[$join_key], $ids)) {
                array_push($ids, $item[$join_key]);
            }
        }
        $result_list = array();
        // 和主库表合并
        if($sd==false) {
            $query = $this->db->select($fields)
                            ->where_in($join_key, $ids)
                            ->get($table);
            if($query->num_rows()>0) {
                $result_list = $query->result_array();
            }
        }else{
            $db_keys = array();
            foreach($ids as $id) {
                $index = $this->divide($id);
                if(isset($db_keys[$index])==false) {
                    $db_keys[$index] = array();
                }
                array_push($db_keys[$index], $id);
            }
            $method = 'mdb';
            if($join_key=='event_id') {
                $method = 'edb';
            }
            foreach($db_keys as $key=>$val) {
                if($join_key=='mem_id') {
                    $query = $this->mdb($key)->select($fields)
                                             ->where_in($join_key, $val)
                                             ->get($table);
                }else{
                    $query = $this->edb($key)->select($fields)
                                             ->where_in($join_key, $val)
                                             ->get($table);
                }
                if($query->num_rows()>0) {
                    $result_list = array_merge($result_list, $query->result_array());
                }
                $query->free_result();
            }
        }
        $list = array_join($list, $result_list, $join_key, $fields);
        return $list;
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


    public function save_log($sql) {
        $this->load->model('sys/log_model', 'log_model');
        $CI = &get_instance();
        $log_id = $CI->log_model->insert(array('query_sql'=>$sql, 'user_id'=>$this->session->userdata('user_id'), 'time'=>time()));
        return $log_id;
    }


    public function combine_where($where) {
        $arr=array();
        foreach ($where as $item){
            $field_name =$item[0];
            $field_value=$item[1];
            $option_type=isset($item[2])?$item[2]:'=';
            switch($option_type){
                case 'like':
                    array_push($arr, $field_name." like '%".$this->db->escape_like_str($field_value)."%'");
                    break;
                case 'like_r':
                    array_push($arr, $field_name." like '%".$this->db->escape_like_str($field_value)."'");
                    break;
                case 'like_l':
                    array_push($arr, $field_name." like '".$this->db->escape_like_str($field_value)."%'");
                    break;
                case 'in':
                case 'not in':
                    array_push($arr, $field_name.' '.$option_type.'('.$this->db->escape_like_str($field_value).")");
                    break;
                default:
                    array_push($arr, $field_name.' '.$option_type.' '.$this->db->escape($field_value));
            }
        }
        $str = implode(' and ',$arr);
        return $str;
    }
}
