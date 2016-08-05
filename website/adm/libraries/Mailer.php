<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**=============================
 *  PHP Mailer CI操作类
 * @author      linzequan <lowkey361@gmail.com>
 * @copyright   Copyright (c) 2014 Forlia Team
 * @link        http://www.playsina.com
 * @since       Version 0.1
 *==============================*/
// 导入必要的PHPMailer类
require_once APPPATH.'third_party/PHPMailer/PHPMailerAutoload.php';

class Mailer {

    private $PHPMailer;         // PHPMailer Handler
    private $wordwrap;

    private $mailtemp;          // 邮件的存放模版

    public function __construct() {
        $CI  = & get_instance();
        $CIConfig = $CI->config;
        $this->PHPMailer = new PHPMailer();
         // 获取配置信息
        $CIConfig->load('email');
        $this->PHPMailer->Host      = $CIConfig->item('mailhost');
        $this->PHPMailer->Username  = $CIConfig->item('mailuser');
        $this->PHPMailer->Password  = $CIConfig->item('mailpswd');
        $this->PHPMailer->From      = $CIConfig->item('mailfrom');
        $this->PHPMailer->FromName  = $CIConfig->item('mailfromname');
        $maildebug                  = $CIConfig->item('maildebug');
        $this->wordwrap             = $CIConfig->item('wordwrap');
        $smtpsecure                 = $CIConfig->item('smtpsecure');
        $charset                    = $CIConfig->item('charset');
        $smtpauth                   = $CIConfig->item('smtpauth');
        $port                       = $CIConfig->item('mailport');
        $this->mailtemp             = $CIConfig->item('mailtemp');
        $this->Encoding             = $CIConfig->item('encoding');

        if($CIConfig->item('issmtp') == true) {
            $this->PHPMailer->isSMTP();
        }
        if(!empty($port) && is_numeric($port)) {
            $this->PHPMailer->Port = $port;
        }
        if($smtpauth) {
            $this->PHPMailer->SMTPAuth = $smtpauth;
        }
        if(!empty($charset)) {
            $this->PHPMailer->CharSet = $charset;
        }
        if(!empty($smtpsecure)) {
            $this->PHPMailer->SMTPSecure = $smtpsecure;
        }
        if(!empty($maildebug)) {
            $this->PHPMailer->SMTPDebug =  $maildebug;
        }
        if(!empty($this->wordwrap)) {
            $this->PHPMailer->WordWrap =  $this->wordwrap;
        }
        if(!empty($this->Encoding)) {
            $this->PHPMailer->Encoding = $this->Encoding;
        }

        $this->PHPMailer->isHTML(true);
    }


    public function setWordwrap($wordwrap) {
        if(is_numeric($wordwrap)) {
            $this->PHPMailer->WordWrap = $wordwrap;
        }
    }


    public function isHTML(boolean $html) {
         $this->PHPMailer->isHTML($html);
    }


    /**
     * 设置调试的级别
     *
     * @param int $level        调试的级别.
     *
     * @access public
     */
    public function setDebug($level) {
        if(is_numeric($level)) {
            $this->PHPMailer->SMTPDebug = $level;
        }
    }


    /**
     * 添加收件人地址
     * @param mixed  $address       收件人邮件地址.
     * @param string $name          收件人名称.
     *
     * @access public
     */
    public function addAddress($address, $name = '') {
        $this->PHPMailer->addAddress($address, $name);
    }


    /**
     * 添加多个收件人地址
     *
     * @param array $addresslist        收件人列表.
     *
     * @access public
     * @return mixed     Value.
     */
    public function addAddressByArr($addresslist) {
        if(is_array($addresslist)) {
            foreach($addresslist as $address) {
                $this->PHPMailer->addAddress($address);
            }
        }
    }


    /**
     * 手动邮件的标题和内容
     *
     * @param string $subject        主题.
     * @param string $body           主体内容.
     * @param string $altbody        altbody.
     *
     * @access public
     */
    public function MakeMailInfo($subject, $body, $altbody) {
        $this->PHPMailer->Subject = "=?utf-8?B?".base64_encode($subject)."?=";
        $this->PHPMailer->Body    = $body;
        $this->PHPMailer->AltBody = $altbody;
    }


    /**
     * 通过模版设置邮件的内容
     *
     * @param mixed $template       模版名称.
     * @param mixed $title          邮件主题.
     * @param mixed $data           模版数据.
     * @param mixed $language       模版语言类型.
     *
     * @access public
     * @return mixed     Value.
     */
    public function setMailTemplate($template, $title, $data = null, $language = null) {
        if(empty($template)) {
            return false;
        }
        $this->PHPMailer->Subject = $title;
        $extension = pathinfo($template, PATHINFO_EXTENSION);
        $template_file = rtrim(str_replace('\\','/',$this->mailtemp),'/').'/'.$template;
        if(empty($extension)) {
            $template_file .= '.php';
        }
        if(is_array($data)) {
            extract($data);
        }
        // 邮件内容获取
        ob_start();
        require_once $template_file;
        $body = ob_get_contents();
        ob_end_clean();
        $this->PHPMailer->Body = $body;
        if(isset($altbody)) {
            $this->PHPMailer->AltBody = $AltBody;
        }
    }


    public function send() {
        return @$this->PHPMailer->send();
    }

}