<?php
/**
 * @author linzequan <lowkey361@gmail.com>
 *
 */
function check_pms($opt) {
    $pms_opts = $GLOBALS['pms_opts'];
    $ctrl_pms = $GLOBALS['ctrl_pms'];
    if(isset($pms_opts[$opt])==false) {
        return false;
    }
    $index = $pms_opts[$opt];
    if($ctrl_pms[$index]=='1') {
        return true;
    }else{
        return false;
    }
}


function get_value(&$arr, $key, $default='') {
    if(isset($arr[$key])) {
        return $arr[$key];
    }else{
        return $default;
    }
}


function create_datagrid_data($total, $rows) {
    return array('total'=>$total, 'rows'=>$rows);
}


function get_datagrid_page() {
    $index = get_value($_POST, 'page', 1);
    $size = get_value($_POST, 'rows', 50);
    if($index<=0) {
        $index = 1;
    }
    return array('index'=>$index, 'size'=>$size);
}


function get_datagrid_order() {
    $sort = get_value($_POST, 'sort', '');
    $order = get_value($_POST, 'order', '');
    if($sort=='') {
        return array();
    } else {
        $result = array();
        $arr_sort = explode(',', $sort);
        $arr_order = explode(',', $order);
        foreach ($arr_sort as $key=>$val) {
            array_push($result, $arr_sort[$key] . ' ' . $arr_order[$key]);
        }
        return $result;
    }
}


function create_tree_list(&$input, &$output, $pid=0, $level=0, $config=array('id_key'=>'id', 'pid_key'=>'pid')) {
    $id_key = $config['id_key'];
    $pid_key = $config['pid_key'];
    $num = count($input);
    for($i=0; $i<$num; $i++) {
        if($input[$i][$pid_key]==$pid) {
            $input[$i]['level'] = $level;
            $input[$i]['is_leaf'] = true;
            $output[$input[$i][$id_key]] = $input[$i];
            if($pid>0) {
                $output[$pid]['is_leaf'] = false;
            }
            create_tree_list($input, $output, $input[$i][$id_key], ($level+1), $config);
        }
    }
}


/**
 * 根据过滤$info中的索引值，保留$fields制定的索引值
 */
function filter_fields($fields, $filter) {
    $filters = explode(',', $filter);
    $arr = explode(',', $fields);
    $rul = array();
    foreach ($arr as $val) {
        if(in_array($val, $filters)) {
            array_push($rul, $val);
        }
    }
    return implode(',', $rul);
}


/**
 * 根据过滤提供的字段列表$fields，自动过滤掉输入数据库的字段值
 */
function filter_data($rows, $fields) {
    $result = array();
    $arr_fields = explode(',', $fields);
    if(isset($rows[0])==false) {
        foreach ($rows as $key=>$val) {
            if(in_array($key,$arr_fields)==true) {
                $result[$key]=$val;
            }
        }
    } else {
        $row = array();
        foreach ($rows as $item) {
            foreach ($item as $key=>$val) {
                if(in_array($key,$arr_fields)==true) {
                    $row[$key] = $val;
                }
            }
            array_push($result, $row);
        }
    }
    return $result;
}


function fields2array($fields) {
    $arr = explode(',', $fields);
    $result = array();
    foreach ($arr as $key) {
        $result[trim($key, ' ')] = '';
    }
    return $result;
}


/**
 * 根据相同键值(join_key)合并两个二维数组$list1,$list2
 *
 * @param array $list1
 * @param array $list2
 * @param string $join_key
 * @return array
 */
function array_join($list1, $list2, $join_key, $right_fields='') {
    $def_item = fields2array($right_fields);
    foreach ($list1 as &$item1) {
        $val = $item1[$join_key];
        $exist = false;
        foreach ($list2 as $item2) {
            if($val==$item2[$join_key]) {
                $item1 = array_merge($item1, $item2);
                $exist = true;
                break;
            }
        }
        if($exist==false) {
            $item1=array_merge($def_item, $item1);
        }
    }
    return $list1;
}


/**
 * @desc 发送系统邮件
 * @param string $content 邮件内容
 * @param int $priority 优先级，默认5，数字越小优先级越高
 * @return boolean
 **/
function add_sys_email($content='', $priority=5) {
    $CI = & get_instance();
    $CI->load->model('sys/email_sys_queue_model', 'email_sys_queue_model');
    if($content=='') {
        return false;
    }
    if($CI->email_sys_queue_model->add($content, $priority)) {
        return true;
    } else {
        return false;
    }
}


/**
 * @desc 字符串GBK转码为UTF-8，数字转换为数字
 **/
function gbk2utf8($s) {
    if(is_numeric($s)) {
        return intval($s);
    } else {
        return iconv('GBK', 'UTF-8', $s);
    }
}


/**
 * @desc 批量处理gbk->utf-8
 **/
function icon_to_utf8($s) {
  if(is_array($s)) {
    foreach($s as $key => $val) {
      $s[$key] = icon_to_utf8($val);
    }
  } else {
      $s = gbk2utf8($s);
  }
  return $s;
}


/**
 * @desc 转换省份
 */
function trans_province($v) {
    $p_array = array(
        '0'     => '未知',
        '1'     => '山东',
        '2'     => '贵州',
        '3'     => '江西',
        '4'     => '重庆',
        '5'     => '内蒙古',
        '6'     => '湖北',
        '7'     => '湖南',
        '8'     => '吉林',
        '9'     => '福建',
        '10'    => '上海',
        '11'    => '北京',
        '12'    => '广西',
        '13'    => '广东',
        '14'    => '四川',
        '15'    => '云南',
        '16'    => '青海',
        '17'    => '甘肃',
        '18'    => '河北',
        '19'    => '台湾',
        '20'    => '浙江',
        '21'    => '江苏',
        '22'    => '黑龙江',
        '23'    => '辽宁',
        '24'    => '天津',
        '25'    => '宁夏',
        '26'    => '安徽',
        '27'    => '山西',
        '28'    => '海南',
        '29'    => '河南',
        '30'    => '陕西',
        '31'    => '新疆',
        '32'    => '澳门',
        '33'    => '香港',
        '34'    => '西藏'
    );
    if($p = $p_array[$v]) {
        return $p;
    } else {
        return $v;
    }
}


/**
 * @desc 转换省份id
 */
function trans_province_id($v) {
    $p_array = array(
        '未知'    => '0',
        '山东'    => '1',
        '贵州'    => '2',
        '江西'    => '3',
        '重庆'    => '4',
        '内蒙古'  => '5',
        '湖北'    => '6',
        '湖南'    => '7',
        '吉林'    => '8',
        '福建'    => '9',
        '上海'    => '10',
        '北京'    => '11',
        '广西'    => '12',
        '广东'    => '13',
        '四川'    => '14',
        '云南'    => '15',
        '青海'    => '16',
        '甘肃'    => '17',
        '河北'    => '18',
        '台湾'    => '19',
        '浙江'    => '20',
        '江苏'    => '21',
        '黑龙江'  => '22',
        '辽宁'    => '23',
        '天津'    => '24',
        '宁夏'    => '25',
        '安徽'    => '26',
        '山西'    => '27',
        '海南'    => '28',
        '河南'    => '29',
        '陕西'    => '30',
        '新疆'    => '31',
        '澳门'    => '32',
        '香港'    => '33',
        '西藏'    => '34'
    );
    if($p = $p_array[$v]) {
        return $p;
    } else {
        return $v;
    }
}


/**
 * 返回省份信息
 * @return array 省份信息
 */
function get_province() {
    return array(
        '1'     => '山东',
        '2'     => '贵州',
        '3'     => '江西',
        '4'     => '重庆',
        '5'     => '内蒙古',
        '6'     => '湖北',
        '7'     => '湖南',
        '8'     => '吉林',
        '9'     => '福建',
        '10'    => '上海',
        '11'    => '北京',
        '12'    => '广西',
        '13'    => '广东',
        '14'    => '四川',
        '15'    => '云南',
        '16'    => '青海',
        '17'    => '甘肃',
        '18'    => '河北',
        '19'    => '台湾',
        '20'    => '浙江',
        '21'    => '江苏',
        '22'    => '黑龙江',
        '23'    => '辽宁',
        '24'    => '天津',
        '25'    => '宁夏',
        '26'    => '安徽',
        '27'    => '山西',
        '28'    => '海南',
        '29'    => '河南',
        '30'    => '陕西',
        '31'    => '新疆',
        '32'    => '澳门',
        '33'    => '香港',
        '34'    => '西藏'
    );
}


/**
 * 对二维数组按照指定的键值排序
 * @param  [type] $multi_array [description]
 * @param  [type] $sort_key    [description]
 * @param  [type] $sort        [description]
 * @return [type]              [description]
 */
function multi_array_sort($multi_array, $sort_key, $sort=SORT_DESC) {
    $key_array = array();
    if(is_array($multi_array)) {
        foreach($multi_array as $row_array) {
            if(is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return FALSE;
            }
        }
    } else {
        return FALSE;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}


/**
 * 格式化成指定位数的百分比
 * @param  [type] $num   [description]
 * @param  [type] $digit [description]
 * @return [type]        [description]
 */
function get_percent($num, $digit) {
    return sprintf("%.".$digit."f", $num*100) . '%';
}


/**
 * Get all hours between two moments
 * @param  [type] $start  The start moment time stamp
 * @param  [type] $end    The end moment time stamp
 * @param  string $format The return hour format, default 'Y-m-d H'
 * @return [type]         Array include all hours
 */
function get_all_hours($start, $end, $format='Y-m-d H') {
    // 时间戳清理
    $start = strtotime(date('Y-m-d H:00:00', $start));
    $end = strtotime(date('Y-m-d H:00:00', $end));

    $result = array();
    if($start<=$end) {
        // 重复timestamp+1小时（3600），直到大于结束时间
        do {
            // 将timestamp转成iso date输出
            $result[] = date($format, $start);
        } while(($start+=60*60) <= $end);
    }
    return $result;
}


/**
 * Get all dates between two moments
 * @param  [type] $start  The start moment time stamp
 * @param  [type] $end    The end moment time stamp
 * @param  string $format The return date format, default 'Y-m-d'
 * @return [type]         Array include all dates
 */
function get_all_dates($start, $end, $format='Y-m-d') {
    // 时间戳清理
    $start = strtotime(date('Y-m-d', $start));
    $end = strtotime(date('Y-m-d', $end));

    $result = array();
    if($start<=$end) {
        // 重复timestamp+1天（86400），直到大于结束日期
        do {
            // 将timestamp转成iso date输出
            $result[] = date($format, $start);
        } while(($start+=24*60*60) <= $end);
    }
    return $result;
}


/**
 * Get all months between two moments
 * @param  [type] $start  The start moment time stamp
 * @param  [type] $end    The end moment time stamp
 * @param  string $format The return month format, default 'Y-m'
 * @return [type]         Array include all months
 */
function get_all_months($start, $end, $format='Y-m') {
    $result = array();
    if($start<=$end) {
        $start_year = date('Y', $start);
        $start_month = date('m', $start);
        $end_year = date('Y', $end);
        $end_month = date('m', $end);

        // 补全不在同一年的月份
        while($start_year<$end_year) {
            if($start_month<=12) {
                $result[] = $start_year . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT);
                $start_month++;
            } else {
                $start_year++;
                $start_month = 1;
            }
        }
        // 补全在同一年的月份
        while($start_month<=$end_month) {
            $result[] = $start_year . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT);
            $start_month++;
        }
        // 返回格式化后的月份
        foreach($result as $k=>$v) {
            $result[$k] = date($format, strtotime($v));
        }
    }
    return $result;
}


/**
 * Get all years between two moments
 * @param  [type] $start  The start moment time stamp
 * @param  [type] $end    THe end moment time stamp
 * @param  string $format The return year format, default 'Y'
 * @return [type]         Array include all years
 */
function get_all_years($start, $end, $format='Y') {
    $result = array();
    if($start<=$end) {
        $start_year = date('Y', $start);
        $end_year = date('Y', $end);

        while($start_year<=$end_year) {
            $result[] = $start_year;
            $start_year++;
        }

        // 返回格式化后的年份
        foreach($result as $k=>$v) {
            $result[$k] = date($format, strtotime($v.'-01-01'));
        }
    }
    return $result;
}


/**
 * 获取所需要年月每天0时的时间戳
 */
function get_timestamp_by_ym($ym) {

    if($ym=='') {
        return false;
    }

    // 当前年月有多少天
    $days = date('t', strtotime($ym));

    $result = array();
    for($i=1; $i<=$days; $i++) {
        if($i<10) {
            $d = '0' . $i;
        } else {
            $d = $i;
        }
        $ymd = $ym . $d;
        $result[] = strtotime($ymd);
    }
    return $result;
}


/**
 * 生成随机数
 * @param  integer $length 随机数长度，默认为3
 * @return [type]          [description]
 */
function myrandom($length=3) {
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    $key = '';
    for($i=0; $i<$length; $i++) {
        $key .= $pattern { mt_rand(0,35) };
    }
    return $key;
}
