<?php
/**
 * Created by PhpStorm.
 * 数据库操作类，对相关查询结果信息进行入库操作
 * User: Zhaoyu
 * Date: 2016/7/20
 * Time: 14:23
 */

ini_set('date.timezone','Asia/Shanghai');

class Common{

    private $db;//数据库信息

    //构造函数
    function __construct($db) {
        $this->db = new Database($db);
    }

    /**
     * 更新IP地址地址
     * @param $_IPAdress    IP地址
     * @param $_Country     国家
     * @param $_Province    省会或直辖市
     * @param $_City        地区或城市
     * @param $_Organization    学校或单位
     * @param $_Telecom     运营商字段
     * @param $_Longitude   纬度
     * @param $_Latitude    经度
     * @param $_Area1       时区一（可能不存在）
     * @param $_Area2       时区二（可能不存在）
     * @param $_AdDivisions 中国行政区划代码
     * @param $_InterNum    国际电话代码
     * @param $_CountryNum  国家两位代码
     * @param $_Continents  世界大洲代码
     */
    public function update_ip_adderss_info($_IPAdress,$_Country,$_Province,$_City,$_Organization,$_Telecom,$_Longitude,$_Latitude,$_Area1,$_Area2,$_AdDivisions,$_InterNum,$_CountryNum,$_Continents,$_Update_time){
        $sql = 'INSERT INTO searip (IPAdress,Country,Province,City,Organization,Telecom,Longitude,Latitude,Area1,Area2,AdDivisions,InterNum,CountryNum,Continents,Update_time) VALUES ';
        $sql .= '("'.$_IPAdress.'","'.$_Country.'","'.$_Province.'","'.$_City.'","'.$_Organization.'","'.$_Telecom.'","'.$_Longitude.'","'.$_Latitude.'","'.$_Area1.'","'.$_Area2.'","'.$_AdDivisions.'","'.$_InterNum.'","'.$_CountryNum.'","'.$_Continents.'","'.$_Update_time.'")';
        //die($sql);
        $this->db->ReturnDataTable($sql);
        //die(print_r($result));
        //return $result[0][0];
    }


}


?>