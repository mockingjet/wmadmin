<?php
class Html
{   
  static function getHead()
	{
		$Query.="
		<head>
			<title>Wmadmin</title>
			<meta charset='UTF-8'>
			<meta name='viewport' content='width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
			<script src='https://code.jquery.com/jquery-3.2.1.min.js' integrity='sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=' crossorigin='anonymous'></script>			
			<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js'></script>
			<script src='https://cdn.jsdelivr.net/npm/semantic-ui@2.3.1/dist/semantic.min.js'></script>
			<script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.28.8/dist/sweetalert2.all.min.js'></script>
			<script src='ckeditor/ckeditor.js'></script>
			<script src='ckfinder/ckfinder.js'></script>
			<script src='ckeditor/adapters/jquery.js'></script>
			<script src='asset/sort-ui/jquery-ui.js'></script>
			<link href='https://cdn.bootcss.com/bootstrap/4.1.1/css/bootstrap-grid.css' rel='stylesheet'>
			<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
			<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/semantic-ui@2.3.1/dist/semantic.min.css'>
			<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.0.13/css/all.css' integrity='sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp' crossorigin='anonymous'>
		";

		//自訂義JS
		foreach (glob("asset/*.js") as $filename) {
			$Query.="<script src='".$filename."'></script>";
		}
		//自訂義CSS
		foreach (glob("asset/*.css") as $filename) {
			$Query.="<link rel='stylesheet' href='".$filename."'>";
		}

		$Query.="
		</head>";
		return $Query;
	}  

	static function Portal()
	{ 
		//<img class='image' src='asset/imgs/logo.jpg' style='zoom:1.2' >
		$des = new Util();  
		$Query.=" 
		<style>
			body {
				font-family:microsoft jhenghei;
				font-size:16px;
				background-color: #DADADA;
			}
			body > .grid {
				height: 100%;
			}
			.image {
				margin-top: -100px;
			}
			.column {
				max-width: 450px;
			}
		</style>
		<div id='modal-section'></div>
		<div id='board' class='ui middle aligned center aligned grid '>
		<div id='adminGate' class='column' >
			<h2 class='ui teal image header animated zoomIn'>
				<div class='content' style='color:#21969F;letter-spacing:2px;'>
				".DB::System()['company_en']."<br>
				".DB::System()['company']."後台管理系統
				</div>
			</h2>
			<form id='login-form' class='ui large form'>
				<div class='ui stacked raised segment'>
					<div class='field'>
					<div class='ui left icon input'>
							<i class='user icon'></i>
							<input type='text' name='account' placeholder='account' autocomplete='account'>
					</div>
					</div>
					<div class='field'>
					<div class='ui left icon input'>
							<i class='lock icon'></i>
							<input type='password' name='password' placeholder='password' autocomplete='current-password'>
					</div>
					</div>
					<div class='ui fluid large teal submit button submitbtn ' data-core='".$des->encrypt("login")."'>登入</div>
				</div>
			</form>

			<div class='ui message'>
				<a target=_blank href='http://www.amazingwork.tw/'>唯美數位科技<BR><font size='1'>WeMade Digital Technology Co., Ltd.</font></a>
			</div>
		</div>
		</div>";

		return $Query;
	}

	static function Loginact()
	{	
		$where="where account='".trim($_GET['account'])."' and password='".trim($_GET['password'])."'";
		$result=DB::select(DB::get('Admin'),"*",$where);
		if(mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_array($result);
			$_SESSION['useracc']=trim($_GET['account']);
			$_SESSION['username']=$row['username'];
			$_SESSION['num_item_page'] = 10;
			$_SESSION['authgrp'] = $row['authgrp'];
			$Query="登入中!";
		}	
		else
		{ 
			$Query = "帳號或密碼錯誤！";
		}
		return $Query;
	}

	static function getBody()
	{
		$des = new Util();  
		$Query.="
		<body>
		<div id='menubar' class='ui left vertical blue inverted sidebar menu' style='font:700 18px microsoft jhenghei'>
			".Html::getMenu()."
		</div>
		<div class='pusher'>
			<div id='modal-section'></div>
			<div class='page-header' style='z-index:9999'>
				<div class='cpname'>
					<a class='item' style='color:white;font-family:microsoft jhenghei' href='index.php'>
						<i class='home icon'></i>".DB::System()['company_br']."
					</a>
				</div>
				<div class='menu-toggler'>
					<i class='fa fa-bars'></i>
				</div>
				<div class='header-right'>
					<div class='ui menu' style='background-color:#399bff'>
						<div class='right menu'>
							<div class='ui dropdown item' id='header-drop'>
								新增資料 
								<i class='dropdown icon'></i>
								<div class='menu' style='height:auto;top:35px;'>
									".Html::getAddItems()."
								</div>
							</div>
						</div>
						<div class='item' style='padding:0px'>
							<div class='ui dropdown item' id='admin-drop'>
								<i class='ui user icon'></i>
								".$_SESSION['useracc']."
								<i class='dropdown icon'></i>
								<div class='menu' style='height:auto;top:50px;'>
									<a id='logout' class='item' data-core='".$des->encrypt("logout")."'><i class='ui sign out alternate icon'></i>登出</a>
									<a id='user_setting' class='item' style='display:none'><i class='ui cog icon'></i>設定</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class='page-content'>
				".html::getDataPanel()."
			</div>
		</div>
		</body>";

		return $Query;
	}

	static function getMenu()
	{
		$des = new Util();  
		$tbs = DB::TBArray();
		$authgrp = explode("&",$_SESSION['authgrp']);
		foreach($tbs as $key => $tb)
		{
			if(in_array($key,$authgrp))
			{
				$tbName = DB::get($tb)['1'];
				$tbIcon = DB::get($tb)['2'];
				$Query.="<a class='menu-item item' style='text-align:center;border-top:1px solid white' data-core='".$des->encrypt($tb)."'><i class='$tbIcon icon'></i>$tbName</a>";
			}		
		}
		return $Query;
	}

	static function getAddItems()
	{
		$des = new Util();  
		$tables=DB::TBArray();
		foreach($tables as $table)
		{
			$Q=DB::get($table);
			if($table!="contact" && $table!="survey")
			{
				$Query.="<a class='item editItem' data-core='".$des->encrypt($table)."' data-func='form'>$Q[1]</a>";
			}
		}
		return $Query;
	}

	static function getDataPanel()
	{
		$Query="
		<div class='panel' style='float:none;margin:auto;margin-top:50px;'>
		<div>資料總覽</div>
			<table class='ui single line table'>
				<thead>
				<tr>
					<th>資料名稱</th>
					<th>現有筆數</th>

				</tr>
				</thead>
				<tbody>";
		
		$tbs = DB::TBArray();
		$authgrp = explode("&",$_SESSION['authgrp']);
		foreach($tbs as $key => $tb)
		{
			if(in_array($key,$authgrp))
			{
				$tb = DB::get($tb);
				$result=DB::select($tb,"*");
				$num = mysql_num_rows($result);
				$result=DB::select($tb,"*","order by id desc limit 1");
				$lastrow = mysql_fetch_array($result);
				$showtime = $lastrow['createAt'] ? $lastrow['createAt'] : $lastrow['updatetime'];
				$Query.="<tr>
					<td><i class='$tb[2] icon'></i> ".$tb['1']."</td>
					<td> ".$num." 筆</td>
				</tr>";
			}
		}
		
		$Query.="</tbody>
			</table>
		</div>";

		return $Query;
	}

	static function getPageBar($Q,$now){
		
		$result = DB::select(DB::get($Q),"*");
		$min = 1;
		$max = ceil(mysql_num_rows($result)/$_SESSION['num_item_page']);

		$Query.="<div class='pagebar ui right vertical pagination menu'
					style='position:absolute; top:30%; right:30px; width:50px;'>";

		if($now != $min) {
			$Query.="
			<a class='icon item' data-page='".($now-1)."'>
				<i class='up chevron icon' ></i>
			</a>";
		}
				
		$Query.="<a class='item' data-page='".$min."'>$min</a>";

		while($min<$max-1)
		{
			$min++;
			$Query.="<a class='item'  data-page='".$min."'>$min</a>";
		}				
				
		if($max>1)
		{
			$Query.="<a class='item'  data-page='".$max."'>$max</a>";
		}

		$des = new Util(); 
		
		if($now != $max) {
			$Query.="
			<a class='icon item' data-page='".($now+1)."'>
				<i class='down chevron icon' ></i>
			</a>";
		}
		
		$Query.="
			<label style='font:700 18px microsoft-jhenghei; text-align:center'>數量</label>
			<select id='change-page-items-amount' data-core='".$des->encrypt("change")."' style='font:700 18px microsoft-jhenghei;'>
				<option style='display:none'>".$_SESSION['num_item_page']."</option>
				<option>10</option>
				<option>15</option>
				<option>20</option>
				<option>all</option>
			</select>
		</div>
		";        

		return $Query;
	}
}
 