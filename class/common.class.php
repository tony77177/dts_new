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
     *   更新IP地址信息
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
     * @param $_Curr_interval_update_time   更新数据间隔时间
     */
    public function update_ip_adderss_info($_IPAddress, $_Country, $_Province, $_City, $_Organization, $_Telecom, $_Longitude, $_Latitude, $_Area1, $_Area2, $_AdDivisions, $_InterNum, $_CountryNum, $_Continents, $_Update_time, $_Curr_interval_update_time){
        $sql = 'call pr_update_location_info ("' . $_IPAddress . '","' . $_Country . '","' . $_Province . '","' . $_City . '","' . $_Organization . '","' . $_Telecom . '","' . $_Longitude . '","' . $_Latitude . '","' . $_Area1 . '","' . $_Area2 . '","' . $_AdDivisions . '","' . $_InterNum . '","' . $_CountryNum . '","' . $_Continents . '","' . $_Update_time . '",' . $_Curr_interval_update_time . ')';
        $this->db->ReturnDataTable($sql);
    }

    /**
     * 更新EMAIL威胁信息
     * @param $_email       Email地址
     * @param $_domains     域名信息
     * @param $_references  参考信息
     * @param $_permalink   永久链接
     * @param $_upd_time    更新时间
     * @param $_curr_interval_update_time   更新间隔世界
     */
    public function update_email_threat_info($_email,$_domains,$_references,$_permalink,$_upd_time,$_curr_interval_update_time){
        $sql = 'call pr_update_email_info("' . $_email . '","' . $_domains . '","' . $_references . '","' . $_permalink . '","' . $_upd_time . '",' . $_curr_interval_update_time . ')';
        $this->db->ReturnDataTable($sql);
    }


    /**
     * 通过SQL获取归属地信息
     * @param $_ip_address
     * @return null
     */
    public function get_location_info($_ip_address){
        $sql = 'SELECT * FROM searip WHERE IPAddress="' . $_ip_address . '"';
        return $this->db->GetResult($sql);
    }

    /**
     * 通过SQL获取email威胁信息
     * @param $_email
     * @return null
     */
    public function get_email_threat_info($_email){
        $sql = 'SELECT * FROM threat_email WHERE email="' . $_email . '"';
        return $this->db->GetResult($sql);
    }

}


?>