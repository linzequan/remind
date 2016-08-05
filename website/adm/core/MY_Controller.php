<?php
/**
 * 应用基础控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class MY_Controller extends CI_Controller {

    public $ctrl_name='';
    public $ctrl_pms='';

    public function __construct($file_fullname='', $opt_alias=array()) {
        parent::__construct();
        // 作用于所有需要登录后打开的控制器
        if($file_fullname!='') {
            // 登陆状态控制
            $user_id = $this->session->userdata('user_id');
            if(empty($user_id)) {
                if($this->input->is_ajax_request()==true) {
                    $this->output_result(false, 1, '尚未登录');
                } else {
                    $referer = urlencode($this->input->server('REQUEST_URI'));
                    header('Location:' . base_url('login?redirect=' . $referer));
                }
                exit;
            }

            // 获取控制器名称
            $pos1 = strpos($file_fullname,'controllers') + 12;
            $pos2 = strpos($file_fullname,'.php') - $pos1;
            $this->ctrl_name = str_replace("\\", '/', substr($file_fullname, $pos1, $pos2));
            // 权限控制
            if($this->ctrl_name=='home' || $this->session->userdata('is_admin')=='1') {
                $this->ctrl_pms = str_repeat('1', $this->config->item('pms_len'));
            } else {
                $this->load->model('sys/pms_model');
                $user_id = $this->session->userdata('user_id');
                $is_admin = $this->session->userdata('is_admin');
                $user_pms = $this->pms_model->get_user_menu_pms($user_id, $is_admin);
                foreach ($user_pms as $item) {
                    if($item['ctrl_name']==$this->ctrl_name) {
                        $this->ctrl_pms = $item['pms'];
                        break;
                    }
                }
                if($this->ctrl_pms=='') {
                    $this->ctrl_pms = str_repeat('0', $this->config->item('pms_len'));
                }
            }

            // 把当前权限数据赋值给全局变量
            $GLOBALS['ctrl_pms']    = $this->ctrl_pms;
            $GLOBALS['pms_opts']    = $this->config->item('pms_opts');
            $GLOBALS['opt_alias']   = array_merge($opt_alias, array('search'=> 'select'));
            // 权限控制
            $this->check_pms($this->get_request('actionxm', 'select'));
        }
    }

    /**
     * 标准化返回结果
     *
     * @param string $success
     * @param number $error
     * @param mixed $data
     * @return string array(success=>false,error=>0,data=>'')
     */
    protected function create_result($success=false, $error=0, $data='') {
        return array('success'=>$success, 'error'=>$error, 'data'=>$data);
        exit;
    }


    protected function check_pms($opt) {
        global $pms_opts,$opt_alias;
        $result = false;
        if(isset($pms_opts[$opt])==false) {
            if(isset($opt_alias[$opt])==true) {
                $opt = $opt_alias[$opt];
            }
        }
        if(check_pms($opt)==false) {
            if($this->input->is_ajax_request()==true) {
                $this->output_result(false, 0, '无权限操作');
            } else {
                die($this->load->view('errors/not-permission', array(), true));
            }
        }
    }


    /**
     * 标准化请求输出
     *
     * @param boolean $success 操作是否成功
     * @param integer $error 操作错误编码
     * @param mixed $data 操作错误提示或结果数据输出
     * @return string {success:false,error:0,data:''}
     */
    public function output_result($success=false, $error=0, $data='') {
        if(is_array($success)==true) {
            echo json_encode($success);
        } else {
            echo json_encode(array('success'=>$success, 'error'=>$error, 'data'=>$data));
        }
        exit();
    }


    /**
     * 标准化datagrid列表数据输出
     *
     * @param integer $total
     * @param array $rows
     * @return string {total:0,rows:{[id:1,name:''],[id:2,name:'']}}
     */
    public function output_list($total,$rows) {
        echo json_encode(create_datagrid_data($total, $rows));
    }


    public function get_request($key='', $default='') {
        if($key!='') {
            return get_value($_REQUEST, $key, $default);
        } else {
            return $_REQUEST;
        }
    }

}
