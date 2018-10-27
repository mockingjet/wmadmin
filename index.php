<?php
header("Content-Type:text/html; charset=utf-8");
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();

//資料庫設定
foreach (glob("model/*.php") as $filename) {
  require($filename);
}
//後台功能
foreach (glob("library/*.php") as $filename) {
  require($filename);
}

DB::connect("ADISON");
DB::createTB(DB::TBArray());
DB::autoCreateMaster("WM","wmadmin","qwe");

// print_r(DB::get(Maingrp));
require('router.php');
// echo json_encode(Maingrp(), JSON_UNESCAPED_UNICODE);
