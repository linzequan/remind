<?php
/**
 * 系统日志管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class log_model extends MY_Model {

    private $table = 'sys_log';
    private $fields = 'time, user_id, query_sql';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {
        $where = array();
        if(count($order)==0) {
            $order[] = ' time desc ';
        }
        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            if($userinfo=$CI->user_model->get_userinfo_by_id($v['user_id'])) {
                $datas['rows'][$k]['user_name'] = $userinfo['user_name'];
            } else {
                $datas['rows'][$k]['user_name'] = '';
            }
            $datas['rows'][$k]['time'] = date('Y-m-d H:i:s', $v['time']);
        }
        return $datas;
    }


    public function insert($info) {

        $this->db->insert($this->table, $info);
        $log_id = $this->db->insert_id();
        return $this->create_result(true, 0, array('log_id'=>$log_id));

    }
}