<?php
/**
 * Created by PhpStorm.
 * User: Zhaoyu
 * Date: 2016/6/17
 * Time: 13:58
 * Desc: 配置文件信息，请勿随意改动
 */

/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             获取IP归属API配置
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * taobao    淘宝API免费公共接口       地址：http://ip.taobao.com/
 *
 * ipip      IPIP.net免费公共接口     地址：http://freeapi.ipip.net/
 *           IPIP.net付费公共接口     地址：http://ipapi.ipip.net/find
 *
 * ip138     IP138.com免费公共接口    地址：http://www.ip138.com/api/
 *
 *
 */

$config['ip_api_info'] = array(
    'taobao' => 'http://ip.taobao.com/service/getIpInfo.php?ip=',
    'ipip' => 'http://freeapi.ipip.net/',
    'ipip_vip'=>'http://ipapi.ipip.net/find?addr=',
    'ip138' => 'http://test.ip138.com/query/?ip='
);


/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             设置当前获取IP地址的接口
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * taobao    淘宝API免费公共接口       地址：http://ip.taobao.com/
 *
 * ipip      IPIP.net免费公共接口     地址：http://freeapi.ipip.net/
 *           IPIP.net付费公共接口     地址：http://ipapi.ipip.net/find
 *
 * ip138     IP138.com免费公共接口    地址：http://www.ip138.com/api/
 *
 * 缺省采用IPIP.net付费API，更换的话将 taobao 更改即可；
 *
 */

$config['cur_ip_api_cfg'] = 'ipip_vip';


/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             设置当前token
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 目前采用IPIP.net付费接口，，更换时只需更换加密部分即可。
 * 如 Token: 89bd0fbbb124c00a1bdec009208517cc7277b2bc
 *
 *
 */

$config['cur_token'] = 'Token: 89bd0fbbb124c00a1bdec009208517cc7277b2bc';


/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             获取威胁信息API配置
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 配置威胁信息获取API接口，默认有5种查询方式：邮箱、域名、IP地址、hash及antivirus
 * API文档地址：https://github.com/threatcrowd/ApiV2
 *
 *
 */
$config['threat_info_api'] = array(
    'email' => 'https://www.threatcrowd.org/searchApi/v2/email/report/?email=',
    'domain' => 'https://www.threatcrowd.org/searchApi/v2/domain/report/?domain=',
    'ip' => 'https://www.threatcrowd.org/searchApi/v2/ip/report/?ip=',
    'hash' => 'https://www.threatcrowd.org/searchApi/v2/file/report/?resource=',
    'antivirus' => 'https://www.threatcrowd.org/searchApi/v2/antivirus/report/?antivirus='
);



/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             数据库信息设置
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 设置相关数据库信息
 *  host    主机地址
 *  user    数据库用户名
 *  pwd     数据库密码
 *  dbname    数据库名称
 *  charset  编码设置，默认为UTF-8，不更改
 *
 */
$databaseinfo = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'pwd' => 'root',
    'dbname' => 'seardb',
    'charset' => 'utf8mb4'
);


/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *          数据库数据更新间隔时间设置
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 设置数据库数据更新间隔时间
 *  单位为秒，默认为30天 24*60*60
 *
 */
$curr_interval_update_time = '10';



/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *        设置系统名称
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 设置系统名称
 *
 */
$config['cur_sys_title'] = '批量信息解析系统';

?>