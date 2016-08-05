<?php
/**
 * 系统邮件通知模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class email_sys_queue_model extends MY_Model{

    private $table = 'email_sys_queue';

    public function __construct() {
        parent::__construct();
    }


    /**
     * @desc 获取一个的邮件队列节点
     * @access public
     **/
    public function get_email($where=array(), $order_by=array()) {
        if(is_array($order_by)) {
            foreach($order_by as $key=>$value) {
                $this->db->order_by($key,$value);
            }
        } else {
            return false;
        }
        $query = $this->db->where($where)->get($this->table);
        return $query->row_array();
    }


    /**
     * @desc 删除操作
     * @param int $id
     * @access public
     **/
    public function delete($id) {
        $result = $this->db->delete($this->table, array('id'=>$id));
        return $result;
    }


    /**
     * @desc 添加系统提醒任务
     * @param string $content 邮件内容
     * @param int $priority 优先级，默认5，数字越小优先级越高
     **/
    public function add($content='', $priority=5) {
        if($content=='') {
            return false;
        }
        $data = array(
            'content'       => $content
            ,'priority'     => $priority
            ,'status'       => 0
            ,'create_time'  => time()
        );
        $this->db->insert($this->table, $data);
        return true;
    }

}