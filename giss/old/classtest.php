<?php
class tidy_get_status
{
  function getContent(){
    require "./config.php";

    //$textReq = "これはクラスの文章だよーん";
    $textReq = $config['pass'];
    return $textReq;
  }

  function getConfig($conf_id, $url){
    $testConfUrl = $conf_id.$url;
    return $testConfUrl;
  }
}
?>
