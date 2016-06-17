<?php
/**
 * Created by PhpStorm.
 * User: Zhaoyu
 * Date: 2016/6/16
 * Time: 16:56
 */

require_once('config.php');
require_once ('../libraries/PHPExcel/PHPExcel.php');

ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0)');

//die(var_dump($_POST));

switch ($_GET['flag']) {

    /*
     * 单个查询的情况处理
     * */
    case 'single_search':
        /*POST查询数据获取*/
        $search_info = $_POST['_search_info'];

        /*API地址拼接*/
        $api_index = $config['cur_ip_api_cfg'];//获取设置的默认API索引
        $api_link = $config['ip_api_info'][$api_index];
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
        die(export_excel());
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
function get_result_info($_index, $_api_link)
{
    $result = "fail";
    switch ($_index) {

        case 'taobao':

            $result_info = json_decode(file_get_contents($_api_link));//获取网页请求返回内容

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
function get_page_info($_url)
{
    $ch = curl_init();
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
    return $data;
}


/**
 *
 * excel 导出文档测试
 *
 */
function export_excel()
{
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");


// Add some data
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Hello')
        ->setCellValue('B2', 'world!')
        ->setCellValue('C1', 'Hello')
        ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A4', 'Miscellaneous glyphs')
        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="01simple.xls"');
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

?>