<?php
/**
 * Created by PhpStorm.
 * 功能函数定义
 * User: Zhaoyu
 * Date: 2016/6/16
 * Time: 16:56
 */




/**
 * 获取归属地信息
 * $_index       索引号 //用来区分调用哪个网站API
 * $_api_link    API地址
 * return $result 结果信息
 */
function get_location_info($_index, $_api_link,$_token,$_search_info,$_dbinfo,$_curr_interval_update_time){
    $result = "fail";
    //die($_index);
    switch ($_index) {

        case 'taobao':

            $result_info = json_decode(get_page_info($_api_link));//获取网页请求返回内容

            //die(var_dump($result_info));
            //淘宝API返回code说明：0：成功，1：失败。
            if (!$result_info->code) {
                $result = $result_info->data->country;

                $result .= ',' . $result_info->data->region;

                $result .= ',' . $result_info->data->city;

                $result .= ',' . $result_info->data->isp;
            }

            break;

        case 'ipip':

            $result_info = get_page_info($_api_link);
            die($result_info);
            //$result_info = file_get_contents($_api_link);//获取网页请求返回内容

            if ($result_info != null) {
                $result = $result_info;
            }

            break;
        case 'ipip_vip':
            /**
             * 此处逻辑：
             *      1、先通过数据库查询是否存在相关的数据，如若存在，则可直接返回
             *      2、如果数据库不存在相关数据，则通过API去拉取最新数据
             */
            $common = new Common($_dbinfo);
            $result_info = $common->get_location_info(gethostbyname($_search_info));

            if (($result_info != null) && (time() - strtotime($result_info['Update_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "ret" => "ok",
                    "data" => array(
                        $result_info['Country'],
                        $result_info['Province'],
                        $result_info['City'],
                        $result_info['Organization'],
                        $result_info['Telecom'],
                        $result_info['Longitude'],
                        $result_info['Latitude'],
                        $result_info['Area1'],
                        $result_info['Area2'],
                        $result_info['AdDivisions'],
                        $result_info['InterNum'],
                        $result_info['CountryNum'],
                        $result_info['Continents']
                    )
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, $_token));
                if ($result_info->ret == 'ok') {
                    $result = $result_info;
                }
            }

            break;
        case 'ip138':

            //截止2016年6月17日14:21:09，IP138 API接口无法访问，暂不做支持

            break;

        default:
            break;
    }
    return $result;
}


/**
 * 查询威胁信息
 * @param $_api_link        API地址
 * @param $_search_info     查询信息
 * @param $_flag            类别查询标志
 * @param $_dbinfo          数据库信息
 * @return array|mixed|string
 */
function get_threat_info($_api_link, $_search_info, $_flag, $_dbinfo, $_curr_interval_update_time){
    $result = 'fail';
    $common = new Common($_dbinfo);
    switch ($_flag) {
        case 'email':   //Email信息查询
            //处理逻辑：先读库获取，获取失败再利用API获取
            $result_info = $common->get_email_threat_info($_search_info);
            if (($result_info != null) && (time() - strtotime($result_info['upd_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "response_code" => "1",
                    "domains" => $result_info['domains'],
                    "references" => $result_info['references'],
                    "permalink" => $result_info['permalink']
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, ''));
                if ($result_info->response_code == '1') {
                    //组装JSON数据
                    $tmp_data = array(
                        "response_code" => "1",
                        "domains" => implode(',', $result_info->domains),
                        "references" => implode(',', $result_info->references),
                        "permalink" => $result_info->permalink
                    );
                    $result = json_decode(json_encode($tmp_data));
                }
            }
            break;
        case 'domain':  //域名信息查询
            //处理逻辑：先读库获取，获取失败再利用API获取
            $result_info = $common->get_domain_threat_info($_search_info);
            $a=time();
            $b=strtotime($result_info['upd_time']);
            $c=$a-$b;

            if (($result_info != null) && (time() - strtotime($result_info['upd_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "response_code" => "1",
                    "last_resolved" => $result_info['last_resolved'],
                    "ip_address" => $result_info['ip_address'],
                    "hashes" => $result_info['hashes'],
                    "emails" => $result_info['emails'],
                    "subdomains" => $result_info['subdomains'],
                    "references" => $result_info['references'],
                    "votes" => $result_info['votes'],
                    "permalink" => $result_info['permalink']
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, ''));
                if ($result_info->response_code == '1') {
                    //组装JSON数据
                    $last_resolved = array();
                    $ip_address = array();
                    foreach ($result_info->resolutions as $value) {//组装数组，进行入栈操作
                        array_push($last_resolved, $value->last_resolved);
                        array_push($ip_address, $value->ip_address);
                    }
                    $tmp_data = array(
                        "response_code" => "1",
                        "last_resolved" => implode(',', $last_resolved),
                        "ip_address" => implode(',', $ip_address),
                        "hashes" => implode(',', $result_info->hashes),
                        "emails" => implode(',', $result_info->emails),
                        "subdomains" => implode(',', $result_info->subdomains),
                        "references" => implode(',', $result_info->references),
                        "votes" => $result_info->votes,
                        "permalink" => $result_info->permalink
                    );
                    $result = json_decode(json_encode($tmp_data));
                }
            }
            break;
        case 'ip':      //IP地址信息查询
            $result_info = $common->get_ip_threat_info($_search_info);
            if (($result_info != null) && (time() - strtotime($result_info['upd_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "response_code" => "1",
                    "last_resolved" => $result_info['last_resolved'],
                    "domain" => $result_info['domain'],
                    "hashes" => $result_info['hashes'],
                    "references" => $result_info['references'],
                    "votes" => $result_info['votes'],
                    "permalink" => $result_info['permalink']
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, ''));
                if ($result_info->response_code == '1') {
                    //组装JSON数据
                    $last_resolved = array();
                    $domain = array();
                    foreach ($result_info->resolutions as $value) {//数组入栈操作
                        array_push($last_resolved, $value->last_resolved);
                        array_push($domain, $value->domain);
                    }
                    $tmp_data = array(
                        "response_code" => "1",
                        "last_resolved" => implode(',', $last_resolved),
                        "domain" => implode(',', $domain),
                        "hashes" => implode(',', $result_info->hashes),
                        "references" => implode(',', $result_info->references),
                        "votes" => $result_info->votes,
                        "permalink" => $result_info->permalink
                    );
                    $result = json_decode(json_encode($tmp_data));
                }
            }

            break;
        case 'hash':    //HASH信息查询
            $result_info = $common->get_hash_threat_info($_search_info);
            if (($result_info != null) && (time() - strtotime($result_info['upd_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "response_code" => "1",
                    "md5" => $result_info['md5'],
                    "sha1" => $result_info['sha1'],
                    "scans" => $result_info['scans'],
                    "ips" => $result_info['ips'],
                    "domains" => $result_info['domains'],
                    "references" => $result_info['references'],
                    "permalink" => $result_info['permalink']
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, ''));
                if ($result_info->response_code == '1') {
                    //组装JSON数据
                    $tmp_data = array(
                        "response_code" => "1",
                        "md5" => $result_info->md5,
                        "sha1" => $result_info->sha1,
                        "scans" => implode(',', $result_info->scans),
                        "ips" => implode(',', $result_info->ips),
                        "domains" => implode(',', $result_info->domains),
                        "references" => implode(',', $result_info->references),
                        "permalink" => $result_info->permalink
                    );

                    $result = json_decode(json_encode($tmp_data));
                }
            }

            break;
        case 'antivirus':   //antivirus信息查询
            //处理逻辑：先读库获取，获取失败再利用API获取
            $result_info = $common->get_antivirus_threat_info($_search_info);
            if (($result_info != null) && (time() - strtotime($result_info['upd_time']) < $_curr_interval_update_time)) {
                //组装JSON数据
                $tmp_data = array(
                    "response_code" => "1",
                    "hashes" => $result_info['hashes'],
                    "references" => $result_info['references'],
                    "permalink" => $result_info['permalink']
                );
                $result = json_decode(json_encode($tmp_data));
            } else {
                $result_info = json_decode(get_page_info($_api_link, ''));
                if ($result_info->response_code == '1') {
                    //组装JSON数据
                    $tmp_data = array(
                        "response_code" => "1",
                        "hashes" => implode(',', $result_info->hashes),
                        "references" => implode(',', $result_info->references),
                        "permalink" => $result_info->permalink
                    );
                    $result = json_decode(json_encode($tmp_data));
                }
            }
            break;
        default;
            break;
    }
    return $result;
}



/**
 * 通过CURL获取页面信息
 * @param $_url
 * @param $_token
 * @return mixed
 */
function get_page_info($_url, $_token){
    $headers = array($_token);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    /**
     * 此处用于区分归属地信息查询和威胁信息查询：
     * 1、归属新为HTTP请求带token
     * 2、威胁信息查询为HTTPS请求，不带token
     * 所以需要根据不同情况设置不同参数
     */

    if ($_token != '') {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    } else {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $handles = curl_exec($ch);
    curl_close($ch);
    return $handles;
}


/**
 * 批量导出归属地查询结果查询结果
 * @param $_search_info 查询内容
 * @param $_result_info 查询结果
 * @throws PHPExcel_Reader_Exception    输出excel文档
 */
function export_location_excel($_search_info,$_result_info){
    //新建Excel类
    $objPHPExcel = new PHPExcel();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("dts")
        ->setLastModifiedBy("dts")
        ->setTitle("Office 2007 XLSX Document")
        ->setSubject("Office 2007 XLSX Document");


    // 设置标题栏
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', '查询内容')
        ->setCellValue('B1', 'IP地址')
        ->setCellValue('C1', '国家')
        ->setCellValue('D1', '省会或直辖市')
        ->setCellValue('E1', '地区或城市')
        ->setCellValue('F1', '学校或单位')
        ->setCellValue('G1', '运营商')
        ->setCellValue('H1', '纬度')
        ->setCellValue('I1', '经度')
        ->setCellValue('J1', '时区一')
        ->setCellValue('K1', '时区二')
        ->setCellValue('L1', '中国行政区划代码')
        ->setCellValue('M1', '国际电话代码')
        ->setCellValue('N1', '国家二位代码')
        ->setCellValue('O1', '世界大洲代码');

    //设置字体加粗、字体大小及垂直居中
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:O1')->getFont()->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:O1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:O1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
    $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

    //添加内容
    for ($i = 0; $i < count($_search_info); $i++) {
        if($_result_info[$i]->ret=='ok'){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($i+2), $_search_info[$i])
                ->setCellValue('B'.($i+2), gethostbyname($_search_info[$i]))
                ->setCellValue('C'.($i+2), $_result_info[$i]->data[0])
                ->setCellValue('D'.($i+2), $_result_info[$i]->data[1])
                ->setCellValue('E'.($i+2), $_result_info[$i]->data[2])
                ->setCellValue('F'.($i+2), $_result_info[$i]->data[3])
                ->setCellValue('G'.($i+2), $_result_info[$i]->data[4])
                ->setCellValue('H'.($i+2), $_result_info[$i]->data[5])
                ->setCellValue('I'.($i+2), $_result_info[$i]->data[6])
                ->setCellValue('J'.($i+2), $_result_info[$i]->data[7])
                ->setCellValue('K'.($i+2), $_result_info[$i]->data[8])
                ->setCellValue('L'.($i+2), $_result_info[$i]->data[9])
                ->setCellValue('M'.($i+2), $_result_info[$i]->data[10])
                ->setCellValue('N'.($i+2), $_result_info[$i]->data[11])
                ->setCellValue('O'.($i+2), $_result_info[$i]->data[12]);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($i+2), $_search_info[$i])
                ->setCellValue('B'.($i+2), gethostbyname($_search_info[$i]))
                ->setCellValue('C'.($i+2), '查询失败，请确认数据正确性');
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . ($i + 2) . ':O' . (($i + 2)));//合并单元格
        }

        //设置列宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(17);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(18);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(18);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(18);

        //设置列垂直居中
//        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':O' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('查询结果');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="批量查询结果-'.date("Ymdhis",time()).'.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');
}

/**
 * 批量导出威胁信息查询结果
 * @param $_search_info
 * @param $_result_info
 * @param $_threat_option
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function export_threat_excel($_search_info, $_result_info, $_threat_option){
    //新建Excel类
    $objPHPExcel = new PHPExcel();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("dts")
        ->setLastModifiedBy("dts")
        ->setTitle("Office 2007 XLSX Document")
        ->setSubject("Office 2007 XLSX Document");

    switch ($_threat_option) {
        case 'email':
            // 设置标题栏
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '查询内容')
                ->setCellValue('B1', 'Domains')
                ->setCellValue('C1', 'References')
                ->setCellValue('D1', 'Permalink');

            //设置字体加粗、字体大小及垂直居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getFont()->setSize(12);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

            //添加内容
            for ($i = 0; $i < count($_search_info); $i++) {
                if ($_result_info[$i]->response_code == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), $_result_info[$i]->domains)
                        ->setCellValue('C' . ($i + 2), $_result_info[$i]->references)
                        ->setCellValue('D' . ($i + 2), $_result_info[$i]->permalink);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), '查询失败，请确认数据正确性');
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':D' . (($i + 2)));//合并单元格
                }

                //设置列宽度
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);

                //设置列垂直居中
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            break;
        case 'domain':

            // 设置标题栏
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '查询内容')
                ->setCellValue('B1', 'last_resolved')
                ->setCellValue('C1', 'ip_address')
                ->setCellValue('D1', 'hashes')
                ->setCellValue('E1', 'emails')
                ->setCellValue('F1', 'subdomains')
                ->setCellValue('G1', 'references')
                ->setCellValue('H1', 'votes')
                ->setCellValue('I1', 'permalink');

            //设置字体加粗、字体大小及垂直居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:I1')->getFont()->setSize(12);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:I1')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

            //添加内容
            for ($i = 0; $i < count($_search_info); $i++) {
                if ($_result_info[$i]->response_code == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), $_result_info[$i]->last_resolved)
                        ->setCellValue('C' . ($i + 2), $_result_info[$i]->ip_address)
                        ->setCellValue('D' . ($i + 2), $_result_info[$i]->hashes)
                        ->setCellValue('E' . ($i + 2), $_result_info[$i]->emails)
                        ->setCellValue('F' . ($i + 2), $_result_info[$i]->subdomains)
                        ->setCellValue('G' . ($i + 2), $_result_info[$i]->references)
                        ->setCellValue('H' . ($i + 2), $_result_info[$i]->votes)
                        ->setCellValue('I' . ($i + 2), $_result_info[$i]->permalink);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), '查询失败，请确认数据正确性');
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':I' . (($i + 2)));//合并单元格
                }

                //设置列宽度
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(25);

                //设置列垂直居中
//                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            break;

        case 'ip':

            // 设置标题栏
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '查询内容')
                ->setCellValue('B1', 'last_resolved')
                ->setCellValue('C1', 'domain')
                ->setCellValue('D1', 'hashes')
                ->setCellValue('E1', 'references')
                ->setCellValue('F1', 'votes')
                ->setCellValue('G1', 'permalink');

            //设置字体加粗、字体大小及垂直居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:G1')->getFont()->setSize(12);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:G1')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

            //添加内容
            for ($i = 0; $i < count($_search_info); $i++) {
                if ($_result_info[$i]->response_code == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), $_result_info[$i]->last_resolved)
                        ->setCellValue('C' . ($i + 2), $_result_info[$i]->domain)
                        ->setCellValue('D' . ($i + 2), $_result_info[$i]->hashes)
                        ->setCellValue('E' . ($i + 2), $_result_info[$i]->references)
                        ->setCellValue('F' . ($i + 2), $_result_info[$i]->votes)
                        ->setCellValue('G' . ($i + 2), $_result_info[$i]->permalink);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), '查询失败，请确认数据正确性');
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':G' . (($i + 2)));//合并单元格
                }

                //设置列宽度
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(25);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(25);

                //设置列垂直居中
//                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            break;

        case 'hash':
            // 设置标题栏
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '查询内容')
                ->setCellValue('B1', 'Md5')
                ->setCellValue('C1', 'Sha1')
                ->setCellValue('D1', 'Scans')
                ->setCellValue('E1', 'Ips')
                ->setCellValue('F1', 'Domains')
                ->setCellValue('G1', 'References')
                ->setCellValue('H1', 'Permalink');

            //设置字体加粗、字体大小及垂直居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:H1')->getFont()->setSize(12);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:H1')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

            //添加内容
            for ($i = 0; $i < count($_search_info); $i++) {
                if ($_result_info[$i]->response_code == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), $_result_info[$i]->md5)
                        ->setCellValue('C' . ($i + 2), $_result_info[$i]->sha1)
                        ->setCellValue('D' . ($i + 2), $_result_info[$i]->scans)
                        ->setCellValue('E' . ($i + 2), $_result_info[$i]->ips)
                        ->setCellValue('F' . ($i + 2), $_result_info[$i]->domains)
                        ->setCellValue('G' . ($i + 2), $_result_info[$i]->references)
                        ->setCellValue('H' . ($i + 2), $_result_info[$i]->permalink);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), '查询失败，请确认数据正确性');
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':H' . (($i + 2)));//合并单元格
                }

                //设置列宽度
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);

                //设置列垂直居中
//                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':H' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            break;

        case 'antivirus':

            // 设置标题栏
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '查询内容')
                ->setCellValue('B1', 'Hashes')
                ->setCellValue('C1', 'References')
                ->setCellValue('D1', 'Permalink');

            //设置字体加粗、字体大小及垂直居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getFont()->setSize(12);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);////水平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);////垂直平对齐
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(25);//行高

            //添加内容
            for ($i = 0; $i < count($_search_info); $i++) {
                if ($_result_info[$i]->response_code == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), $_result_info[$i]->hashes)
                        ->setCellValue('C' . ($i + 2), $_result_info[$i]->references)
                        ->setCellValue('D' . ($i + 2), $_result_info[$i]->permalink);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $_search_info[$i])
                        ->setCellValue('B' . ($i + 2), '查询失败，请确认数据正确性');
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':D' . (($i + 2)));//合并单元格
                }

                //设置列宽度
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);

                //设置列垂直居中
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            break;

        default:
            break;
    }


    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('查询结果');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="批量查询结果-' . date("Ymdhis", time()) . '.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');
}

?>