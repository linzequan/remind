<?php
/**
 * 用户权限管理控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class pms extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sys/pms_model', 'def_model');
    }


    public function index() {
        $this->load->view('sys/pms');
    }


    public function get() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'user-search':
                $this->load->model('sys/user_model');
                $params = $this->input->post('rs');
                $order  = get_datagrid_order();
                $page   = get_datagrid_page();
                $result = $this->user_model->search($params, $order, $page);
                break;
            case 'menu-search':
                $this->load->model('sys/menu_model');
                $result = $this->menu_model->search();
                break;
            case 'search':
                $user_id = $this->input->post('user_id');
                $result = $this->def_model->search($user_id);
                break;
        }
        echo json_encode($result);
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'update':
                $user_id    = $this->input->post('user_id');
                $menu_pms   = $this->input->post('menu_pms');
                $result = $this->def_model->update($user_id, $menu_pms);
                break;
        }
        $this->output_result($result);

    }
}