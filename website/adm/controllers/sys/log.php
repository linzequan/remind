<?php
/**
 * 系统日志管理控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class log extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sys/log_model', 'def_model');
    }


    public function index() {
        $this->load->view('sys/log');
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
        }
        echo json_encode($result);
    }

}