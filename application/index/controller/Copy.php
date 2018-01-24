<?php
namespace app\index\controller;
use think\Db;

class Copy
{
    public function copyChapter() {             //获取章节列表数据
        $url = 'http://m.p5w.net/s_exam/practice/list';
        $urlDatas = getUrlData($url,'GET','');
        $urlDatas = $urlDatas['data'];      //url返回的data属性数据
        $length = count($urlDatas);     //数据长度
        $timeNow = date('Y-m-d H:i:s',time());
        $datas = array();       //批量写入的数组数据集
        $row = array();        //单个写入的数组数据
        for($i=0; $i<$length; $i++){
            $cid = $urlDatas[$i]['cid'];
            $title = explode(" ",$urlDatas[$i]['title'])[0];
            $content = explode(" ",$urlDatas[$i]['title'])[1];
            $row = ['id' => $cid, 'title' => $title, 'content' => $content, 'edit_time' => $timeNow];
            array_push($datas,$row);
        }
//        dump($datas);
        try{
            $returnCount = Db::name('chapter')->insertAll($datas);
            if($returnCount){
                $returnData = array('msg'=>'成功添加数据');
                return_result('success', '0000', $returnData);
            }else{
                $returnData = array('msg'=>'添加失败');
                return_result('info', '1000', $returnData);
            }
        }catch(\Exception $e){
            $returnData = array('msg'=>'服务器错误');
            return_result('error', '2000', $returnData);
        }
        echo $returnData;
     }

    public function copyTopic() {             //获取题目数据
        $cid = empty($_GET['cid']) ? 1 : $_GET['cid'];      //章节id
        $page = empty($_GET['page']) ? 1 : $_GET['page'];   //页数
        $qno = empty($_GET['qno']) ? 1 : $_GET['qno'];      //题目qno
        $size = empty($_GET['size']) ? 10 : $_GET['size'];  //一页的数据数量
        $type = empty($_GET['type']) ? 0 : $_GET['type'];   //类型
        $url = 'http://m.p5w.net/s_exam/practice/chapter/questions_after';
        $param = array('cid'=>$cid,'page'=>$page,'qno'=>$qno,'size'=>$size,'type'=>$type);
        $urlDatas = getUrlData($url,'POST',$param);
        $urlDatas = $urlDatas['data'];      //url返回的data属性数据
        $length = count($urlDatas);     //数据长度
        $timeNow = date('Y-m-d H:i:s',time());
        $datas = array();       //批量写入的数组数据集
        $row = array();        //单个写入的数组数据
        for($i=0; $i<$length; $i++){
            $qid = $urlDatas[$i]['qid'];
            $cid = $urlDatas[$i]['cid'];
            $qno = $urlDatas[$i]['qno'];
            $type = $urlDatas[$i]['type'];
            $main_content = $urlDatas[$i]['main_content'];
            $options_a = empty($urlDatas[$i]['options']['A']) ? '' : $urlDatas[$i]['options']['A'];
            $options_b = empty($urlDatas[$i]['options']['B']) ? '' : $urlDatas[$i]['options']['B'];
            $options_c = empty($urlDatas[$i]['options']['C']) ? '' : $urlDatas[$i]['options']['C'];
            $options_d = empty($urlDatas[$i]['options']['D']) ? '' : $urlDatas[$i]['options']['D'];
            $options_e = empty($urlDatas[$i]['options']['E']) ? '' : $urlDatas[$i]['options']['E'];
            $options_f = empty($urlDatas[$i]['options']['F']) ? '' : $urlDatas[$i]['options']['F'];
            $option_right = is_array($urlDatas[$i]['option_right']) ? implode(",", $urlDatas[$i]['option_right']) : $urlDatas[$i]['option_right'];
            $analysis_content = $urlDatas[$i]['analysis_content'];
            $row = ['id' => $qid, 'cid' => $cid, 'qno' => $qno, 'type' => $type, 'main_content' => $main_content,
                'options_a' => $options_a, 'options_b' => $options_b, 'options_c' => $options_c, 'options_d' => $options_d,
                'options_e' => $options_e, 'options_f' => $options_f, 'option_right' => $option_right,
                'analysis_content' => $analysis_content,'edit_time' => $timeNow];
            array_push($datas,$row);
        }
//        dump($datas);
        try{
            $returnCount = Db::name('topic')->insertAll($datas);
            if($returnCount){
                $returnData = array('msg'=>'成功添加数据' . $returnCount);
                return_result('success', '0000', $returnData);
            }else{
                $returnData = array('msg'=>'添加失败');
                return_result('info', '1000', $returnData);
            }
        }catch(\Exception $e){
            $returnData = array('msg'=>'服务器错误');
            return_result('error', '2000', $returnData);
        }
        echo $returnData;
    }
}
