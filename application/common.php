<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件    可以在controller里面直接调用
    /**
     * 比较标准的接口输出函数
     * @param string  $info 消息
     * @param integer $code 接口错误码，很关键的参数
     * @param array   $data 附加数据
     * @return array
     */
function return_result($message = '', $code = '000000', $data = array()) {
    $out['code'] = $code ?: '000000';
    $out['message'] = $message ?: ($out['code'] ? 'error' : 'success');
    $out['data'] = $data ?: array();
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin:*');
    echo json_encode($out, JSON_HEX_TAG);
    exit(0);
}

/**
 * 保存64位编码图片
 */

function saveBase64Image($base64_image_content){
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        //图片后缀
        $type = $result[2];
        if($type=='jpeg'){
            $type='jpg';
        }
        //保存位置--图片名
        $image_name=date('His').str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).".".$type;
        $image_url = 'uploads/signature/'.date('Ymd').'/'.$image_name;
        if(!is_dir(dirname('./'.$image_url))){
            if(!is_dir(dirname('uploads/'))) mkdir(dirname('uploads/'));
            if(!is_dir(dirname('uploads/signature/'))) mkdir(dirname('uploads/signature/'));
            if(!is_dir(dirname('uploads/signature/'.date('Ymd').'/'))) mkdir(dirname('uploads/signature/'.date('Ymd').'/'));
            mkdir(dirname('./'.$image_url));
            chmod(dirname('./'.$image_url), 0777);
            //umask($oldumask);
        }
        //解码
        $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
        if (file_put_contents('./'.$image_url, $decode)){
            $data['code']='0';
            $data['imageName']=$image_name;
            $data['image_url']=$image_url;
            $data['type']=$type;
            $data['msg']='保存成功！';
        }else{
            $data['code']='1';
            $data['imgageName']='';
            $data['image_url']='';
            $data['type']='';
            $data['msg']='图片保存失败！';
        }
    }else{
        $data['code']='1';
        $data['imgageName']='';
        $data['image_url']='';
        $data['type']='';
        $data['msg']='base64图片格式有误！';
    }
    return $data;
 }
/**
 * 发起网络请求，并返回获取到的数据
 */

function getUrlData($url,$method = 'GET',$param = array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($method == 'POST'){       // post数据
        $post_data = $param;
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);   // post的变量
    }
    $result = json_decode(curl_exec($ch),true);
    curl_close($ch);
    return $result;
 }

/**
 * 读取/dev/urandom获取随机数
 * @param $len
 * @return mixed|string
 */
function randomFromDev($len) {
    $fp = @fopen('/dev/urandom','rb');
    $result = '';
    if ($fp !== FALSE) {
        $result .= @fread($fp, $len);
        @fclose($fp);
    }
    else
    {
        //trigger_error('Can not open /dev/urandom.');
        $result = generate_password(16);
    }
    // convert from binary to string
    $result = base64_encode($result);
    // remove none url chars
    $result = strtr($result, '+/', '-_');

    return substr($result, 0, $len);
}

function generate_password( $length = 16 ) {
    // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $password = '';
    for ( $i = 0; $i < $length; $i++ ) {
    // 这里提供两种字符获取方式
    // 第一种是使用 substr 截取$chars中的任意一位字符；
    // 第二种是取字符数组 $chars 的任意元素
    // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}