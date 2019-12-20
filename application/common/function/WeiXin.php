<?php
// +----------------------------------------------------------------------
// | Minishop [ Easy to handle for Micro businesses ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.qasl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: tangtanglove <dai_hang_love@126.com> <http://www.ixiaoquan.com>
// +----------------------------------------------------------------------

use think\Db;
use think\Cache;

/**
 * 系统公共库文件
 * 主要定义系统内与微信相关的函数
 */

/**
 * 用CODE换取用户的OPENID和【授权TOKEN】
 * @param string $APPID 公众号的APPID
 * @param string $SECRET 公众号的开发者密码
 * @param string $CODE 用户的CODE
 * @return array 用户的OPENID和【授权TOKEN】
 */
function get_user_openid($APPID, $SECRET, $CODE)
{
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $APPID  . '&secret=' . $SECRET . '&code=' . $CODE . '&grant_type=authorization_code';
    $info = json_decode(file_get_contents($url)); //通过code获取用户的 openId 
    $info = json_decode(json_encode($info), true); //返回的json数组转换成array数组
    if (isset($info->errcode)) {
        $data['error'] = $info->errcode;
        $data['errmsg'] = $info->errmsg;
    } else {
        $data = json_decode(json_encode($info), true); //返回的json数组转换成array数组
    }
    return $data;
}
/**
 * 获取公众号的【基础token】
 * @param   string $APPID       公众号的APPID
 * @param   string $SECRET      公众号的开发者密码
 * @return  string $token       公众号的基础token
 * @author  由于微信基础token有每日访问次数限制，所以必须设立缓存机制
 */
function get_access_token($APPID, $SECRET)
{
    $access_token = Cache::get("access_token");
    if ($access_token && !empty($access_token)) {
        $token = $access_token;
    } else {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $APPID . "&secret=" . $SECRET;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //要访问的地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //跳过证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        $data = json_decode(curl_exec($ch));
        if (isset($data->access_token) && !empty($data->access_token)) {
            Cache::set("access_token", $data->access_token, 7000);
        }
        $token = $data->access_token;
    }
    return $token;
}
/**
 * 使用access_token获取用户信息
 * @param   string $ACCESS_TOKEN    公众号的token(基础token或者授权token)
 * @param   string $OPENID          公众号的开发者密码
 * @param   string $type            在什么机制下获取
 * @return  array $data            用户的基本信息
 */
function get_user_info($ACCESS_TOKEN, $OPENID, $type = 'UnionID')
{
    if ($type == 'UnionID') {
        //UnionID机制下的获取用户信息
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $ACCESS_TOKEN . "&openid=" . $OPENID . "&lang=zh_CN";
    } else {
        //SNS机制下的获取用户信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $ACCESS_TOKEN . '&openid=' . $OPENID . '&lang=zh_CN';
    }
    $user = json_decode(file_get_contents($url));
    if (isset($user->errcode)) {
        $data['error'] = $user->errcode;
        $data['errmsg'] = $user->errmsg;
    } else {
        $data = json_decode(json_encode($user), true); //返回的json数组转换成array数组
    }
    return $data;
}
/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug 调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false)
{
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true); //开启后:函数执行成功时会返回执行的结果，失败时返回 
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if ($ssl) {
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2); /*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    // dump();
    $response = curl_exec($ci);
    // dump($response);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    $res = json_decode($response, true);
    return $response;
    // return array($http_code, $response,$requestinfo);
}
function getSignPackage($APPID, $SECRET, $actName)
{
    $url = "http://hq.oonekj.com" . $actName;

    $jsapiTicket = getjsapi_ticket($APPID, $SECRET);

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $url = explode('#', $url)[0];
    $timestamp = time();

    $nonceStr = createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=" . $jsapiTicket . "&noncestr=" . $nonceStr . "&timestamp=" . $timestamp . "&url=" . $url;
    $signature = sha1($string);

    $signPackage = array(
        "appId" => $APPID,
        "nonceStr" => $nonceStr,
        "timestamp" => $timestamp,
        "url" => $url,
        "signature" => $signature,
        "rawString" => $string,
        "jsapiTicket" => $jsapiTicket
    );

    return $signPackage;
}
//获取jsapi_ticket
function getjsapi_ticket($APPID, $SECRET)
{
    header("Content-type:text/html;charset=utf-8");
    //获取access_token
    $access_token = get_access_token($APPID, $SECRET);
    $ticket = Cache::get("ticket");
    if ($ticket && !empty($ticket)) {
        return $ticket;
    } else {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data, true);
        if ($data["errcode"] == "0") {
            Cache::set("ticket", $data["ticket"], 7000);
            return $data['ticket'];
        }
        return "";
    }
}
//生成16位随机码
function createNonceStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}
