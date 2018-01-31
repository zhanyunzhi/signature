<?php
namespace app\index\controller;
use think\Db;

class Xsb
{
    //获取章节列表的标题和简介
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
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, '200001', $returnData);
        }
        return_result('success', '000000', $returnData);
    }
    /*
     * 返回练习题
     * param：cid（章节id），page（页数），qno（获取的数据的开始题号），size（一次获取的记录数量），type（获取的题目类型，0单选；1多选；2判断）
     * return：对应数量的记录
     * */
    public function practice_questions_after()
    {
//        if (request()->isPost()) dump(request()->header());
//        if (request()->isPost()) echo request()->header()['token'];
        $post_data = request()->param();         //获取post过来的参数
        $type = isset($post_data['type']) ? $post_data['type'] : '';    //如果对应要求的参数存在，则取对应传过来的值，否则赋值为空字符串
        $cid = isset($post_data['cid']) ? $post_data['cid'] : '';
        $qno = isset($post_data['qno']) ? $post_data['qno'] : '';
        $page = empty($post_data['page']) ? 1 : $post_data['page'];
        $size = empty($post_data['size']) ? 10 : $post_data['size'];
        $returnData = array();
        $message = 'success';
        if($type === '' || $cid === '' || $qno ===''){            //判断必须要的参数是否为空字符串是的话返回对应提示
            $message = '缺少参数';
            return_result($message, '200002', $returnData);
        }
        try{
            $data = Db::table("topic")
                ->where('qno','egt',$qno)
                ->where('type','eq',$type)
                ->where('cid','eq',$cid)
                ->field('id as qid,analysis_content,cid,main_content,option_right,options_a,options_b,options_c,options_d,options_e,options_f,qno,type')
                ->limit(($page-1)*$size,$size)
                ->select();
            if(empty($data)){
                $message = 'fail';
                $returnData = array();
                return_result($message, '100001', $returnData);
            }else{
                $options = array();
                foreach($data as $k => $d){
                    foreach($d as $key => $value){
                        if($key === "options_a" && $value !== '') $options['A'] = $value;
                        if($key === "options_b" && $value !== '') $options['B'] = $value;
                        if($key === "options_c" && $value !== '') $options['C'] = $value;
                        if($key === "options_d" && $value !== '') $options['D'] = $value;
                        if($key === "options_e" && $value !== '') $options['E'] = $value;
                        if($key === "options_f" && $value !== '') $options['F'] = $value;
                    }
                    $data[$k]['options'] = $options;
                    $data[$k]['option_right'] = explode(',', $data[$k]['option_right']);   //将字符串分割后变成数组
                    unset($data[$k]['options_a']);
                    unset($data[$k]['options_b']);
                    unset($data[$k]['options_c']);
                    unset($data[$k]['options_d']);
                    unset($data[$k]['options_e']);
                    unset($data[$k]['options_f']);
                }
                $returnData = $data;
                return_result($message, '000000', $returnData);
            }
        }catch(\Exception $e){
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, '200001', $returnData);
        }
        return_result('success', '000000', $returnData);
    }
    /*
     * 返回练习题的题目列表清单
     * param：cid（章节id）
     * return：当前章节的所有题目列表清单
     * */
    public function practice_list()
    {
        /*Db::listen(function($sql, $time, $explain){
            // 记录SQL
            echo $sql. ' ['.$time.'s]';
            // 查看性能分析结果
            dump($explain);
        });*/
        $get_data = request()->param();         //获取post过来的参数
        $cid = isset($get_data['cid']) ? $get_data['cid'] : '';
        $returnData = array();
        $message = 'success';
        if($cid === ''){            //判断必须要的参数是否为空字符串是的话返回对应提示
            $message = '缺少参数';
            return_result($message, '200002', $returnData);
        }
        try{
            $data = Db::table("topic")
                ->where('cid','eq',$cid)
                ->field('id as qid,type,qno')
                ->order('qno')
                ->select();
            $chapter = Db::table("chapter")
                ->where('id','eq',$cid)
                ->field('title,content')
                ->find();
            if(empty($data)){
                $message = 'fail';
                $returnData = array();
                return_result($message, '100001', $returnData);
            }else{
                $data_filter = array();
                $data_filter[0] = array();
                $data_filter[1] = array();
                $data_filter[2] = array();
                foreach ($data as $d){
                    if($d['type'] === 0) array_push($data_filter[0],$d);
                    if($d['type'] === 1) array_push($data_filter[1],$d);
                    if($d['type'] === 2) array_push($data_filter[2],$d);
                };
                $returnData['chapter_title'] = $chapter['title']." ".$chapter['content'];
                $returnData['list'] = $data_filter;
                return_result($message, '000000', $returnData);
            }
        }catch(\Exception $e){
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, '200001', $returnData);
        }
        return_result('success', '000000', $returnData);
    }
    /*
     * 登录
     * param：cid（章节id）
     * return：当前章节的所有题目列表清单
     * */
    public function login()
    {
        $returnData = array();
        return_result('success', '000000', $returnData);
    }
}
