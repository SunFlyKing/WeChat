<?php
/**
 * Created by PhpStorm.
 * User: DeLL
 * Date: 2017/10/21
 * Time: 20:40
 */
header("content-type:text/html;charset=utf8");
$face_token ='e3b80c7da6e974db8b191c45a055888a';
//$id =17;
$row = getdata($face_token);
echo "<pre>";
var_dump($row);
function getdata($face_token){
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
    $sql = "select `name`,`age` from facedata where face_token ='$face_token'";

    $RES = mysql_query($sql);
    $row = mysql_fetch_assoc($RES);
    return $row;
}


//$url = "http://b333.photo.store.qq.com/psb?/V11UqnZw1Ng0pn/J5x6DlyzZo0hP*TllDU2*8yEjH*hPFCzs7cpAKsGadE!/b/dEYdiMZpDwAA&bo=wAOAAgAAAAABB2E!&rf=viewer_4";
//
////$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=alkshduhalskfjasd;
//$curl = curl_init($url);
//curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
//$imageData = curl_exec($curl);
//var_dump($imageData);
//$readHandle = fopen($image_url,"rb");
//$image_info = getimagesize($image_url);
//$base64 = base64_encode(fread($readHandle, $image_info));
//            //关闭资源
//fclose($readHandle);
////$contents = fread($handle,filesize($image_url));
//var_dump($base64);
//$PSize = filesize($image_file);
//$picturedata = fread(fopen($image_file, "r"), $PSize);
//var_dump($image_file);
//define('APPID','wx673677d67f6f4999');
//define('APPSECRET','3f414073fc8f6171bedd4dbb49255a27');
//define your token
//define("TOKEN", "weixin");
//require './WeChatAPI.class.php';
//
//$wechatObj = new WeChatAPI(APPID,APPSECRET,TOKEN);
//$Menu_templ = '
//    {
//         "button":[
//         {
//              "type":"click",
//              "name":"宝宝故事",
//              "key":"BABY_STORY"
//          },
//          {
//              "type":"click",
//              "name":"宝妈笑话",
//              "key":"MUM_STORY"
//          },
//          {
//              "sub_button":[
//               {
//                   "type":"click",
//                   "name":"切水果",
//                   "key":"FRULTS"
//                },
//                {
//                    "type":"click",
//                    "name":"消消乐",
//                    "key":"MISS"
//                 }]
//          }]
//    }';
//$access_token= $wechatObj->_createMenu($Menu_templ);
//var_dump($access_token);