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
 *
 * ip138     IP138.com免费公共接口    地址：http://www.ip138.com/api/
 *
 *
 */

$config['ip_api_info'] = array(
    'taobao' => 'http://ip.taobao.com/service/getIpInfo.php?ip=',
    'ipip' => 'http://freeapi.ipip.net/',
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
 *
 * ip138     IP138.com免费公共接口    地址：http://www.ip138.com/api/
 *
 * 缺省采用淘宝API，更换的话将 taobao 更改即可；
 *
 */

$config['cur_ip_api_cfg'] = 'taobao';

?>