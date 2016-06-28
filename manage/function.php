<?php
/**
 * Created by PhpStorm.
 * User: Zhaoyu
 * Date: 2016/6/16
 * Time: 16:56
 */

error_reporting(0);
require_once('config.php');
require_once ('../libraries/PHPExcel/PHPExcel.php');
require_once ('../libraries/IP/IP.class.php');

ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0)');

/*API地址拼接*/
$api_index = $config['cur_ip_api_cfg'];//获取设置的默认API索引
$api_link = $config['ip_api_info'][$api_index];

//die(var_dump($_POST));

switch ($_GET['flag']) {

    /*
     * 单个查询的情况处理
     * */
    case 'single_search':
        /*POST查询数据获取*/
        $search_info = $_POST['_search_info'];

        /*API地址拼接*/
        $api_link .= gethostbyname($search_info);

        /*IP地址结果获取*/
        $result = get_result_info($api_index, $api_link);

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


/**
 * 获取目标地址
 * $_index       索引号 //用来区分调用哪个网站API
 * $_api_link    API地址
 * return $result 结果信息
 */
function get_result_info($_index, $_api_link){
    $result = "fail";
    switch ($_index) {

        case 'taobao':

            $result_info = json_decode(get_page_info($_api_link));//获取网页请求返回内容

            //淘宝API返回code说明：0：成功，1：失败。
            if (!$result_info->code) {
                $result = $result_info->data->country;

                $result .= ',' . $result_info->data->region;

                $result .= ',' . $result_info->data->city;

                $result .= ',' . $result_info->data->isp;
            }

            break;

        case 'ipip':

            //$result_info = get_page_info($_api_link);
            $result_info = file_get_contents($_api_link);//获取网页请求返回内容

            if ($result_info != null) {
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
 * @return mixed
 */
function get_page_info($_url){
    $ch = curl_init();
    $timeout = 3;

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $_url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);

    /*
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $url = $_url;
    curl_setopt($ch, CURLOPT_URL, $url);
    $page = curl_exec($ch);
    $data = array(
        'page' => $page,
        'http_code' => curl_getinfo($ch)['http_code']
    );
    curl_close($ch);
    */
    return $data;
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