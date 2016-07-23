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
require_once('../libraries/IP/IP.class.php');
require_once('../libraries/PHPExcel/PHPExcel.php');

ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0)');
ini_set('date.timezone', 'Asia/Shanghai');

/*API地址拼接*/
$api_index = $config['cur_ip_api_cfg'];//获取设置的默认API索引
$api_link = $config['ip_api_info'][$api_index];

/*token设置*/
$token = $config['cur_token'];

/*common class 声明*/
$common = new Common($databaseinfo);

switch ($_GET['flag']) {

    /*
     * 单个查询的情况处理
     * */
    case 'location_search':
        /*POST查询数据获取*/
        $search_info = $_GET['_search_info'];//获取查询内容

        //die(date('Y-m-d H:i:s',time()));
        $multi_flag = $_GET['multi'];//是否为批量查询标志，1：是批量查询；其他：非批量查询

        /*批量查询操作，组装成批量的API请求并生成对应的excel文档*/
        if ($multi_flag) {
//            die(var_dump($search_info));
            $result_arr = array();//结果数组
            $search_info = explode(',',$search_info);

            for ($i = 0; $i < count($search_info); $i++) {
                $result_arr[$i] = get_location_info($api_index, $api_link . gethostbyname($search_info[$i]), $token, $search_info[$i], $databaseinfo);
                if ($result_arr[$i]->ret == 'ok') {//查询成功时才写库
                    $common->update_ip_adderss_info(gethostbyname($search_info[$i]), $result_arr[$i]->data[0], $result_arr[$i]->data[1], $result_arr[$i]->data[2], $result_arr[$i]->data[3], $result_arr[$i]->data[4], $result_arr[$i]->data[5], $result_arr[$i]->data[6], $result_arr[$i]->data[7], $result_arr[$i]->data[8], $result_arr[$i]->data[9], $result_arr[$i]->data[10], $result_arr[$i]->data[11], $result_arr[$i]->data[12], date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                }
            }

            die(export_excel($search_info, $result_arr));
//            die(var_dump($result_arr));
        }

        /*API地址拼接*/
        $api_link .= gethostbyname($search_info);

        /*IP地址结果获取*/
        $result = get_location_info($api_index, $api_link, $token, $search_info, $databaseinfo);
        //die(var_dump($result));



        //当查询成功时将数据入库
        if ($result != 'fail') {

            $common->update_ip_adderss_info(trim(gethostbyname($search_info)), $result->data[0], $result->data[1], $result->data[2], $result->data[3], $result->data[4], $result->data[5], $result->data[6], $result->data[7], $result->data[8], $result->data[9], $result->data[10], $result->data[11], $result->data[12], date('Y-m-d H:i:s', time()), $curr_interval_update_time);

            //die(($result->data[0]));

            $tmp_data = '国家：' . (($result->data[0] != '') ? $result->data[0] : 'N/A');
            $tmp_data .= '<br>省会或直辖市：' . (($result->data[1] != '') ? $result->data[1] : 'N/A');
            $tmp_data .= '<br>地区或城市：' . (($result->data[2] != '') ? $result->data[2] : 'N/A');
            $tmp_data .= '<br>学校或单位：' . (($result->data[3] != '') ? $result->data[3] : 'N/A');
            $tmp_data .= '<br>运营商：' . (($result->data[4] != '') ? $result->data[4] : 'N/A');
            $tmp_data .= '<br>纬度：' . (($result->data[5] != '') ? $result->data[5] : 'N/A');
            $tmp_data .= '<br>经度：' . (($result->data[6] != '') ? $result->data[6] : 'N/A');
            $tmp_data .= '<br>时区一：' . (($result->data[7] != '') ? $result->data[7] : 'N/A');
            $tmp_data .= '<br>时区二：' . (($result->data[8] != '') ? $result->data[8] : 'N/A');
            $tmp_data .= '<br>中国行政区划代码：' . (($result->data[9] != '') ? $result->data[9] : 'N/A');
            $tmp_data .= '<br>国际电话代码：' . (($result->data[10] != '') ? $result->data[10] : 'N/A');
            $tmp_data .= '<br>国家二位代码：' . (($result->data[11] != '') ? $result->data[11] : 'N/A');
            $tmp_data .= '<br>世界大洲代码：' . (($result->data[12] != '') ? $result->data[12] : 'N/A');
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
        //
        die(export_excel($api_index, $api_link));
        break;

    default:
        break;
}


?>