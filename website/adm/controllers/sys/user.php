<?php
/**
 * 系统用户管理控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class user extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sys/user_model', 'def_model');
    }


    public function index() {
        $this->load->view('sys/user');
    }


    public function get() {
        $actionxm=$this->get_request('actionxm');
        $result=array();
        switch($actionxm) {
            case 'search':
                $params = $this->input->post('rs');
                $order  = get_datagrid_order();
                $page   = get_datagrid_page();
                $result = $this->def_model->search($params, $order, $page);
                break;
            case 'get_list':
                $result = $this->def_model->get_list();
                break;
        }
        echo json_encode($result);
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'insert':
                $info['user_name'] = $this->input->post('user_name');
                $info['true_name'] = $this->input->post('true_name');
                $info['is_admin'] = $this->input->post('is_admin');
                $info['pwd'] = $this->input->post('pwd');
                $result = $this->def_model->insert($info);
                break;
            case 'update':
                $user_id = $this->input->post('user_id');
                $info['user_name'] = $this->input->post('user_name');
                $info['true_name'] = $this->input->post('true_name');
                $info['is_admin'] = $this->input->post('is_admin');
                $pwd = $this->input->post('pwd');
                if($pwd!='not-pwd') {
                    $info['pwd'] = $pwd;
                } else {
                    $info['pwd'] = 'not-pwd';
                }
                $result = $this->def_model->update($user_id, $info);
                break;
            case 'delete':
                $user_id = $this->input->post('user_id');
                $result = $this->def_model->delete($user_id);
                break;
        }
        $this->output_result($result);
    }
}
