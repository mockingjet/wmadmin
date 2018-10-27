<?php
//輸出json
class DB {

	static function get($Q) {
		$json =  file_get_contents("model/$Q.json");
		$array = json_decode($json,true);
		return $array;
	}

	static function System() {
		$Q['company'] = "亞帝昇生物科技";
		$Q['company_en'] = "ADISON BIOTECHNOLOGY";
		$Q['company_br'] = "亞帝昇";
		$Q['company_en_br'] = "ADISON";
		return $Q;
	}

	//資料表陣列
	static function TBArray(){		
		return array(
			"Admin",
			"Carousel",
			"Maingrp",
			"Product",
			"News",
			"Donation",
			"Question",
			"Contact",
			"Other",
		);
	}

	//預先創建資料表
	static function createTB(array $tables)
	{
		foreach($tables as $table)
		{
			if(!mysql_query("Select * from ".$table.""))
			{
				DB::create(DB::get($table),'id');
			}
		}
		return $tables;
	}

	//自動新增最高管理員
	static function autoCreateMaster($name,$account,$password)
	{
		$result=DB::select(DB::get('Admin'),"*","where account = '".$account."'");
		if(mysql_num_rows($result)<1)
		{
			$tbs = DB::TBArray();
			$tbskey=[];
			foreach($tbs as $key => $tb)
			{
				array_push($tbskey,$key);
			}
			$authgrp = implode("&",$tbskey);
			$value="null,'".$name."','".$account."','".$password."','".$authgrp."','1','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','0'";        
			$result = DB::insert(DB::get('Admin'),$value); 
		}
	}

	//資料庫連線
	static function connect($DB)
	{
		if(!mysql_connect("localhost","root",""))
		{
			$result=mysql_connect("localhost","root"," ");
			//$result=mysql_connect("localhost","root","nchu1002@#"); shiye
		}
		$result=mysql_query("set names utf8 ");
		$result=mysql_query(date_default_timezone_set("Asia/Taipei"));
		$result=mysql_select_db($DB);
		if(!$result)
		{
			$sql="Create database ".$DB." CHARACTER SET utf8 COLLATE utf8_general_ci";
			$result=mysql_query($sql);
			$result=mysql_select_db($DB);	
			if(!$result)
			{
				echo mysql_error();
			}	
		}	
	}

	static function create($Q,$value)
	{
		foreach($Q as $key => $val)
		{
			if(!is_numeric($key))
			{
				$cnameandtype[] = $key ." ". $val['datatype'];
			}
		}
		$query = "create table ".$Q["0"]." (".implode(",",$cnameandtype).",primary key(".$value."))";
		$result = mysql_query($query);
		if(!$result)
		{
			return mysql_error();
		}
		return $result;
	}
		
	static function insert($Q,$value)
	{
		foreach ($Q as $key => $val)
		{
			if(!is_numeric($key))
			{
				$cname[]=$key;	
			}		
		}
		$query="insert into ".$Q["0"]." (".implode(",",$cname).") values (".$value.")";
		$result = mysql_query($query);
		if(!$result)
		{
			return mysql_error();
		}
		return $result;
	}

	static function update($Q,$value,$where)
	{
		$result = mysql_query("update ".$Q["0"]." set ".$value." ".$where);
		if(!$result)
		{
			return mysql_error();
		}
		return $result;
	}
		
	static function delete($Q,$where)
	{
		$result = mysql_query("delete from ".$Q["0"]." ".$where);
		if(!$result)
		{
			return mysql_error();
		}
		return $result;
	}
		
	static function select($Q,$value,$where)
	{
		$result = mysql_query("select ".$value." from ".$Q["0"]." ".$where);
		if(!$result)
		{
			return mysql_error();
		}
		return $result;
	}	
}
?>