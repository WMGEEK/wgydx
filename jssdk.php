<?php
class JSSDK {
  private $appId;
  private $appSecret;
 
  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }
 
  public function getSignPackage() {
    
    
    $urls = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode(file_get_contents($urls),true);
      $access_token = $res['access_token'];
    
    $urls = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$access_token";
      $res = json_decode(file_get_contents($urls),true);
      $ticket = $res['ticket'];
 
    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = $_POST['url'];
 
    $urls = file_get_contents("php://input");
    $urlq = json_decode($urls,true);
    $url = $urlq['url'];
    //print_r($url);
 
    $timestamp = time();
    $nonceStr = $this->createNonceStr();
 
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
 
    $signature = sha1($string);
 
    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage;
  }
 
  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }
 
  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
  
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode(file_get_contents($url),true);
      $ticket = $res['ticket'];
 
 
    return $ticket;
  }
 
  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例

      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode(file_get_contents($url),true);
      $access_token = $res['access_token'];
      return $res;
  }
 
    private function httpGet($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证SSL证书

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("HTTP GET request failed: $error");
        }

        curl_close($ch);
        return $response;
    }

 
  private function get_php_file($filename) {
    return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
}