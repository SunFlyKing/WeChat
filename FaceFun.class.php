<?php
/**
 * Created by PhpStorm.
 * User: DeLL
 * Date: 2017/10/12
 * Time: 18:53
 */
class FaceFun{
    public $server          = 'https://api-cn.faceplusplus.com/facepp/v3';

    public $api_key;         //调用此API的API Key
    public $api_secret;      //调用此API的API Secret
    public $faceset_token;     //FaceSet的标识
    public $outer_id;    //用户提供的FaceSet标识
    public $check_empty;      //删除时是否检查FaceSet中是否存在face_token，默认值为10：不检查1：检查如果设置为1，当FaceSet中存在face_token则不能删除
    public $display_name;      //人脸集合的名字
    public $tags;     //FaceSet自定义标签组成的字符串，用来对FaceSet分组
    public $face_tokens;    //。最多不超过5个face_token可选	user_da
    public $image_file;      //目标人脸所在的图片，二进制文件，需要用 post multipart/form-data 的方式上传。
    public $return_landmark;   //是否检测并返回人脸关键点。合法值为：1	检测0	不检测注：本参数默认值为 0
    public $return_attributes;     //是否检测并返回根据人脸特征判断出的年龄、性别、情绪等属性。

    public function __construct($params)
    {
        $this->api_key = !empty($params['api_key'])  ? $params['api_key'] : '';
        $this->api_secret = !empty($params['api_secret'])  ? $params['api_secret'] : '';
        $this->faceset_token = !empty($params['faceset_token'])  ? $params['faceset_token'] : '';
        $this->outer_id = !empty($params['outer_id'])  ? $params['outer_id'] : '';
        $this->check_empty = !empty($params['check_empty'])  ? $params['check_empty'] : '';
        $this->display_name = !empty($params['display_name'])  ? $params['display_name'] : '';
        $this->tags = !empty($params['tags'])  ? $params['tags'] : '';
        $this->face_tokens = !empty($params['face_tokens'])  ? $params['face_tokens'] : '';
        $this->image_file = !empty($params['image_file'])  ? $params['image_file'] : '';
        $this->image_url = !empty($params['$image_url'])  ? $params['$image_url'] : '';
        $this->return_landmark = !empty($params['return_landmark'])  ? $params['return_landmark'] : '';
        $this->return_attributes = !empty($params['return_attributes'])  ? $params['return_attributes'] : '';
    }

    private function request($api_url, $data)
    {
        //初始化一个cURL会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);         // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);         // 从证书中检查SSL加密算法是否存在

        //设置请求选项
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER,0);

        //这是请求类型
        curl_setopt($ch, CURLOPT_POST, TRUE);

        //添加post数据到请求中
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        return curl_exec($ch);
    }


    //判断密钥是否设置
    private function apiPropertiesAreSet()
    {
        if( ! $this->api_key) {
            return false;
        }
        if( ! $this->api_secret) {
            return false;
        }
        return true;
    }


    /**
     * @param $method - The Face++ API
     * @param array $params - Request Parameters
     * @return array - {'http_code':'Http Status Code', 'request_url':'Http Request URL','body':' JSON Response'}
     * @throws Exception
     */
    public function APIFunction($method, array $params)
    {
        if( ! $this->apiPropertiesAreSet()) {
            throw new Exception('API properties are not set');
        }
        $params['api_key']      = $this->api_key;
        $params['api_secret']   = $this->api_secret;
        return $this->request("{$this->server}{$method}",$params);
    }

    public function GetDetailFaceSet(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'outer_id' =>$this->outer_id
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res = $this->APIFunction('/faceset/getdetail',$data);
        return  json_decode($res,true);

    }

    public function FaceSetDelete(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'outer_id' =>$this->outer_id,
            'check_empty' =>$this->check_empty
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res =$this->APIFunction('/faceset/delete',$data);
        return  json_decode($res,true);
    }

    public function FaceSetCreate(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'outer_id' =>$this->outer_id,
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res =$this->APIFunction('/faceset/create',$data);
        return  json_decode($res,true);
    }

    public function FaceDetect(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'return_landmark'=>$this->return_landmark,
            'return_attributes'=>$this->return_attributes,
            'outer_id' =>$this->outer_id,
            'image_file";filename="image' =>$this->image_file
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res =$this->APIFunction('/detect',$data);
        return  json_decode($res,true);
    }

    public function FaceSetAddFace(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'outer_id' =>$this->outer_id,
            'face_tokens' =>$this->face_tokens
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res =$this->APIFunction('/faceset/addface',$data);
        return  json_decode($res,true);
    }


    public function FaceSearch(){
        $data = array(
            'api_key' =>$this->api_key,
            'api_secret' =>$this->api_secret,
            'outer_id' =>$this->outer_id,
            'image_file";filename="image' =>$this->image_file,
//            'image_url'=>$this->image_url
//            'faceset_token' =>$this->faceset_token  或者使用faceset_token
        );
        $res =$this->APIFunction('/search',$data);
        return  json_decode($res,true);
    }
}