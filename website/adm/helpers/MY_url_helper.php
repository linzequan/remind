<?php
/**
 * @author linzequan <lowkey361@gmail.com>
 *
 */
function change_domain($app) {
    if(isset($_SERVER['HTTP_HOST'])==false) {
        return false;
    }
    $http_host = $_SERVER['HTTP_HOST'];
    $sub_domain = substr($http_host, strpos($http_host, '.'));
    return $app . $sub_domain;
}
