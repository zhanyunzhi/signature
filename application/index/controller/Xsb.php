<?php
namespace app\index\controller;
use think\Db;
use my\ErrorCode;


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
                return_result($message, ErrorCode::$DATABASE_SEARCH_ERROR, $returnData);
            }else{
                $returnData = $data;
                return_result($message, ErrorCode::$SUCCESS, $returnData);
            }
        }catch(\Exception $e){
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, ErrorCode::$DATABASE_SYS_ERROR, $returnData);
        }
        return_result('success', ErrorCode::$SUCCESS, $returnData);
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
            return_result($message, ErrorCode::$MISSING_PARAMETER_ERROR, $returnData);
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
                return_result($message, ErrorCode::$DATABASE_SEARCH_ERROR, $returnData);
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
                return_result($message, ErrorCode::$SUCCESS, $returnData);
            }
        }catch(\Exception $e){
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, ErrorCode::$DATABASE_SYS_ERROR, $returnData);
        }
        return_result('success', ErrorCode::$SUCCESS, $returnData);
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
            return_result($message, ErrorCode::$MISSING_PARAMETER_ERROR, $returnData);
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
                return_result($message, ErrorCode::$DATABASE_SEARCH_ERROR, $returnData);
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
                return_result($message, ErrorCode::$SUCCESS, $returnData);
            }
        }catch(\Exception $e){
            $message = '获取数据失败';
            $returnData = array();
            return_result($message, ErrorCode::$DATABASE_SYS_ERROR, $returnData);
        }
        return_result('success', ErrorCode::$SUCCESS, $returnData);
    }
    /*
     * 登录
     *
     * return：session3rd本地服务器与小程序之间的登录session
     * */
    public function wx_login() {
//        $this->check_login();
        $appid = config('appid');   //小程序的appid
        $secret = config('secret');  //小程序的secret
        $code = input("code", '', 'htmlspecialchars_decode');
        $rawData = input("rawData", '', 'htmlspecialchars_decode');
        $signature = input("signature", '', 'htmlspecialchars_decode');
        $encryptedData = input("encryptedData", '', 'htmlspecialchars_decode');
        $iv = input("iv", '', 'htmlspecialchars_decode');
        /**
         * 4.server调用微信提供的jsoncode2session接口获取openid, session_key, 调用失败应给予客户端反馈
         * , 微信侧返回错误则可判断为恶意请求, 可以不返回. 微信文档链接
         * 这是一个 HTTP 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。其中 session_key 是对用户数据进行加密签名的密钥。
         * 为了自身应用安全，session_key 不应该在网络上传输。
         * 接口地址："https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code"
         */
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);        // 设置是否输出header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 设置是否输出结果
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 设置是否检查服务器端的证书
        $urlDatas = json_decode(curl_exec($ch),true);
        curl_close($ch);
        $returnData = array();
        if(!isset($urlDatas)) return_result('request session_key failed', ErrorCode::$GET_WX_LOGIN_SESSION_KEY_ERROR, $returnData);
        $session_key = $urlDatas['session_key'];
        $openid = $urlDatas['openid'];
        /**
         * 5.server计算signature, 并与小程序传入的signature比较, 校验signature的合法性, 不匹配则返回signature不匹配的错误. 不匹配的场景可判断为恶意请求, 可以不返回.
         * 通过调用接口（如 wx.getUserInfo）获取敏感数据时，接口会同时返回 rawData、signature，其中 signature = sha1( rawData + session_key )
         *
         * 将 signature、rawData、以及用户登录态发送给开发者服务器，开发者在数据库中找到该用户对应的 session-key
         * ，使用相同的算法计算出签名 signature2 ，比对 signature 与 signature2 即可校验数据的可信度。
         */
        $signature2 = sha1($rawData . $session_key);
        if ($signature2 !== $signature) return_result('signature not match'.$session_key, ErrorCode::$WX_LOGIN_SIGNATURE_ERROR, $returnData);
        /**
         *
         * 6.使用第4步返回的session_key解密encryptData, 将解得的信息与rawData中信息进行比较, 需要完全匹配,
         * 解得的信息中也包括openid, 也需要与第4步返回的openid匹配. 解密失败或不匹配应该返回客户相应错误.
         * （使用官方提供的方法即可）
         */
        $pc = new \wx\WXBizDataCrypt($appid, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode !== 0) {
            return_result('encryptData not match'.$errCode, ErrorCode::$WX_LOGIN_ENCRYPT_DATA_ERROR, $returnData);
        }
        /**
         * 7.生成第三方3rd_session，用于第三方服务器和小程序之间做登录态校验。为了保证安全性，3rd_session应该满足：
         * a.长度足够长。建议有2^128种组合，即长度为16B
         * b.避免使用srand（当前时间）然后rand()的方法，而是采用操作系统提供的真正随机数机制，比如Linux下面读取/dev/urandom设备
         * c.设置一定有效时间，对于过期的3rd_session视为不合法
         *
         * 以 $session3rd 为key，sessionKey+openId为value，写入memcached
         */
        $data = json_decode($data, true);
        if($openid !== $data['openId']) return_result('openid not match'.$session_key, ErrorCode::$WX_LOGIN_VERIFY_ERROR, $returnData);      //openid验证
        if($time=intval(time()) - $data['watermark']['timestamp'] > 7200) return_result('openid is expire'.$session_key, ErrorCode::$WX_LOGIN_VERIFY_ERROR, $returnData);  //时间戳时效性验证，2个小时
        $session3rd = randomFromDev(16);        //生成16位随机数
        cache($session3rd, $session_key . $data['openId'], 3600);       //将session_key和openId存入cache中
        $timeNow = date('Y-m-d H:i:s',time());      //获取当前时间
        $user = Db::table("user")               //查询数据是否已经存在
            ->where('openId','eq',$data['openId'])
            ->field('id')
            ->find();
        if(!$user){         //新增需要插入到数据库的数据集
            $sql_data = ['openId' => $data['openId'], 'nickName' => $data['nickName'], 'gender' => $data['gender'], 'city' => $data['city'], 'province' => $data['province'], 'country' => $data['country'], 'avatarUrl' => $data['avatarUrl'], 'language' => $data['language'], 'add_time' => $timeNow ];
            Db::table('user')->insert($sql_data);            //插入数据库
        }else{          //更新
            $sql_data = ['openId' => $data['openId'], 'nickName' => $data['nickName'], 'gender' => $data['gender'], 'city' => $data['city'], 'province' => $data['province'], 'country' => $data['country'], 'avatarUrl' => $data['avatarUrl'], 'language' => $data['language'], 'edit_time' => $timeNow ];
            Db::table('user')
                ->where('openId','eq',$data['openId'])
                ->inc('visit_count')                //统计访问次数
                ->update($sql_data);
        }
        $returnData = array('token' => $session3rd);
        return_result('success', ErrorCode::$SUCCESS, $returnData);
    }
    /*
     * 检查登录状态
     *
     * return：返回openId
     * */
    public function check_login() {
        $session3rd = request()->header()['token'];         //获取header中的token
        $openid_session_key = cache($session3rd);
        $returnData = array();
        if(!$openid_session_key) return_result('session invalid', ErrorCode::$LOGIN_VERIFY, $returnData);          //服务器中的$session3rd不存在或者失效
//        return substr($openid_session_key, 0,24);
        return substr($openid_session_key, 24);         //返回openId
    }
    /*
     * 增加收藏
     * return：成功或者失败
     * */
    public function collect_add() {
        $openId = $this->check_login();
        $post_data = request()->param();         //获取post过来的参数
        $qid = isset($post_data['qid']) ? $post_data['qid'] : '';
        $timeNow = date('Y-m-d H:i:s',time());      //获取当前时间
        $result = '';       //数据库操作的结果
        $returnData = array();
        $collect = Db::table("collect")               //查询数据是否已经存在
            ->where('openId','eq',$openId)
            ->field('id,qids')
            ->find();
        if($collect){
            if(strpos($collect['qids'],strval($qid)) === false){                    //qid不在数据库中的收藏记录中了，也就是还没收藏过
                $sql_data = [ 'qids' => $collect['qids'].','.$qid, 'edit_time' => $timeNow ];
                $result = Db::table('collect')          //更新数据库
                    ->where('openId','eq',$openId)
                    ->inc('collect_count')                //统计操作次数
                    ->update($sql_data);
                if($result > 0) {
                    return_result('success', ErrorCode::$SUCCESS, $returnData);
                }else{
                    return_result('收藏失败', ErrorCode::$HANDLE_ERROR, $returnData);
                };
            }else{          //qid已经在数据库中的收藏记录中了，也就是已经收藏过了
                return_result('已收藏', ErrorCode::$REPETITION_HANDLE_ERROR, $returnData);
            }
        }else{          //说明用户是第一次收藏题目，新增一条记录
            $sql_data = ['openId' => $openId, 'qids' => ','.$qid, 'add_time' => $timeNow ];
            $result = Db::table('collect')->insert($sql_data);            //插入数据库
            if($result > 0) {
                return_result('success', ErrorCode::$SUCCESS, $returnData);
            }else{
                return_result('收藏失败', ErrorCode::$HANDLE_ERROR, $returnData);
            };
        }
    }
    /*
     * 删除收藏
     * return：成功或者失败
     * */
    public function collect_delete() {
        $openId = $this->check_login();
        $post_data = request()->param();         //获取post过来的参数
        $qid = isset($post_data['qid']) ? $post_data['qid'] : '';
        $timeNow = date('Y-m-d H:i:s',time());      //获取当前时间
        $result = '';       //数据库操作的结果
        $returnData = array();
        $collect = Db::table("collect")               //查询数据是否已经存在
            ->where('openId','eq',$openId)
            ->field('id,qids')
            ->find();
        if($collect){
            if(strpos($collect['qids'],strval($qid)) === false){                    //qid不在数据库中的收藏记录中了，也就是还没收藏过，无法删除
                return_result('非法操作', ErrorCode::$ILLEGAL_HANDLE_ERROR, $returnData);
            }else{          //qid已经在数据库中的收藏记录中了，也就是已经收藏过了
                $collect['qids'] = str_replace(','.strval($qid),"",$collect['qids']);
                $sql_data = [ 'qids' => $collect['qids'], 'edit_time' => $timeNow ];
                $result = Db::table('collect')          //更新数据库
                ->where('openId','eq',$openId)
                    ->inc('collect_count')                //统计操作次数
                    ->update($sql_data);
                if($result > 0) {
                    return_result('success', ErrorCode::$SUCCESS, $returnData);
                }else{
                    return_result('收藏失败', ErrorCode::$HANDLE_ERROR, $returnData);
                };
            }
        }else{          //找不到记录，无法删除
            return_result('非法操作', ErrorCode::$ILLEGAL_HANDLE_ERROR, $returnData);
        }
    }
}
