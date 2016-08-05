<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['protocol']     = 'sendmail';
$config['mailpath']     = '/usr/sbin/sendmail';
$config['charset']      = 'UTF-8';
$config['wordwrap']     = TRUE;
// PHPMailer Config
$config['smtpauth']     = TRUE;
$config['issmtp']       = TRUE;
$config['mailhost']     = 'smtp.163.com';
$config['mailuser']     = 'mykakaday@163.com';
$config['mailpswd']     = 'iamlion';
$config['mailport']     = 25;
$config['smtpsecure']   = ''; // or tls;
$config['mailfrom']     = 'mykakaday@163.com';
$config['mailfromname'] = 'remind';
$config['wordwrap']     = 0;
$config['maildebug']    = 0;
// $config['mailtemp']     = FCPATH.APPPATH.'views/email';
$config['error_log']    = 'adm/logs/email/';
$config['charset']      = 'utf-8';
$config['encoding']     = 'base64';

// 系统信息接收者
$config['sys_receive']  = array('361789273@qq.com');
