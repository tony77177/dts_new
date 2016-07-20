<?php
/**
 * Created by PhpStorm.
 * 功能函数定义
 * User: Zhaoyu
 * Date: 2016/6/16
 * Time: 16:56
 */



/**
 * 获取目标地址
 * $_index       索引号 //用来区分调用哪个网站API
 * $_api_link    API地址
 * return $result 结果信息
 */
function get_result_info($_index, $_api_link,$_token){
    $result = "fail";
    //die($_index);
    switch ($_index) {

        case 'taobao':

            $result_info = json_decode(get_page_info($_api_link));//获取网页请求返回内容

            die(var_dump($result_info));
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
            $result_info = json_decode(get_page_info($_api_link,$_token));
            //die($result_info);
            if($result_info->ret=='ok'){
//                $data .= '国家：'.$result_info->data[0];
//                $data .= '<br>省会或直辖市：'.$result_info->data[1];
//                $data .= '<br>地区或城市：'.$result_info->data[2];
//                $data .= '<br>纬度：'.$result_info->data[5];
//                $data .= '<br>经度：'.$result_info->data[6];
                $result = $result_info;
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
 * 通过CURL获取页面信息
 * @param $_url
 * @param $_token
 * @return mixed
 */
function get_page_info($_url,$_token){

    $headers = array($_token);

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$_url);
    curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
    $handles = curl_exec($ch);
    curl_close($ch);
    //die(var_dump($handles));
    return $handles;
}


/**
 *
 * excel 导出文档测试
 *
 */
function export_excel($_index,$_value){
    // Create new PHPExcel object

//    die(var_dump($_index."---------------".$_value));
//    echo $_index;
//    die($_value);

    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("dts")
        ->setLastModifiedBy("dts")
        ->setTitle("Office 2007 XLSX Document")
        ->setSubject("Office 2007 XLSX Document");


// Add some data
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue($_index, $_value);
//        ->setCellValue('B2', 'world!')
//        ->setCellValue('C1', 'Hello')
//        ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
//    $objPHPExcel->setActiveSheetIndex(0)
//        ->setCellValue('A4', 'Miscellaneous glyphs')
//        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

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

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
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
        $result = get_result_info($_api_index, $api_link);
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