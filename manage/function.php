<?php
/**
 * Created by PhpStorm.
 * User: Zhaoyu
 * Date: 2016/6/16
 * Time: 16:56
 */

die(print_r($_POST));

switch ($_POST){

    /*
     * 单个查询的情况处理
     * */
    case '_search_info':
        $search_info = $_POST['_search_info'];
        die($search_info);
        break;

    default:
        break;
}

?>