<?php
/**
 * 系统用户管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class user_model extends MY_Model {

    private $table = 'sys_user';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {
        $fields = 'user_id, user_name, true_name, is_admin, create_uname, create_time';
        $where = array(
                    array('is_del', '0'),
                    array('user_name', get_value($params, 'user_name'), 'like'),
                    array('true_name', get_value($params, 'true_name'), 'like'),
                    array('is_admin', get_value($params, 'is_admin')),
        );
        $result = $this->db->get_page($this->table, $fields, $where, $order, $page);
        return $result;
    }


    public function insert($info) {
        $info['pwd'] = $this->password_encode($info['pwd']);
        $query = $this->db->where('user_name', $info['user_name'])->select('user_id')->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0) {
            return $this->create_result(false, 1, '用户账号重复');
        }
        $info['create_uname'] = $this->session->userdata('user_name');
        $this->db->insert($this->table, $info);
        $user_id = $this->db->insert_id();
        return $this->create_result(true, 0, array('user_id'=>$user_id));
    }


    public function update($user_id, $info) {
        $query = $this->db->where(array('user_name'=>$info['user_name'], 'user_id !='=>$user_id))
                        ->select('user_id')
                        ->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0) {
            return $this->create_result(false, 1, '用户账号重复');
        }
        if($info['pwd']!='not-pwd') {
            $info['pwd'] = $this->password_encode($info['pwd']);
        } else {
            unset($info['pwd']);
        }
        $this->db->update($this->table, $info, array('user_id'=>$user_id));
        return $this->create_result(true, 0, array('user_id'=>$user_id));
    }


    public function delete($user_id) {
        $info = array('is_del'=>1);
        $this->db->update($this->table, $info, array('user_id'=>$user_id));
        return $this->create_result(true, 0, array('user_id'=>$user_id));
    }


    public function password_encode($pwd) {
        return md5('sata'.$pwd);
    }


    public function check_login($user_name, $pwd) {
        $query = $this->db->select('user_id, user_name, true_name, pwd, is_admin')
                        ->from($this->table)
                        ->where('user_name', $user_name)
                        ->get();
        if($query->num_rows()<=0) {
            return $this->create_result(false, 1, '用户不存在');
        }
        $info = $query->row_array();
        if($info['pwd']!=$this->password_encode($pwd)) {
            return $this->create_result(false, 2, '密码错误');
        }
        return $this->create_result(true, 0, $info);
    }


    public function pwd_update($user_id, $pwd_old, $pwd_new) {
        $query = $this->db->select('user_id')
                        ->where(array('user_id'=>$user_id, 'pwd'=>$this->password_encode($pwd_old)))
                        ->get($this->table);
        if($query->num_rows()<=0) {
            return $this->create_result(false, 1, '密码错误');
        }
        $this->db->update(
                $this->table,
                array('pwd'=>$this->password_encode($pwd_new)),
                array('user_id'=>$user_id));
        return $this->create_result(true, 0, '密码修改成功');
    }


    public function get_userinfo_by_id($user_id) {
        $query = $this->db->get_where($this->table, array('user_id'=>$user_id));
        $result = $query->result_array();
        if(count($result)>0) {
            return $result[0];
        } else {
            return false;
        }
    }


    public function get_list() {
        $fields = 'user_id, user_name, true_name, is_admin, create_uname, create_time';
        $result = $this->db->get_list($this->table, $fields);
        return $result['rows'];
    }
}
