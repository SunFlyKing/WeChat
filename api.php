<?php
/**
  * wechat php test
  */
//测试号
define('APPID','wxb86c7455601c0779');
define('APPSECRET','5d92f0f6778b6f185b603ab45f32354c');
//公众号
//define('APPID','wx673677d67f6f4999');
//define('APPSECRET','3f414073fc8f6171bedd4dbb49255a27');
//define your token
define("TOKEN", "weixin");
require './WeChatAPI.class.php';
$wechatObj = new WeChatAPI(APPID,APPSECRET,TOKEN);

$mune_tmpl ='{
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
                 "type":"miniprogram",
                 "name":"wxa",
                 "url":"http://mp.weixin.qq.com",
                 "appid":"wx286b93c14bbf93aa",
                 "pagepath":"pages/lunar/index"
             },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }';

/*|| !isset($_GET["signature"]) || !isset($_GET["timestamp"]) || !isset($_GET["nonce"])*/
if(!isset($_GET["echostr"]))
{
    $wechatObj->_createMenu($mune_tmpl);
    $wechatObj->responseMsg();

}else{
    $wechatObj->valid();
}
?>