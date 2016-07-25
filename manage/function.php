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
function get_location_info($_index, $_api_link,$_token,$_search_info,$_dbinfo){
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
            //die($_api_link);
            /**
             * 此处逻辑：
             *      1、先通过数据库查询是否存在相关的数据，如若存在，则可直接返回
             *      2、如果数据库不存在相关数据，则通过API去拉取最新数据
             */
            $common = new Common($_dbinfo);
            $result_info = $common->get_location_info(gethostbyname($_search_info));
            if ($result_info != null) {
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
//                $tmp_data = json_encode($tmp_data);
//                $tmp_data = json_decode($tmp_data);
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


function get_threat_info($_result_info,$_flag){
    $result= 'fail';
    switch ($_flag){
        case 'email':
            $tmp_data = '';
            for ($i = 0; $i < count($_result_info->domains); $i++) {
                if ($i == 0) {
//                    $tmp_data = 'domains：' . (($_result_info->domains[$i] != '') ? $_result_info->domains[$i] : 'N/A');
                    $tmp_data = 'domains：' . $_result_info->domains[$i].',';
                } else {
                    $tmp_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . (($_result_info->domains[0] != '') ? $_result_info->domains[$i] : 'N/A');
                }
            }

            for ($i = 0; $i < count($_result_info->references); $i++) {
                if ($i == 0) {
                    $tmp_data = 'domains：' . (($_result_info->references[$i] != '') ? $_result_info->references[$i] : 'N/A');
                } else {
                    $tmp_data = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . (($_result_info->references[0] != '') ? $_result_info->references[$i] : 'N/A');
                }
            }

            $tmp_data .= '<br>省会或直辖市：' . (($result->data[1] != '') ? $result->data[1] : 'N/A');
            $tmp_data .= '<br>地区或城市：' . (($result->data[2] != '') ? $result->data[2] : 'N/A');
            $tmp_data .= '<br>学校或单位：' . (($result->data[3] != '') ? $result->data[3] : 'N/A');
            $result = $tmp_data;
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
 * 批量导出查询结果
 * @param $_search_info 查询内容
 * @param $_result_info 查询结果
 * @throws PHPExcel_Reader_Exception    输出excel文档
 */
function export_excel($_search_info,$_result_info){

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

        //设置列垂直居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + 2) . ':O' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
 * 读取excel文件信息
 */
function read_excel($_api_index,$_api_link){
    if (!file_exists("../uploadfiles/Book1.xls")) {
        exit("not found Book1.xls\n");
    }


    $result_info[] = "";//结果数组

    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
    $PHPExcel = $reader->load("../uploadfiles/Book1.xls"); // 载入excel文件
    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
    $highestRow = $sheet->getHighestRow(); // 取得总行数
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    $highestColumm = PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27

//    die($highestRow.'<br>'.$highestColumm);

    /** 循环读取每个单元格的数据 */

    $IPClass = new IP();
    for ($row = 2; $row <= $highestRow; $row++) {//行数是以第2行开始，列只选0和1列（0列代表域名，1列代表IP），做判断，优选0列

        $domain_value = $sheet->getCellByColumnAndRow('0', $row)->getValue();//域名列信息
        $ip_value = $sheet->getCellByColumnAndRow('1', $row)->getValue();//IP列信息

//        echo $domain_value . '/////////' . $ip_value .'///////////////'.gethostbyname($domain_value) .'<br>';


//        $result_info[$row - 2] = $IPClass->find(gethostbyname($domain_value));


        $startTime=explode(' ',microtime());
        $startTime=$startTime[0] + $startTime[1];


        if($domain_value != '') {
            /*API地址拼接*/
            $api_link = $_api_link . gethostbyname($domain_value);
        } elseif ($ip_value != '') {
            /*API地址拼接*/
            $api_link = $_api_link . gethostbyname($ip_value);
        }

//        echo $_api_link . "<br>" . $api_link."<br>";

        /*IP地址结果获取*/
        $result = get_location_info($_api_index, $api_link);
        if ($result != 'fail') {
            $result_info[$row - 2] = $result;
        } else {
            $result_info[$row - 2] = "";
        }

        $endTime = explode(' ',microtime());
        $endTime = $endTime[0] + $endTime[1];
        $totalTime = $endTime - $startTime;
        echo 'curl:'.number_format($totalTime, 10, '.', "")." seconds</br>";


//        for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
//            $columnName = PHPExcel_Cell::stringFromColumnIndex($column);
//
//            echo $columnName . $row . ":" . $sheet->getCellByColumnAndRow($column, $row)->getValue() . "<br />";
//        }
    }
    die(var_dump(($result_info)));
//    die($sheet->getCellByColumnAndRow("A", "2")->getValue());
    export_excel("A1", $sheet->getCellByColumnAndRow("A", "2")->getValue());
}

?>