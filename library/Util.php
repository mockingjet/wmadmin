<?php
    
class Util
{
    public $key = ""; //密钥 要与java的转化成的16进制字符串对应

    function __construct()  
    {  
        if ($_SESSION['thekey']==null) {  
            echo 'thekey is not valid';  
            exit();  
        }  
        $this->key =$_SESSION['thekey']; 
    } 
    //数据加密
    function encrypt($input)
    {
        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = str_pad($this->key,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = str_replace("+","%2B",base64_encode($data));
        return $data;
    }
    //数据解密
    function decrypt($encrypted)
    {
        $encrypted = str_replace("%2B","+",base64_decode($encrypted));
        $key = str_pad($this->key,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }
    
    function pkcs5_pad ($text, $blocksize) 
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    
    function pkcs5_unpad($text)
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
    
    static function MakeRandomcode($len,$randType)
    {
      $randstr = explode(",",Util::getDataByArrayName("randType",$randType));
      for($i = 0 ; $i < $len ; $i++){
        $rand = rand(0,count($randstr) - 1);
        $result[] = $randstr[$rand];
      }
      return implode("",$result);
    }
  
    static function getDataByArrayName($GName,$GKey)
    {
      $randType["EngANDNum-UL"] = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,M,N,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
      $randType["EngANDNum-U"] = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,M,N,P,Q,R,S,T,U,V,W,X,Y,Z";
      $randType["EngANDNum-L"] = "0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
      $randType["Num"] = "0,1,2,3,4,5,6,7,8,9";
      $randType["Eng-U"] = "A,B,C,D,E,F,G,H,I,J,K,M,N,P,Q,R,S,T,U,V,W,X,Y,Z";
      $randType["Eng-L"] = "a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
      
      $mkName = $$GName;
      $feedback = !$GKey ? $mkName : $mkName[$GKey];
      
      return $feedback;
    }    
}