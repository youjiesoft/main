<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of tmlCrypt
 *
 * @author mashihe
 */
class tmlCrypt extends Think{
    var $key;
    function tmlCrypt($key)   
    {         
        $this->key = $key;
    }
    //解密字符
    public function decrypt($encrypted)  
    {         
       $encrypted = base64_decode($encrypted);  
       $key =$this->key;  
       $td = mcrypt_module_open('des','','ecb','');   
       $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);  
       $ks = mcrypt_enc_get_key_size($td);  
       @mcrypt_generic_init($td, $key, $iv);  
       //初始处理                 
       $decrypted = mdecrypt_generic($td, $encrypted);  
       //解密  
       mcrypt_generic_deinit($td);  
       //结束               
       mcrypt_module_close($td);  
       $y=$this->pkcs5_unpad($decrypted);  
       return $y;     
    }
    //反派生
    private function pkcs5_unpad($text)   
    {         
       $pad = ord($text{strlen($text)-1});  
       if ($pad > strlen($text))  
       {
           return false;  
       }  
       if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)  
       {  
          return false;  
       }
       return substr($text, 0, -1 * $pad);  
    }
}
?>
