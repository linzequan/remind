<?php
/**
 * 应用基础控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }


    /**
     * 标准化请求输出
     *
     * @param boolean $success 操作是否成功
     * @param integer $error 操作错误编码
     * @param mixed $data 操作错误提示或结果数据输出
     * @return string {success:false, error:0, data:''}
     */
    public function output_result($success=false, $error=0, $data='') {
        if(is_array($success)==true) {
            echo json_encode($success);
        } else {
            echo json_encode(array('success'=>$success, 'error'=>$error, 'data'=>$data));
        }
        exit;
    }


    public function get_request($key, $default='') {
        return get_value($_REQUEST, $key, $default);
    }
}