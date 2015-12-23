<?php
/**
 * 接口请求验证

 * @author commanderYao <30822252@qq.com>
 */
class RI{
	private $key="yangxi";
    /**
     * @param string      $examineString    提交的数组
     * @param string|null $secretKey    安全字校验字符串  define('EASY_SECRET_CODE', "123456abcdEFG");E点通 开放接口提供此常量
     * @param bool        $verify T/F    是否跳过验证过程
     *
     * @return 原json对象
     */
    public static function decode($examineString,$secretKey=null,$verify=true){
       $tks=explode('.',$examineString);
         if (count($tks)!=3){//非3段结构
            //throw new UnexpectedValueException('段落数目错误！');
			return -1;
        }
        list($headb64,$examloadb64,$cryptob64)=$tks;
        if (null===($header=RI::jsonDecode(RI::safeB64Decode($headb64)))){
            //throw new UnexpectedValueException('无效的段落编码！');
			return -2;
        }
        if (null === $examineString = RI::jsonDecode(RI::safeB64Decode($examloadb64))){
            //throw new UnexpectedValueException('无效的段落编码！');
			return -3;
        }
        $sig = RI::safeB64Decode($cryptob64);
       if ($verify){
            if (empty($header->alg)){
                //throw new DomainException('算法为空！');
				return -4;
            }
            if ($sig!=RI::sign($headb64.'.'.$examloadb64,$secretKey,$header->alg)){
                //throw new UnexpectedValueException('签名验证失败！');
				return -5;
            }
        }
         return $examineString;
    }

    /**
     * @param object|array $examineArray PHP 对象或 array数组
     * @param string       $secretKey    安全字校验字符串
     * @param string       $algo    数字签名算法
     *
     * @return string  最终RI
     */
    public static function encode($examineArray,$key,$algo='HS256'){
    	$key=$this->key;
        $header=array('typ'=>'RI','alg'=>$algo);
        $segments=array();
        $segments[]=RI::safeB64Encode(RI::jsonEncode($header));
        $segments[]=RI::safeB64Encode(RI::jsonEncode($examineArray));
        $signing_input=implode('.',$segments);
        $signature=RI::sign($signing_input,$key,$algo);
        $segments[]=RI::safeB64Encode($signature);
		return implode('.',$segments);
    }
    /**
     * @param string $msg    签名信息
     * @param string $key    安全校验码
     * @param string $method 签名算法
     *
     * @return string 加密信息
     */
    public static function sign($msg,$key,$method='HS256'){ //我叫 mt   3种任选
    	$key=$this->key;
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])){
            //throw new DomainException('本算法不支持！');
			return -6;
        }
		return hash_hmac($methods[$method],$msg,$key,false);
		//return hash_hmac($methods[$method],$msg,$key,true);///the Fucking 哈希
    }

    /**
     * @param string $input JSON 串串
     *
     * @return object JSON字符串对象
     */
    public static function jsonDecode($input){
        $obj=json_decode($input);
        if (function_exists('json_last_error')&&$errno=json_last_error()){
            RI::handleJsonError($errno);
        }else if ($obj===null&&$input!=='null') {
            //throw new DomainException('非空输入造成空结果异常！');
			return -7;
        }
        return $obj;
    }

    /**
     * @param object|array $input PHP对象或者array数组
     *
     * @return string PHP对象的JSON值
     */
    public static function jsonEncode($input){
        $json=json_encode($input);
        if (function_exists('json_last_error')&&$errno=json_last_error()){
            RI::handleJsonError($errno);
        }
        else if ($json==='null'&&$input!==null) {
            //throw new DomainException('非空输入造成空结果异常！');
			return -7;
        }
        return $json;
    }

    /**
     * @param string $input 基于base64编码字符串
     *
     * @return string 解密后的字符串
     */
    public static function safeB64Decode($input){
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param string $input 任意串
     *
     * @return string  上边input被base64编译后的东东  数字签名
     */
    public static function safeB64Encode($input){
        return str_replace('=','',strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param int $errno json_last_error()函数返回的上一个错误编号
     *
     * @return void
     */
    private static function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => '超过最大对内存深度！',
            JSON_ERROR_CTRL_CHAR => '发现意外的控制字符串！',
            JSON_ERROR_SYNTAX => '语法错误，格式不合法！'
        );
        throw new DomainException(isset($messages[$errno])?$messages[$errno]:'一个未知错误！:'.$errnol);
    }

}

