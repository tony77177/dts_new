<?php
/**
 * Created by PhpStorm.
 * 查询操作实现等
 * User: Zhaoyu
 * Date: 2016/7/20
 * Time: 13:25
 */

error_reporting(0);
require_once('config.php');
require_once('function.php');
require_once('../class/common.class.php');
require_once('../libraries/mysqli.class.php');
require_once('../libraries/PHPExcel/PHPExcel.php');
require_once('../libraries/IP/IP.class.php');

ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0)');
ini_set('date.timezone', 'Asia/Shanghai');

/*API地址拼接*/
$api_index = $config['cur_ip_api_cfg'];//获取设置的默认API索引
$api_link = $config['ip_api_info'][$api_index];

/*token设置*/
$token = $config['cur_token'];

/*common class 声明*/
$common = new Common($databaseinfo);

switch ($_POST['flag']) {

    /*
     * 单个查询的情况处理
     * */
    case 'single_search':
        /*POST查询数据获取*/
        $search_info = $_POST['_search_info'];

        //die(date('Y-m-d H:i:s',time()));

        /*API地址拼接*/
        $api_link .= gethostbyname($search_info);

        /*IP地址结果获取*/
        $result = get_result_info($api_index, $api_link, $token);
        //die(var_dump($result));


        //当查询成功时将数据入库
        if ($result != 'fail') {

            $common->update_ip_adderss_info(trim(gethostbyname($search_info)), $result->data[0], $result->data[1], $result->data[2], $result->data[3], $result->data[4], $result->data[5], $result->data[6], $result->data[7], $result->data[8], $result->data[9], $result->data[10], $result->data[11], $result->data[12], date('Y-m-d H:i:s', time()));

            //die(($result->data[0]));

            $tmp_data = '国家：' . $result->data[0];

            $tmp_data .= '<br>省会或直辖市：' . $result->data[1];
            $tmp_data .= '<br>地区或城市：' . $result->data[2];
            $tmp_data .= '<br>纬度：' . $result->data[5];
            $tmp_data .= '<br>经度：' . $result->data[6];
            $result = $tmp_data;

        }


        //$result_info = json_decode(get_page_info($_api_link,$token));

        die($result);

        break;

    /*
     * 批量查询的情况处理
     * */
    case 'multi_search':
//        export_excel();
        die(read_excel($api_index, $api_link));
        break;

    default:
        break;
}


?>