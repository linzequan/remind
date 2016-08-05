<?php
/**
 * 系统后台登陆控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class login extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }


    public function index() {
        $referer = $this->get_request('redirect', base_url('/'));
        $this->load->view('login', array('referer'=>$referer));
    }


    public function sign_in() {
        $this->load->model('sys/user_model');
        $user_name  = $this->input->post('user_name');
        $pwd        = $this->input->post('pwd');
        $result = $this->user_model->check_login($user_name, $pwd);
        if($result['success']) {
            $this->load->model('sys/pms_model');
            $user_pms = $this->pms_model->get_user_menu_pms($result['data']['user_id'], $result['data']['is_admin']);
            // var_dump($user_pms);
            $userdata = array(
                'user_id'   =>$result['data']['user_id'],
                'user_name' =>$result['data']['user_name'],
                'true_name' =>$result['data']['true_name'],
                'is_admin'  =>$result['data']['is_admin']
            );
            $this->session->set_userdata($userdata);
            $this->output_result(true, 0, '登录成功');
        } else {
            $this->output_result($result);
        }
    }


    public function sign_out() {
        $this->session->sess_destroy();
        header('Location:'.base_url('/login'));
    }
}
