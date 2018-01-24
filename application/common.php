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
function return_result($info = '', $code = '0000', $data = array()) {
    $out['code'] = $code ?: '0';
    $out['info'] = $info ?: ($out['code'] ? 'error' : 'success');
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