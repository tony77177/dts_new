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
     * 更新DOMAIN威胁信息
     * @param $_domain          域名
     * @param $_resolutions     
     * @param $_hashes
     * @param $_emails
     * @param $_subdomains
     * @param $_references
     * @param $_votes
     * @param $_permalink
     * @param $_upd_time
     * @param $_curr_interval_update_time
     */
    public function update_domain_threat_info($_domain,$_resolutions,$_hashes,$_emails,$_subdomains,$_references,$_votes,$_permalink,$_upd_time,$_curr_interval_update_time){
        $sql = 'call pr_update_domain_info("' . $_domain . '","' . $_resolutions . '","' . $_hashes . '","' . $_emails . '","' . $_subdomains . '","' . $_references . '","' . $_votes . '","' . $_permalink . '","' . $_upd_time . '",' . $_curr_interval_update_time . ')';
        $this->db->ReturnDataTable($sql);
    }


    /**
     * 更新ANTIVIRUS威胁信息
     * @param $_antivirus
     * @param $_hashes
     * @param $_references
     * @param $_permalink
     * @param $_upd_time
     * @param $_curr_interval_update_time
     */
    public function update_antivirus_threat_info($_antivirus, $_hashes, $_references, $_permalink, $_upd_time, $_curr_interval_update_time){
        $sql = 'call pr_update_antivirus_info("' . $_antivirus . '","' . $_hashes . '","' . $_references . '","' . $_permalink . '","' . $_upd_time . '",' . $_curr_interval_update_time . ')';
        $this->db->ReturnDataTable($sql);
    }


    /**
     * 更新HASH威胁信息
     * @param $_hash
     * @param $_md5
     * @param $_sha1
     * @param $_scans
     * @param $_ips
     * @param $_domains
     * @param $_references
     * @param $_permalink
     * @param $_upd_time
     * @param $_curr_interval_update_time
     */
    public function update_hash_threat_info($_hash, $_md5, $_sha1, $_scans, $_ips, $_domains, $_references, $_permalink, $_upd_time, $_curr_interval_update_time){
        $sql = 'call pr_update_hash_info("' . $_hash . '","' . $_md5 . '","' . $_sha1 . '","' . $_scans . '","' . $_ips . '","' . $_domains . '","' . $_references . '","' . $_permalink . '","' . $_upd_time . '",' . $_curr_interval_update_time . ')';
        $this->db->ReturnDataTable($sql);
    }

    /**
     * 通过SQL获取归属地信息
     * @param $_ip_address
     * @return array
     */
    public function get_location_info($_ip_address){
        $sql = 'SELECT * FROM searip WHERE IPAddress="' . $_ip_address . '"';
        return $this->db->GetResult($sql);
    }

    /**
     * 通过SQL获取email威胁信息
     * @param $_email
     * @return array
     */
    public function get_email_threat_info($_email){
        $sql = 'SELECT * FROM threat_email WHERE email="' . $_email . '"';
        return $this->db->GetResult($sql);
    }

    /**
     * 通过SQL获取domain威胁信息
     * @param $_domain
     * @return array
     */
    public function get_domain_threat_info($_domain){
        $sql = 'SELECT * FROM threat_domain WHERE domain="' . $_domain . '"';
        return $this->db->GetResult($sql);
    }


    /**
     * 通过SQL查询antivirus威胁信息
     * @param $_antivirus
     * @return null
     */
    public function get_antivirus_threat_info($_antivirus){
        $sql = 'SELECT * FROM threat_antivirus WHERE antivirus="' . $_antivirus . '"';
        return $this->db->GetResult($sql);
    }

    /**
     * 通过SQL查询hash威胁信息
     * @param $_hash
     * @return null
     */
    public function get_hash_threat_info($_hash){
        $sql = 'SELECT * FROM threat_hash WHERE hash="' . $_hash . '"';
        return $this->db->GetResult($sql);
    }

}


?>