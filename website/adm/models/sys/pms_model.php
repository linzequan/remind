<?php
/**
 * 用户权限管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class pms_model extends MY_Model {

    private $table='sys_user_pms';


    public function __construct() {
        parent::__construct();
    }


    public function search($user_id) {
        $query = $this->db->select('menu_id, pms')->from($this->table)->where('user_id', $user_id)->get();
        return $query->result_array();
    }


    public function update($user_id, $menu_pms) {
        $list=array();
        foreach($menu_pms as $menu_id=>$pms) {
            array_push($list, array('user_id'=>$user_id, 'menu_id'=>$menu_id, 'pms'=>$pms));
        }
        $this->db->delete($this->table, array('user_id'=>$user_id));
        $this->db->insert_batch($this->table, $list);
        $this->create_result(true, 0, '权限设置成功');
    }


    public function get_user_menu_pms($user_id, $is_admin=0) {
        if($is_admin!=1) {
            $sql = 'select B.menu_id,B.pid,B.title,B.ctrl_name,A.pms'
                    .' from sys_user_pms as A'
                    .' left join sys_menu as B'
                    .' on A.menu_id=B.menu_id'
                    .' where A.user_id='.$user_id
                    .' order by pid asc,sort asc';
        } else {
            $sql = "select menu_id,pid,title,ctrl_name,'".str_repeat('1', $this->config->item('pms_len'))."' as pms"
                    .' from sys_menu'
                    .' order by pid asc,sort asc';
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}