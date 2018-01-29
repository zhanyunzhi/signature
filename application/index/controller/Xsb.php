<?php
namespace app\index\controller;
use think\Db;

class Xsb
{
    public function practice_chapter_list()
    {
        $returnData = array();
        $message = 'success';
        try{
            $data = Db::table("chapter")
                ->field('id as cid,title,content')
                ->select();
            if(empty($data)){
                $message = 'fail';
                $returnData = array();
                return_result($message, '100001', $returnData);
            }else{
                $returnData = $data;
                return_result($message, '000000', $returnData);
            }
        }catch(\Exception $e){
            $message = '服务器暂时无法验证您的信息';
            $returnData = array();
            return_result($message, '200001', $returnData);
        }
        return_result('success', '000000', $returnData);
    }
}
