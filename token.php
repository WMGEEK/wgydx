<?php
 header('Access-Control-Allow-Origin:*');
 header('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With, Authorization,security');
 header('Content-Type: application/json');

require_once "jssdk.php";
$jssdk = new JSSDK("wxb1c89411d5a74e49", "870d7f9268fa5a0b425c2beb7b3cf6d9");
$signPackage = $jssdk->GetSignPackage();
echo json_encode($signPackage);

?>
