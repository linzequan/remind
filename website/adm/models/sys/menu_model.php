<?php
/**
 * 应用菜单管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class menu_model extends MY_Model {

    private $table='sys_menu';

    public function __construct() {
        parent::__construct();
    }


    public function search() {
        $fields = 'menu_id, pid, title, ctrl_name, sort, create_uname, create_time';
        $query = $this->db->select($fields)->order_by('pid asc, sort asc')->get($this->table);
        $list = $query->result_array();
        $tree = array();
        create_tree_list($list, $tree, 0, 0, array('id_key'=>'menu_id', 'pid_key'=>'pid'));
        return array_values($tree);
    }


    public function insert($info) {
        $query = $this->db->where('ctrl_name', $info['ctrl_name'])->select('menu_id')->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0 && $info['ctrl_name']!='') {
            return $this->create_result(false, 1, '菜单访问控制器重复');
        }
        $info['create_uname'] = $this->session->userdata('user_name');
        $this->db->insert($this->table, $info);
        $user_id = $this->db->insert_id();
        return $this->create_result(true, 0, $info);
    }


    public function update($menu_id,$info) {
        $query = $this->db->where(array('ctrl_name'=>$info['ctrl_name'], 'menu_id !='=>$menu_id))
                        ->select('menu_id')
                        ->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0 && $info['ctrl_name']!='') {
            return $this->create_result(false, 1, '菜单访问控制器重复');
        }
        $this->db->update($this->table, $info, array('menu_id'=>$menu_id));
        return $this->create_result(true, 0, array('menu_id'=>$menu_id));
    }


    public function delete($menu_id) {
        $this->db->delete($this->table, array('menu_id'=>$menu_id));
        return $this->create_result(true, 0, '删除成功');
    }
}
