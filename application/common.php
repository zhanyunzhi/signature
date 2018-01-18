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