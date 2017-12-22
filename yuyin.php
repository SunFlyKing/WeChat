<?php
/*
    坚强di理由
    http://blog.csdn.net/qczxl/
*/
$options = array(
    'token'=>'weixin', //填写你设定的key
    'encodingaeskey'=>'123456', //填写加密用的EncodingAESKey
    'appid'=>'wxb86c7455601c0779', //填写高级调用功能的app id
);
$weObj = new wechatCallbackapiTest($options);
switch($weObj->getValue('MsgType')) {
    case 'text':
        $content = $weObj->getValue('Content');
        //业务处理...
        break;
    case 'event':
        switch ($weObj->getValue('Event')) {
            case "subscribe":  // 关注
                if(!empty($weObj->getValue('EventKey'))) { // 二维码参数值
                    //扫描带参数二维码，进行关注后的事件推送
                }
                //业务处理...
                break;
            case "unsubscribe": //取消关注
                //业务处理...
                break;
            case "SCAN": //扫描带参数二维码（用户已关注时的事件推送）
                $content = $weObj->getValue('EventKey');  // 二维码参数值
                //业务处理...
                break;
            case "CLICK": //菜单 - 点击菜单拉取消息
                $content = $weObj->getValue('EventKey');  // 设置的关键字
                //业务处理...
                break;
            case "LOCATION": //上报地理位置
                $lat = $weObj->getValue('Latitude');  //地理位置纬度
                $lng = $weObj->getValue('Longitude'); //地理位置经度
                //业务处理...
                break;
            case "VIEW": //菜单 - 点击菜单跳转链接
                $content = $weObj->getValue('EventKey'); // 跳转链接
                //业务处理...
                break;
            default:
                //业务处理...
                break;
        }
        break;
    case 'image':
        break;
    case 'location':
        break;
    case 'voice':$this->voice('7Ft2g8Yx92qk1cZh4lw_B365kwNE7zHm2NHUE4wx33d');
        break;
    case 'video':
        break;
    case 'link':
        break;
    default:
        echo '';
        break;
}

class wechatCallbackapiTest {

    private $token;
    private $encodingAesKey;
    private $encrypt_type;
    private $appid;
    private $postarr;
    private $item = '';

    public function __construct($options) {
        $this->token          = $options['token'];
        $this->encodingAesKey = $options['encodingaeskey'];
        $this->appid          = $options['appid'];
        $this->valid();
    }

    protected function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = sha1( implode( $tmpArr ) );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function valid() {
        $encryptStr="";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postStr = file_get_contents("php://input");
            $array   = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
            if($this->encrypt_type == 'aes') {
                $pc     = new Prpcrypt($this->encodingAesKey);
                $array  = $pc->decrypt($array['Encrypt'],$this->appid);
                if (!isset($array[0]) || ($array[0] != 0)) {
                    echo '';exit;
                }
                $this->postarr = (array)simplexml_load_string($array[1], 'SimpleXMLElement', LIBXML_NOCDATA); //解析XML
            } else {
                $this->postarr = $array;
            }
        } elseif (isset($_GET["echostr"])) {
            if($this->checkSignature()) {
                echo $_GET["echostr"];
                exit;
            }
        }
    }

    public function getValue($key) {
        if(isset($this->postarr[$key])) {
            return $this->postarr[$key];
        }
        return '';
    }

    public function setHeaderDate($type) {
        return array(
            'ToUserName'   => $this->getValue('FromUserName'),
            'FromUserName' => $this->getValue('ToUserName'),
            'CreateTime'   => time(),
            'MsgType'      => $type
        );
    }

    //回复文本消息
    public function text($text='') {
        $arr = array(
            'Content'      => $text
        );
        $this->sendReply(array_merge($this->setHeaderDate('text'),$arr));
    }

    /**
     * 回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *      "0"=>array(
     *          'Title'=>'msg title',
     *          'Description'=>'summary text',
     *          'PicUrl'=>'http://www.domain.com/1.jpg',
     *          'Url'=>'http://www.domain.com/1.html'
     *      ),
     *  )
     */
    public function news($newsData=array()) {
        $count = count($newsData);
        $arr   = array(
            'ArticleCount' => $count,
            'Articles'     => $newsData
        );
        $this->item = 'item';
        $this->sendReply(array_merge($this->setHeaderDate('news'),$arr));
    }

    /**
     * 回复图片
     * @param string $mediaid
     */
    public function image($mediaid='')
    {
        $arr = array(
            'Image'=>array('MediaId'=>$mediaid)
        );
        $this->sendReply(array_merge($this->setHeaderDate('image'),$arr));
    }

    /**
     * 回复语音
     * @param string $mediaid
     */
    public function voice($mediaid='')
    {
        $arr = array(
            'Voice'=>array('MediaId'=>$mediaid)
        );
        $this->sendReply(array_merge($this->setHeaderDate('voice'),$arr));
    }

    /**
     * 回复视频
     * @param string $mediaid
     */
    public function video($mediaid='',$title='',$description='')
    {

        $arr = array(
            'Video'=>array(
                'MediaId'=>$mediaid,
                'Title'=>$title,
                'Description'=>$description
            )
        );
        $this->sendReply(array_merge($this->setHeaderDate('video'),$arr));
    }

    /**
     * 回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function music($title,$desc,$musicurl,$hgmusicurl='',$thumbmediaid='') {
        $arr = array(
            'Music'=>array(
                'Title'=>$title,
                'Description'=>$desc,
                'MusicUrl'=>$musicurl,
                'HQMusicUrl'=>$hgmusicurl
            )
        );
        if ($thumbmediaid) {
            $arr['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $this->sendReply(array_merge($this->setHeaderDate('music'),$arr));

    }

    //发送回复代码
    public function sendReply($arr) {
        $xmldata = $this->setXml($arr);
        if ($this->encrypt_type == 'aes') {
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->appid);
            $ret = $array[0];
            if ($ret != 0) {
                echo '';exit;
            }
            $timestamp = time();
            $nonce = rand(77,999)*rand(605,888)*rand(11,99);
            $encrypt = $array[1];
            $tmpArr = array($this->token, $timestamp, $nonce, $encrypt);
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
        }
        $this->logger("T ".$xmldata);
        echo $xmldata;
    }

    /**
     * xml格式加密，仅请求为加密方式时再用
     */
    private function generate($encrypt, $signature, $timestamp, $nonce)
    {
        //格式化加密信息
        $format = "<xml>  
<Encrypt><![CDATA[%s]]></Encrypt>  
<MsgSignature><![CDATA[%s]]></MsgSignature>  
<TimeStamp>%s</TimeStamp>  
<Nonce><![CDATA[%s]]></Nonce>  
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }

    //数组组装xml
    protected function arrayToXml($arr) {
        $xml = '';
        foreach ($arr as $key => $val) {
            $key = is_numeric($key) ? $this->item : $key;
            $xml .= "<$key>";
            if(is_numeric($val)) {
                $xml .= "$val";
            } else {
                $xml .= is_array($val) ? $this->arrayToXml($val) : '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$val).']]>';
            }
            $xml .= "</$key>";
        }
        return $xml;
    }

    protected function setXml($arr) {
        return "<xml>".$this->arrayToXml($arr)."</xml>";
    }

    //日志记录
    private function logger($log_content)
    {
        $max_size = 10000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
    }

}

class PKCS7Encoder
{
    public static $block_size = 32;

    /**
     * 对需要加密的明文进行填充补位
     * @param $text 需要进行填充补位操作的明文
     * @return 补齐明文字符串
     */
    function encode($text)
    {
        $block_size = PKCS7Encoder::$block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = PKCS7Encoder::block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param decrypted 解密后的明文
     * @return 删除填充补位后的明文
     */
    function decode($text)
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > PKCS7Encoder::$block_size) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}

/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
    public $key;
    function __construct($k) {
        $this->key = base64_decode($k . "=");
    }
    /**
     * 兼容老版本php构造函数，不能在 __construct() 方法前边，否则报错
     */
    function Prpcrypt($k)
    {
        $this->key = base64_decode($k . "=");
    }
    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();//"aaaabbbbccccdddd";
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            // 网络字节序
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $pkc_encoder = new PKCS7Encoder;
            $text = $pkc_encoder->encode($text);
            mcrypt_generic_init($module, $this->key, $iv);
            //加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //          print(base64_encode($encrypted));
            //使用BASE64对加密后的字符串进行编码
            return array(ErrorCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            //print $e;
            return array(ErrorCode::$EncryptAESError, null);
        }
    }
    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {
        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            mcrypt_generic_init($module, $this->key, $iv);
            //解密
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array(ErrorCode::$DecryptAESError, null);
        }
        try {
            //去除补位字符
            $pkc_encoder = new PKCS7Encoder;
            $result = $pkc_encoder->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
            if (!$appid)
                $appid = $from_appid;
            //如果传入的appid是空的，则认为是订阅号，使用数据中提取出来的appid
        } catch (Exception $e) {
            //print $e;
            return array(ErrorCode::$IllegalBuffer, null);
        }
        if ($from_appid != $appid)
            return array(ErrorCode::$ValidateAppidError, null);
        //不注释上边两行，避免传入appid是错误的情况
        return array(0, $xml_content, $from_appid); //增加appid，为了解决后面加密回复消息的时候没有appid的订阅号会无法回复

    }
    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}
/**
 * error code
 * 仅用作类内部使用，不用于官方API接口的errCode码
 */
class ErrorCode
{
    public static $OK = 0;
    public static $ValidateSignatureError = 40001;
    public static $ParseXmlError = 40002;
    public static $ComputeSignatureError = 40003;
    public static $IllegalAesKey = 40004;
    public static $ValidateAppidError = 40005;
    public static $EncryptAESError = 40006;
    public static $DecryptAESError = 40007;
    public static $IllegalBuffer = 40008;
    public static $EncodeBase64Error = 40009;
    public static $DecodeBase64Error = 40010;
    public static $GenReturnXmlError = 40011;
    public static $errCode=array(
        '0' => '处理成功',
        '40001' => '校验签名失败',
        '40002' => '解析xml失败',
        '40003' => '计算签名失败',
        '40004' => '不合法的AESKey',
        '40005' => '校验AppID失败',
        '40006' => 'AES加密失败',
        '40007' => 'AES解密失败',
        '40008' => '公众平台发送的xml不合法',
        '40009' => 'Base64编码失败',
        '40010' => 'Base64解码失败',
        '40011' => '公众帐号生成回包xml失败'
    );
    public static function getErrText($err) {
        if (isset(self::$errCode[$err])) {
            return self::$errCode[$err];
        }else {
            return false;
        };
    }
}