<?php

//加密解密
$_SESSION['thekey'] = $_SESSION['thekey'] ? $_SESSION['thekey'] : Util::MakeRandomcode(24,"EngANDNum-UL");
$des = new Util();  
$token = $des->decrypt($_GET['token']);
$func = $_GET['func'];
$tables = DB::TBArray();

switch($token)
{
  case "":
    $Query.= 
      !empty($_SESSION['useracc']) 
      ? Html::getHead().Html::getBody() 
      : Html::getHead().Html::Portal();
    break;
  
  case in_array($token,$tables):
    $Query= method_exists("data",$func) ? data::$func($token) : "無此頁面";
  break;    

  case "login":
    $Query.= Html::Loginact();
    break;

  case "logout":
    unset($_SESSION['useracc']);
    $Query="登出成功！";
    break;
  
  case "change":
    $_SESSION['num_item_page'] = $_GET['val'];
    break;

  case "searchbox":
    echo html::searchbox();
    break;

  default: 
    $Query="Oops!";
    break;
}

echo $Query;