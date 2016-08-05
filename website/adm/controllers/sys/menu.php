<?php
/**
 * 应用菜单管理控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class menu extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sys/menu_model', 'def_model');
    }


    public function index() {
        $this->load->view('sys/menu');
    }


    public function get(){
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'search':
                $result = $this->def_model->search();
                break;
        }
        echo json_encode($result);
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'insert':
                $info['pid']        = $this->input->post('pid');
                $info['title']      = $this->input->post('title');
                $info['ctrl_name']  = $this->input->post('ctrl_name');
                $info['sort']       = $this->input->post('sort');
                $result = $this->def_model->insert($info);
                break;
            case 'update':
                $menu_id = $this->input->post('menu_id');
                $info['pid']        = $this->input->post('pid');
                $info['title']      = $this->input->post('title');
                $info['ctrl_name']  = $this->input->post('ctrl_name');
                $info['sort']       = $this->input->post('sort');
                $result = $this->def_model->update($menu_id,$info);
                break;
            case 'delete':
                $menu_id = $this->input->post('menu_id');
                $result = $this->def_model->delete($menu_id);
                break;
        }
        $this->output_result($result);
    }
}