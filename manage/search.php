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
        $search_info = $_GET['_search_info'];//获取查询内容
        $multi_flag = $_GET['multi'];//是否为批量查询标志，1：是批量查询；其他：非批量查询

        /*批量查询操作，组装成批量的API请求并生成对应的excel文档*/
        if ($multi_flag) {
            $result_arr = array();//结果数组
            $search_info = explode(',', $search_info);

            for ($i = 0; $i < count($search_info); $i++) {
                $result_arr[$i] = get_location_info($api_index, $api_link . gethostbyname($search_info[$i]), $token, $search_info[$i], $databaseinfo, $curr_interval_update_time);
                if ($result_arr[$i]->ret == 'ok') {//查询成功时才写库
                    $common->update_ip_adderss_info(gethostbyname($search_info[$i]), $result_arr[$i]->data[0], $result_arr[$i]->data[1], $result_arr[$i]->data[2], $result_arr[$i]->data[3], $result_arr[$i]->data[4], $result_arr[$i]->data[5], $result_arr[$i]->data[6], $result_arr[$i]->data[7], $result_arr[$i]->data[8], $result_arr[$i]->data[9], $result_arr[$i]->data[10], $result_arr[$i]->data[11], $result_arr[$i]->data[12], date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                }
            }

            die(export_location_excel($search_info, $result_arr));
        }

        /*API地址拼接*/
        $api_link .= gethostbyname($search_info);

        /*IP地址结果获取*/
        $result = get_location_info($api_index, $api_link, $token, $search_info, $databaseinfo, $curr_interval_update_time);

        //当查询成功时将数据入库
        if ($result != 'fail') {

            //查询成功，进行入库操作
            $common->update_ip_adderss_info(trim(gethostbyname($search_info)), $result->data[0], $result->data[1], $result->data[2], $result->data[3], $result->data[4], $result->data[5], $result->data[6], $result->data[7], $result->data[8], $result->data[9], $result->data[10], $result->data[11], $result->data[12], date('Y-m-d H:i:s', time()), $curr_interval_update_time);

            //组装单个查询前台显示列表
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

            //结果赋值
            $result = $tmp_data;
        }

        die($result);
        break;

    /*
     * 微威胁信息查询
     * */
    case 'threat_search':
        $threat_option = $_GET['_threat_option'];//获取查询类别
        $threat_info = $_GET['_threat_info'];//获取查询内容
        $multi_flag = $_GET['multi'];//是否为批量查询标志，1：是批量查询；其他：非批量查询

        /*批量查询操作，组装成批量的API请求并生成对应的excel文档*/
        if ($multi_flag) {

            $result_arr = array();//结果数组
            $threat_info = explode(',', $threat_info);

            switch ($threat_option) {
                case 'email':
                    for ($i = 0; $i < count($threat_info); $i++) {
                        $result_arr[$i] = get_threat_info($config['threat_info_api'][$threat_option] . $threat_info[$i], $threat_info[$i], $threat_option, $databaseinfo, $curr_interval_update_time);
                        if ($result_arr[$i]->response_code == '1') {//查询成功时才写库
                            $common->update_email_threat_info($threat_info[$i], $result_arr[$i]->domains, $result_arr[$i]->references, $result_arr[$i]->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                        }
                    }
                    break;
                case 'domain':
                    for ($i = 0; $i < count($threat_info); $i++) {
                        $result_arr[$i] = get_threat_info($config['threat_info_api'][$threat_option] . $threat_info[$i], $threat_info[$i], $threat_option, $databaseinfo, $curr_interval_update_time);
                        if ($result_arr[$i]->response_code == '1') {//查询成功时才写库
                            $common->update_domain_threat_info($threat_info[$i], $result_arr[$i]->last_resolved, $result_arr[$i]->ip_address, $result_arr[$i]->hashes, $result_arr[$i]->emails, $result_arr[$i]->subdomains, $result_arr[$i]->references, $result_arr[$i]->votes, $result_arr[$i]->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                        }
                    }
                    break;

                case 'ip':
                    for ($i = 0; $i < count($threat_info); $i++) {
                        $result_arr[$i] = get_threat_info($config['threat_info_api'][$threat_option] . $threat_info[$i], $threat_info[$i], $threat_option, $databaseinfo, $curr_interval_update_time);
                        if ($result_arr[$i]->response_code == '1') {//查询成功时才写库
                            $common->update_ip_threat_info($threat_info[$i], $result_arr[$i]->last_resolved, $result_arr[$i]->domain, $result_arr[$i]->hashes, $result_arr[$i]->references, $result_arr[$i]->votes, $result_arr[$i]->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                        }
                    }
                    break;

                case 'hash':

                    for ($i = 0; $i < count($threat_info); $i++) {
                        $result_arr[$i] = get_threat_info($config['threat_info_api'][$threat_option] . $threat_info[$i], $threat_info[$i], $threat_option, $databaseinfo, $curr_interval_update_time);
                        if ($result_arr[$i]->response_code == '1') {//查询成功时才写库
                            $common->update_hash_threat_info($threat_info[$i], $result_arr[$i]->md5, $result_arr[$i]->sha1, $result_arr[$i]->scans, $result_arr[$i]->ips, $result_arr[$i]->domains, $result_arr[$i]->references, $result_arr[$i]->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                        }
                    }

                    break;

                case 'antivirus':
                    for ($i = 0; $i < count($threat_info); $i++) {
                        $result_arr[$i] = get_threat_info($config['threat_info_api'][$threat_option] . $threat_info[$i], $threat_info[$i], $threat_option, $databaseinfo, $curr_interval_update_time);
                        if ($result_arr[$i]->response_code == '1') {//查询成功时才写库
                            $common->update_antivirus_threat_info($threat_info[$i], $result_arr[$i]->hashes, $result_arr[$i]->references, $result_arr[$i]->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);
                        }
                    }
                    break;
                default:
                    break;
            }

            die(export_threat_excel($threat_info, $result_arr, $threat_option));
        }

        /*API地址拼接*/
        $threat_api_link = $config['threat_info_api'][$threat_option] . $threat_info;

        /*威胁信息结果获取*/
        $result = get_threat_info($threat_api_link, $threat_info, $threat_option, $databaseinfo, $curr_interval_update_time);


        //当查询成功时将数据入库
        if ($result->response_code == '1') {

            //单个查询前台及入库操作
            switch ($threat_option) {
                case 'email':
                    //查询成功，进行入库操作
                    $common->update_email_threat_info($threat_info, $result->domains, $result->references, $result->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);

                    //组装单个查询前台显示列表
                    $tmp_data = '<b>Domains：</b>';
                    $domains_array = explode(',', $result->domains);
                    for ($i = 0; $i < count($domains_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($domains_array[$i] != '') ? $domains_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>References：</b>';
                    $references_array = explode(',', $result->references);
                    for ($i = 0; $i < count($references_array); $i++) {
                        if ($i % 1 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($references_array[$i] != '') ? $references_array[$i] . '' : 'N/A');
                    }

                    $tmp_data .= '<br><b>Permalink：</b>';
                    $tmp_data .= (($result->permalink != '') ? $result->permalink : 'N/A');
                    break;
                case 'domain':
                    //查询成功，进行入库操作
                    $common->update_domain_threat_info($threat_info, $result->last_resolved, $result->ip_address, $result->hashes, $result->emails, $result->subdomains, $result->references, $result->votes, $result->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);

                    //组装单个查询前台显示列表
                    $tmp_data = '<b>Resolutions：</b>';

                    $last_resolved_array = explode(',', $result->last_resolved);
                    $ip_address_array = explode(',', $result->ip_address);
                    for ($i = 0; $i < count($last_resolved_array); $i++) {
                        $tmp_data .= '<br>&nbsp;last_resolved：' . (($last_resolved_array[$i] != '') ? $last_resolved_array[$i] : 'N/A');
                        $tmp_data .= '<br>&nbsp;ip_address：' . (($ip_address_array[$i] != '') ? $ip_address_array[$i] : 'N/A');
                    }

                    $tmp_data .= '<br><b>Hashes：</b>';
                    $hashes_array = explode(',', $result->hashes);
                    for ($i = 0; $i < count($hashes_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($hashes_array[$i] != '') ? $hashes_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>Emails：</b>';
                    $emails_array = explode(',', $result->emails);
                    for ($i = 0; $i < count($emails_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($emails_array[$i] != '') ? $emails_array[$i] . ' , ' : 'N/A , ');
                    }


                    $tmp_data .= '<br><b>Subdomains：</b>';
                    $subdomains_array = explode(',', $result->subdomains);
                    for ($i = 0; $i < count($subdomains_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($subdomains_array[$i] != '') ? $subdomains_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>References：</b>';
                    $references_array = explode(',', $result->references);
                    for ($i = 0; $i < count($references_array); $i++) {
                        if ($i % 1 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($references_array[$i] != '') ? $references_array[$i] . '' : 'N/A');
                    }

                    $tmp_data .= '<br><b>Votes：</b>';
                    $tmp_data .= (($result->votes != '') ? $result->votes : 'N/A');
                    $tmp_data .= '<br><b>Permalink：</b>';
                    $tmp_data .= (($result->permalink != '') ? $result->permalink : 'N/A');

                    break;

                case 'ip':

                    //查询成功，进行入库操作
                    $common->update_ip_threat_info($threat_info, $result->last_resolved, $result->domain, $result->hashes, $result->references, $result->votes, $result->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);

                    //组装单个查询前台显示列表
                    $tmp_data = '<b>Resolutions：</b>';

                    $last_resolved_array = explode(',', $result->last_resolved);
                    $domain_array = explode(',', $result->domain);
                    for ($i = 0; $i < count($last_resolved_array); $i++) {
                        $tmp_data .= '<br>&nbsp;last_resolved：' . (($last_resolved_array[$i] != '') ? $last_resolved_array[$i] : 'N/A');
                        $tmp_data .= '<br>&nbsp;domain：' . (($domain_array[$i] != '') ? $domain_array[$i] : 'N/A');
                    }

                    $tmp_data .= '<br><b>Hashes：</b>';

                    $hashes_array = explode(',', $result->hashes);
                    for ($i = 0; $i < count($hashes_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= ($hashes_array[$i] != '' ? $hashes_array[$i] . ' , ' : 'N/A');
                    }

                    $tmp_data .= '<br><b>References：</b>';
                    $references_array = explode(',', $result->references);
                    for ($i = 0; $i < count($references_array); $i++) {
                        if ($i % 1 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($references_array[$i] != '') ? $references_array[$i] . '' : 'N/A');
                    }

                    $tmp_data .= '<br><b>Votes：</b>';
                    $tmp_data .= (($result->votes != '') ? $result->votes : 'N/A');
                    $tmp_data .= '<br><b>Permalink：</b>';
                    $tmp_data .= (($result->permalink != '') ? $result->permalink : 'N/A');

                    break;

                case 'hash':

                    //查询成功，进行入库操作
                    $common->update_hash_threat_info($threat_info, $result->md5, $result->sha1, $result->scans, $result->ips, $result->domains, $result->references, $result->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);

                    //组装单个查询前台显示列表
                    $tmp_data = '<br><b>Md5：</b>';
                    $tmp_data .= (($result->md5 != '') ? $result->md5 : 'N/A');
                    $tmp_data .= '<br><b>Sha1：</b>';
                    $tmp_data .= (($result->sha1 != '') ? $result->sha1 : 'N/A');

                    $tmp_data .= '<br><b>Scans：</b>';
                    $scans_array = explode(',', $result->scans);
                    for ($i = 0; $i < count($scans_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= ($scans_array[$i] != '' ? $scans_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>Ips：</b>';
                    $ips_array = explode(',', $result->ips);
                    for ($i = 0; $i < count($ips_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= ($ips_array[$i] != '' ? $ips_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>Domains：</b>';
                    $domains_array = explode(',', $result->domains);
                    for ($i = 0; $i < count($domains_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= ($domains_array[$i] != '' ? $domains_array[$i] . ' , ' : 'N/A , ');
                    }

                    $tmp_data .= '<br><b>References：</b>';
                    $references_array = explode(',', $result->references);
                    for ($i = 0; $i < count($references_array); $i++) {
                        if ($i % 1 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($references_array[$i] != '') ? $references_array[$i] . '' : 'N/A');
                    }

                    $tmp_data .= '<br><b>Permalink：</b>';
                    $tmp_data .= (($result->permalink != '') ? $result->permalink : 'N/A');

                    break;

                case 'antivirus':

                    //查询成功，进行入库操作
                    $common->update_antivirus_threat_info($threat_info, $result->hashes, $result->references, $result->permalink, date('Y-m-d H:i:s', time()), $curr_interval_update_time);

                    //组装单个查询前台显示列表
                    $tmp_data = '<br><b>Hashes：</b>';
                    $hashes_array = explode(',', $result->hashes);
                    for ($i = 0; $i < count($hashes_array); $i++) {
                        if ($i % 3 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= $hashes_array[$i] . ' , ';
                    }

                    $tmp_data .= '<br><b>References：</b>';
                    $references_array = explode(',', $result->references);
                    for ($i = 0; $i < count($references_array); $i++) {
                        if ($i % 1 == 0) {
                            $tmp_data .= '<br>';
                        }
                        $tmp_data .= (($references_array[$i] != '') ? $references_array[$i] . '' : 'N/A');
                    }

                    $tmp_data .= '<br><b>Permalink：</b>';
                    $tmp_data .= (($result->permalink != '') ? $result->permalink : 'N/A');

                    break;
                default:
                    break;
            }
            //结果赋值
            $result = $tmp_data;
        }
        die($result);
        break;

    default:
        break;
}


?>