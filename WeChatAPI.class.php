<?php
/**
 * Created by PhpStorm.
 * User: DeLL
 * Date: 2017/10/20
 * Time: 18:44
 */
require 'FaceFun.class.php';
require 'AipSpeech.php';
// 你的 APPID AK SK
const APP_ID = '10274511';
const API_KEY = 'XgH86R5MovlYjH0KrWQUWKCf';
const SECRET_KEY = '4lf1Ze1uZisMb8a6dMAmRoashG3zED9G ';
class WeChatAPI{
    private $_appid;
    private $_appsecret;
    private $_token;

    public function __construct($_appid , $_appsecret, $_token)
    {
        $this->_appid = $_appid;
        $this->_appsecret = $_appsecret;
        $this->_token = $_token;
    }

    public function responseMsg()
    {
        //注:微信官方demo是使用$GLOBALS,而我使用的是file_get_contents的形式,来获取转发过来的xml内容.具体php配置不同,应采用不同的形式,请大家注意下,不然后可能粉丝收不到你的公众号回复的消息.
//      $postStr = file_get_contents("php://input");
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if(!empty($postStr)){
            libxml_disable_entity_loader(true);
            //解析XML到对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = $postObj->MsgType;
            switch($msgType){
                case 'text':
                    $this->_doText($postObj);
                    break;
                case 'image':
                    $this->_doImage($postObj);
                    break;
                case 'voice':
                    $this->_doVoice($postObj);
                    break;
                case 'video':
                    $this->_doVideo($postObj);
                    break;
                case 'shortvideo':
                    $this->_doShortvideo($postObj);
                    break;
                case 'location':
                    $this->_doLocation($postObj);
                    break;
                case 'link':
                    $this->_doLink($postObj);
                    break;
                case 'event':
                    $this->_doEvent($postObj);
                    break;
                default:
                    echo "";
            }
        }
        else {
            echo "";
            exit;
        }

    }

    private function _doEvent($postObj){
        switch ($postObj->Event){

            case 'subscribe':
                $this->_dosubscribe($postObj);
                exit;
            case 'CLICK' :
                $this->_doClick($postObj);
                exit;

            case 'VIEW' :
                $this->_doView($postObj);
                exit;

            case 'scancode_push' :
                $this->_doSubscribe($postObj);
                exit;

            case 'scancode_waitmsg' :
                $this->_doSubscribe($postObj);
                exit;

            case 'pic_sysphoto' :
                $this->_doSubscribe($postObj);
                exit;

            case 'pic_photo_or_album' :
                $this->_doSubscribe($postObj);
                exit;

            case 'pic_weixin' :
                $this->_doSubscribe($postObj);
                exit;

            case 'location_select' :
                $this->_doSubscribe($postObj);
                exit;

            case 'view_limited' :
                $this->_doSubscribe($postObj);
                exit;

            case 'media_id' :
                $this->_doSubscribe($postObj);
                exit;
            default :
                echo "";
        }
    }

    private function _dosubscribe($postObj){
            //手机用户
            $fromUsername = $postObj->FromUserName;
            //公众平台
            $toUsername = $postObj->ToUserName;
            //接受数据类型
            $time = time();
            $contentStr = "我还是很喜欢你，"."\n"."像风走了八千里，不问归期."."\n"."我还是很喜欢你，"."\n"."像鲸鱼缺氧深海，乐此不疲."."\n"."我还是很喜欢你，"."\n"."像钗头凤搁下的最后一笔，相思成疾."."\n"."感谢您关注【sfx的demo】"."\n"."微信号：asami004";
        $this->_responseMsgText($fromUsername, $toUsername, $time, $contentStr);

    }
    private function _doText($postObj){

                //手机用户
                $fromUsername = $postObj->FromUserName;
                //公众平台
                $toUsername = $postObj->ToUserName;
                //接受关键字
                $keyword = trim($postObj->Content);
                //接受数据类型
                $time = time();
                //文本发送模板
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                //判断用户发送是否为空
                if(!empty( $keyword ))
                {
                    if($keyword == 'RNG'){
                        //设置回复类型为TEXT
                        $msgType = "text";
                        //设置回复内容
                        $contentStr = "RNG 必胜";
                        //格式化字符串  sprintf 格式化XML模板
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        //返回数据
                        echo $resultStr;
                    }else{
                        //设置回复类型为TEXT
                        $msgType = "text";
                        //url
                        $url = "http://www.tuling123.com/openapi/api?key=46f887daf9604aa5bcd454234dcfdd8b&info={$keyword}";
                        $str = file_get_contents($url);
                        $json = json_decode($str);
                        //设置回复内容
                        $contentStr = $json->text;
                        //格式化字符串  sprintf 格式化XML模板
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, strip_tags($contentStr));

                        //返回数据
                        echo $resultStr;
                    }

                }else{
                    echo "Input something...";
                }
    }

    private function _doImage($postObj){
        //获取图片相关信息进行处理
         $picURL = trim($postObj->PicUrl);
        //手机用户
         $fromUsername = $postObj->FromUserName;
        //公众平台
         $toUsername = $postObj->ToUserName;
        //时间
         $time = time();
        //类型
         $mediaID = trim($postObj->MediaId);


         if(!empty($picURL)){
             $curl = curl_init($picURL);
             curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
             $imageData = curl_exec($curl);
             $data = array(
                 'api_key'=>'8ltyJmxXl7eMlqhcPmeUnWYyqYzhRlH9',
                 'api_secret'=>'6nyv5OZOarKCAphvqMvQjhOPHSvznD3i',
                 'return_landmark'=>'1',
                 'return_attributes'=>'gender,age',
             );
             $_facepp = new FaceFun($data);
             $_facepp->outer_id = 'faceLogin';
             $_facepp->image_file="{$imageData}";
             $Detect_res = $_facepp->FaceDetect();
             $sex = $Detect_res['faces'][0]['attributes']['gender']['value'];
             $age = $Detect_res['faces'][0]['attributes']['age']['value'];

             $Search_res = $_facepp->FaceSearch();
             $face_token = $Search_res['results'][0]['face_token'];
             $confidence = $Search_res['results'][0]['confidence'];

             $row = $this->_getBasedata($face_token);
             $name = $row['name'];
             $agetrue = $row['age'];
             //文本发送模板
             $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

                     $msgType = "text";
                     //设置回复内容
                     $contentStr = "识别成功!"."\n"."姓名:{$name}"."\n"."实际年龄:{$agetrue}"."\n"."测试年龄:{$age}"."\n"."测试性别:{$sex}"."\n"."相似度:{$confidence}";
                     //格式化字符串  sprintf 格式化XML模板

                     $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                     //返回数据
                     echo $resultStr;
         }else{
            $resultStr = "Picture URL is empty.";
         }
   }

    private function _doVoice($postObj){
        //收到语音消息
        //手机用户
        $fromUsername = $postObj->FromUserName;
        //公众平台
        $toUsername = $postObj->ToUserName;

        //接受数据类型
        $time = time();
//        MediaId    语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
//        Format     语音格式，如amr，speex等
//        开通语音识别功能，用户每次发送语音给公众号时，微信会在推送的语音消息XML数据包中，增加一个Recongnition字段。
//        注：由于客户端缓存，开发者开启或者关闭语音识别功能，对新关注者立刻生效，对已关注用户需要24小时生效。开发者可以重新关注此帐号进行测试。
        $recognition = $postObj->Recognition;

        //url
        $url = "http://www.tuling123.com/openapi/api?key=46f887daf9604aa5bcd454234dcfdd8b&info={$recognition}";
        $str = file_get_contents($url);
        $json = json_decode($str);
        //设置回复内容
        $contentStr = $json->text;


//        $filepath =dirname(__FILE__)."/voice/speak.mp3";
//        $this->_speakdata($contentStr,$filepath);
//        $access_token = $this->_getAccessToken();
//        $res = $this->_datatoAMR($access_token,$filepath);
//        $mediaId = $res['media_id'];

        $aipSpeech = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);

        $result = $aipSpeech->synthesis($contentStr, 'zh', 1, array(
            'vol' => 5,
        ));

        $filepath =dirname(__FILE__)."/voice/speak.mp3";

//         识别正确返回语音二进制 错误则返回json 参照下面错误码
//        if(!is_array($result)){
//            if(file_exists($filepath))
//            {
//                unlink($filepath);
//                file_put_contents($filepath, $result);
//
//            }
//        }
        if(!is_array($result)){
            file_put_contents($filepath, $result);
        }

        $access_token = $this->_getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=voice";

        $filedata  = array("media" => "@$filepath");
        $result	= $this->_request($url,true,'POST',$filedata);
        $res = json_decode($result,true);
        $mediaId = $res['media_id'];
        $this->_responseVoice($fromUsername, $toUsername, $time, $mediaId);


    }
    private function _speakdata($contentStr,$filepath){
        $aipSpeech = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);

        $result = $aipSpeech->synthesis($contentStr, 'zh', 1, array(
        'vol' => 5,
        ));

            // 识别正确返回语音二进制 错误则返回json 参照下面错误码
        if(!is_array($result)){
            file_put_contents($filepath, $result);
        }
    }
    private function _datatoAMR($access_token,$filepath){
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=voice";
        $filedata  = array("media" => "@".$filepath);
        $result	= _request($url,true,'POST',$filedata);
        $res = json_decode($result,true);
        return $res;
    }
    private function _getBasedata($face_token){
        /*替换为你自己的数据库名*/
        $dbname = 'sunfly';
        /*填入数据库连接信息*/
        $host = '47.52.95.83';
        $port = 3306;
        $user = 'aaa';//用户AK
        $pwd = 'admin888';//用户SK
        /*以上信息都可以在数据库详情页查找到*/
        /*接着调用mysql_connect()连接服务器*/
        /*为了避免因MySQL数据库连接失败而导致程序异常中断，此处通过在mysql_connect()函数前添加@，来抑制错误信息，确保程序继续运行*/
        /*有关mysql_connect()函数的详细介绍，可参看http://php.net/manual/zh/function.mysql-connect.php*/
        $link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
        mysql_query("set names utf8");
        if(!$link) {
            die("Connect Server Failed: " . mysql_error());
        }
        /*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
        if(!mysql_select_db($dbname,$link)) {
            die("Select Database Failed: " . mysql_error($link));
        }

        $sql = "select name,age from facedata where face_token ='$face_token'";
        $RES = mysql_query($sql);
        $row = mysql_fetch_assoc($RES);
        return $row;
    }
    private function _responseMsgText($fromUsername, $toUsername, $time,  $contentStr){
        //文本发送模板
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        //设置回复类型为TEXT
        $msgType = "text";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, strip_tags($contentStr));

        //返回数据
        echo $resultStr;
    }
    private function _responseVoice($fromUsername, $toUsername, $time,  $mediaId){
        //语音发送模板
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        <Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                        </Voice>
                        </xml>";
        //设置回复类型为voice
//        $msgType = "voice";
        $resultStr = sprintf($textTpl, $fromUsername,$toUsername,$time,$mediaId);
        //返回数据
        echo $resultStr;
    }
    private function _doVideo($postObj){

    }

    private function _doShortvideo($postObj){

    }

    private function _doLocation($postObj){

    }

    private function _doLink($postObj){

    }


    private function _doClick($postObj){

    }

    private function _doView($postObj){

    }

    public function _createMenu($Menu_templ){
        $curl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->_getAccessToken();
        $content = json_decode($this->_request($curl, true, 'POST', $Menu_templ));
        if($content->errcode == 0){
            echo "菜单成功创建";
        }
    }

    public function _deleteMenu(){
        $curl = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->_getAccessToken();
        $content = json_decode($this->_request($curl,true,'GET'));
        if($content->errcode == 0)
            echo "菜单已删除";
    }

    public function _request($curl, $https = true, $method = 'GET', $data = null){
        $ch = curl_init(); // 初始化curl
//        curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false);//php5.6yihou

        curl_setopt($ch, CURLOPT_URL, $curl); //设置访问的 URL
        curl_setopt($ch, CURLOPT_HEADER, false); //放弃 URL 的头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而不直接输出
        if($https){ //判断是否是使用 https 协议
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不做服务器的验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  //做服务器的证书验证
        }
        if($method == 'POST'){ //是否是 POST 请求
            curl_setopt($ch, CURLOPT_POST, true); //设置为 POST 请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置POST的请求数据
        }
        $content = curl_exec($ch); //开始访问指定URL
        curl_close($ch);//关闭 cURL 释放资源
        return $content;
    }

    public function _getAccessToken(){ //获取Access Token
        $file = './accesstoken';	//设置Access Token的存放位置
        if(file_exists($file)){
            $content = file_get_contents($file); //读取文档
            $content = json_decode($content); //解析json数据
//            var_dump($content->expires_in);die;
            if(time() - filemtime($file) < ($content->expires_in)) //判断access token是否过期
                return $content->access_token;
        }
        $curl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_appsecret; //通过该 URL 获取Access Token
        $content = $this->_request($curl);  //发送请求
        file_put_contents($file, $content);//保存Access Token 到文件
        $content = json_decode($content); //解析json
        return $content->access_token;
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->_token;

//        if(!isset($signature) && !isset($timestamp) && !isset($nonce))
//            return false;
        //将所有参数的值进行字符串的字典序排序,拼接成一个字符串进行sha1加密.
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        return (sha1(implode($tmpArr)) == $signature) ? true : false;
    }

}