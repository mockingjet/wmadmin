<?php
// name     欄位名稱 id,username,account,password...       
// datatype 資料型態 int(10),char(200),text,datetime...    
// header   表格標題 (若無則不會出現在報表)
// show     顯示全部 (show的名稱，若無則不顯示)
// label    表單標籤 (表單必填項目)
// case     處理類別 (資料庫處理的case分類) 

class data
{
    static function display($Q)
    {
      $des = new Util();  
      $tb = DB::get($Q);
      //報表標題列
      $Query.= $_GET['dataID'] ? "<div class='ui large modal' style='width:90vw;'>" : "";
      $Query.="
      <div class='data-topic'>
        <div><i class='$tb[2] icon'></i>$tb[1]</div>
        <div id='refresh' data-core='".$des->encrypt($Q)."'><i class='redo loading icon'></i></div>
      </div>";

      $Query.="<div class='data-table d-table ui raised' id='sortable'>";
      $Query.="<div class='d-row'>";
      foreach($tb as $key => $data)
      {     
        if(!is_numeric($key))
        {
          $Query.= $data['header']!="" ? "<div class='data-title d-cell' style='cursor:pointer' data-attr='".$key."'>".$data['header']."</div>" : "";
        }
      }
      $Query.="<div class='data-title d-cell'>功能</div>
      </div>";

      //右側頁數選單
      if(is_numeric($_SESSION['num_item_page'])){ 
        $num_item_page = $_SESSION['num_item_page'];
        if($_GET['page'])
        {        
          if($_GET['dataID'])
          {
            $result=DB::select($tb,"*","where id = '".$_GET['dataID']."' order by sort asc limit $min,$num_item_page");
          }else{
            $Query.=html::getPageBar($Q,$_GET['page']);
            $max = $_GET['page']*$num_item_page;
            $min = $max-$num_item_page;
            $result=DB::select($tb,"*","order by sort asc limit $min,$num_item_page");
          }
        }else{
          if($_GET['dataID'])
          {
            $result=DB::select($tb,"*","where id = '".$_GET['dataID']."' order by sort asc limit $num_item_page");
          }else{            
            $Query.=html::getPageBar($Q,1);
            $result=DB::select($tb,"*","order by sort asc limit $num_item_page");
          }
        }
      } else {
        $Query.=html::getPageBar($Q,1);
        $result=DB::select($tb,"*","order by sort asc");
      }
      $m=0;

      //報表資料列
      for($i=0;$i< mysql_num_rows($result); $i++)
      {   
        $m++;
        $row = mysql_fetch_assoc($result);
        $src = "imgfiles/".$Q."/".$row['imgfile'];
        $Query.="<div class='d-row ui-state-default' data-id='".$row['id']."'>";
        foreach($tb as $key => $data)
        {
          if(!is_numeric($key))
          {
            switch($key)
            {
              case 'id':
                $Query.="<div class='data-cell d-cell'>$m</div>";
                break;
              case 'sort':
                if(is_numeric($_SESSION['num_item_page'])){ 	
                  $Query.="
                  <div class='data-cell d-cell'>
                    <i id='turnOnSort' class='link power icon'  data-core='".$des->encrypt('change')."'></i>
                  </div>";
                } else {
                  $Query.="
                  <div class='data-cell d-cell'>
                    <i class='ellipsis horizontal icon sortItem' style='cursor:move'></i>
                  </div>";
                }
                break;

              case 'imgfile':
                $Query.="
                <div class='img-cell data-cell d-cell'>
                  <img class='data-img' src='".$src."'>
                </div>";
                break;

              case 'maingrp':
                $res = DB::select(maingrp(),"*","where id='".$row[$key]."'");
                $grp = mysql_fetch_assoc($res);
                $vals = $grp['name'];
                $Query.="<div class='data-cell d-cell'>$vals</div>";
                break;

              case 'status':
                $Query.= $row[$key]==1 
                ? "<div class='data-cell d-cell'><div class='ui toggle checkbox switchBtn'
                    data-core='".$des->encrypt($Q)."' data-id='".$row['id']."' data-func='status'>
                      <input type='checkbox' value='1' name='status' checked>
                    </div></div>
                  " 
                : "<div class='data-cell d-cell'><div class='ui toggle checkbox switchBtn' data-func='status'
                    data-core='".$des->encrypt($Q)."' data-id='".$row['id']."'>
                      <input type='checkbox' value='0' name='status' >
                    </div></div>
                  ";
                break;

              default:
                $Query.= $data['header']!="" ? "<div class='data-cell d-cell'>".$row[$key]."</div>" : "";
                break;
            }
          }
        }
        $Query.="
        <div class='data-cell d-cell' style='overflow:visible;'>
          <div class='ui dropdown item func-drop'>
            <i class='dropdown icon'></i>                    
            <div class='menu'>
              <a class='item showItem' data-core='".$des->encrypt($Q)."' data-id='".$row['id']."'><i class='eye icon'></i>檢視</a>
              <a class='item editItem' data-core='".$des->encrypt($Q)."' data-func='form' data-id='".$row['id']."'><i class='edit icon'></i>編輯</a>
              <a class='item quitItem' data-core='".$des->encrypt($Q)."' data-func='delete' data-id='".$row['id']."'><i class='trash icon'></i>刪除</a>
            </div>
          </div><script>$('.ui.checkbox').checkbox();</script>";
        $Query.="</div>
        </div>";
        }                   
        $Query.="</div>";
        $Query.= $_GET['dataID'] ? "</div>" : "";
        return $Query;
    }

    static function form($Q)
    {
      $des = new Util();  
      $tb = DB::get($Q);
      if($_GET['dataID']!="")
      {
        $result=DB::select($tb,"*","where id ='".$_GET['dataID']."'");
        $row=mysql_fetch_assoc($result);
      }  
      $Query.= $_GET['dataID'] ? "<div class='ui modal update-modal'><i class='close icon' style='zoom:1.5'></i>":"";
      $Query.="<form class='ui form' id='update-form' enctype='multipart/form-data'> ";
      foreach($tb as $key => $data)
      {
        if($data['label']!="")
        {
          switch($data['case'])
          {
            case "text":
              $disable = $key=="account" && $_GET['dataID']!=undefined ? " readonly" : "";
              $Query.="
              <div class='field'>
                <label>".$data['label']."</label>
                <input type='text' class='' name='".$key."' value='".$row[$key]."' $disable>
              </div>";
              break;

            case "password":
              $disable = $key=="password" && $_GET['dataID']!=undefined ? " readonly" : "";
              $Query.="  
              <div class='field'>
                <label>".$data['label']."</label>
                <input class='' type='password' name='".$key."' value='".$row[$key]."' $disable>
              </div>
              <div class='field'>
                <label>再次確認</label>
                <input class='' type='password' name='check-".$key."' value='".$row[$key]."' $disable>
              </div>";
              break;

            case "sex":
              $Query.="
              <div class='inline field'>
                <label>".$data['label']."</label>
                <select name='".$key."'>
                <option value='1' class='hidden'>請選擇</option>
                <option value='1'>先生</option>
                <option value='0'>女士</option>
                </select>
              </div>";
              break;
                    
            case "status":
              $value = $row[$key]==null ? 1 : $row[$key];
              $Query.="
              <div class='inline field'>
                <label>".$data['label']."</label>
                <select name='".$key."'>
                <option value='".$value."' class='hidden'>請選擇</option>
                <option value='1'>開啟</option>
                <option value='0'>關閉</option>
                </select>
              </div>";
              break;

            case "birthday":
              $Query.="
              <div class='inline field'>
                <label>".$data['label']."</label>
                <input type='date' class='' name='".$key."' value='".$row[$key]."'>
              </div>";
              break;

            case "imgupload":
              $pic= $row['imgfile'] ? "imgfiles/".$Q."/".$row[$key]."" :"";
              $Query.="
              <div class='inline field'>
                <label>".$data['label']."</label>
                <label class='ui primary basic icon button' for='".$key."'>
                <i class='file icon'></i>Open File
                </label>
                <input type='file' id='".$key."' class='".$key."' style='display:none' name='".$key."' data-id='' data-imgsrc='#viewbox'>
                <BR>
                <img src='".$pic."' class='imgOriginal'>
                <div class='hidden imgboxClass' id='viewimg' style='display:inline-block'>
                  <div class='imgNameClass' id='imgname'></div>
                  <img class='imgViewClass' id='viewbox'>
                </div>
              </div>";
              break;

            case "ckeditor":
              $Query.="
              <div class='inline field'>
                <label>".$data['label']."</label>
                <textarea id='".$key."' type='' class='' name='".$key."'>".$row[$key]."</textarea>
              </div>";
              break;

            case "maingrp":
              $res = DB::select(DB::get('Maingrp'),"*");
              $Query.="
              <div class='field'>
                <label>".$data['label']."</label>
                <select name='".$key."'>
              ";
              if($_GET['dataID']!=undefined)
              {
                $res2 = DB::select(maingrp(),"*","where id = '".$row[$key]."'");
                $cates = mysql_fetch_assoc($res2);
                $Query.="<option style='display:none' value='".$cates['id']."'>".$cates['name']."</option>";
              }else{
                $Query.="<option style='display:none'>請選擇</option>";
              }
              for($i=0; $i< mysql_num_rows($res); $i++)
              {
                $vals = mysql_fetch_assoc($res);
                $Query.="<option value='".$vals['id']."'>".$vals['name']."</option>";
              }
              $Query.="</select></div>";
              break;

            case "authgrp":
              $tables = DB::TBArray();
              $Query.="<div class='field'><label>".$data['label']."</label>";
              foreach($tables as $key => $table)
              {
                $table=DB::get($table);
                $Query.="<div class='ui slider checkbox' style='margin:10px;'><input type='checkbox' class='' name='".$data['case']."[]' value='".$key."'><label>$table[1]</label></div>";
              }
              $Query.="</div>";
              break;
            
            case "othergrp":
              $Query.="
              <div class='field'>
                <label>".$data['label']."</label>
                <select name='".$key."'>
              ";
              if($_GET['dataID']!=undefined)
              {
                $Query.="<option style='display:none' value='".$row[$key]."'>".$row[$key]."</option>";
              }else{
                $Query.="<option style='display:none'>請選擇</option>";
              }
              $Query.="<option value='網站關鍵字'>網站關鍵字</option>";
              $Query.="</select></div>";
              break;
          }
        }
      }
      $Query.= $_GET['dataID']!=undefined
      ?"<button class='ui fluid violet button' id='update-form-btn' type='button' data-core='".$des->encrypt($Q)."' data-func='update' data-id='".$row['id']."'>更新</button></div>"
      :"<button class='ui fluid violet button' id='update-form-btn' type='button' data-core='".$des->encrypt($Q)."' data-func='update'>新增</button></div>";
      $Query.= $_GET['dataID'] ? "</div>":"";
      return $Query;
    }

    static function show($Q)
    {
      if($_GET['dataID'])
      {
        $tb = DB::get($Q);
        $result = DB::select($tb,"*","where id = '".$_GET['dataID']."'");
        $row = mysql_fetch_assoc($result);
        $Query.="
          <div class='ui modal update-modal'>
            <i class='close icon' style='zoom:1.5'></i>
            <div class='header'>檢視資料</div>
        ";
        $i=0;
        foreach($row as $key => $value)
        {
          $col = $tb[$key]['show'];
          switch($col) {
            case '建立時間':
              $value = $row['createAt'];
              break;
            case '狀態':
              $value = $value==1 ? "開啟" : "關閉";
              break;
            case '連結':
              $value = "<a href='".$data."' target=_blank style='color:blue'>$value</a>";
              break;
            case '圖片':
              $value = "<img src='imgfiles/".$Q.'/'.$value."' height='80'>";
              break;
            case '分類':
              $res = DB::select(DB::get('Maingrp'),"*","where id = '".$value."'");
              $grp = mysql_fetch_assoc($res);
              $value = $grp['name']." / ".$grp['en_name'];
              break;
            case '權限':
              $tablesArr = DB::TBArray();
              $keys = explode("&",$value);
              $tables = [];
              foreach($keys as $key)
              {
                $tbfunc = DB::get($tablesArr[$key])['1'];
                array_push($tables,$tbfunc);
              }
              $value = implode(" / ",$tables);
              break;
          }
          if($col!='') {
            $Query.="
            <div class='row detail-row'>
              <div class='col-3 detail-col'>$col</div>
              <div class='col-9 detail-data'>$value</div>
            </div>";  
          }
          $i++;
        }
        $Query.="</div>";
      }
      return $Query;
    }

    static function update($Q)
    {
      $tb = DB::get($Q);
      //新增資料
      if($_GET['dataID']==undefined)
      {
        foreach($tb as $key => $data)
        {
          if(!is_numeric($key))
          {
            $val = "'".$_POST[$key]."'";
            if($data['label']!="")
            {
              switch($key)
              {
                case 'account':
                  $result=DB::select($tb,"*","where account=$val");
                  if(mysql_num_rows($result)>0)
                  {
                    $Query="已有相同帳號存在，新增失敗";
                    return $Query;
                  }else{
                    $values[]= $val; 
                  }
                  break;
                case 'password':        
                  if($val == "'".$_POST['check-password']."'")
                  {
                    $values[]= $val; 
                  }else{
                    $Query="二次密碼輸入不同，新增失敗";
                    return $Query;
                  }
                  break;                  
                case 'imgfile':
                  $path="imgfiles/".$Q."/";
                  if(!is_dir($path))
                  {
                    mkdir($path, 0777, true);
                  }
                  $imgName = time().$_FILES['imgfile']['name'];
                  $sourcePath = $_FILES['imgfile']['tmp_name'];     
                  $targetPath = $path."/".$imgName; 								
                  move_uploaded_file($sourcePath,$targetPath) ; 
                  $values[]="'".$imgName."'";        
                  break;               
                case 'authgrp':
                  $values[]="'".implode("&",$_POST['authgrp'])."'";
                  break;
                default:                
                  $values[]= $val;                            
                  break;
              }
            }else{
              $d=date("Y-m-d H:i:s");
              switch($key)
              {
                case 'createAt':        $values[]="'".$d."'";                       break;
                case 'updateAt' :       $values[]="'".$d."'";                       break;
                case 'status' :         $values[]="'1'";                            break;
                case 'sort' :           
                  $counter = DB::select($tb,"*");
                  $counter = mysql_num_rows($counter);
                  $counter++;
                  $values[]="'".$counter."'";    
                break;
                default:                $values[]="null";                           break;
              }        
            }
          }
        }
        $value=implode(",",$values);
        $result=DB::insert($tb,$value);
        $Query= $result ? "新增成功" : "新增失敗";
      }else{
        $id=$_GET['dataID'];
        $result=DB::select($tb,"*","where id='".$id."'");
        $row=mysql_fetch_assoc($result);
        foreach($tb as $key => $data)
        {
          if(!is_numeric($key))
          {
            $val = "".$key."='".$_POST[$key]."'";
            if($data['label']!="")
            {
              switch($key)
              {
                case 'password':        
                  if($val == "".$key."='".$_POST['check-password']."'")
                  {
                    $values[]= $val; 
                  }else{
                    $Query="二次密碼輸入不同，更新失敗";
                    return $Query;
                  }
                  break; 
                case 'imgfile':
                  if($_FILES['imgfile']['name']!='')
                  {
                    $path="imgfiles/".$Q."/";
                    $imgName = time().$_FILES['imgfile']['name'];
                    $sourcePath = $_FILES['imgfile']['tmp_name'];     
                    $targetPath = $path."/".$imgName; 								
                    move_uploaded_file($sourcePath,$targetPath) ; 
                    $values[]= "".$key."='".$imgName."'";     
                  }
                  break;
                case 'authgrp':
                  $values[]="authgrp='".implode("&",$_POST['authgrp'])."'";
                  break;
                default:                
                  $values[]= $val;                            
                  break;
              }
            }else{
              $d=date("Y-m-d H:i:s");
              $lastd=$row['logindatetime']?$row['logindatetime']:$d;
              $logintimes=$row['logintimes']?($row['logintimes']+1):"1";
              switch($key)
              {
                case 'updateAt' :     $values[]="".$key."='".$d."'";                       break;
              }        
            }
          }
          $value=implode(",",$values);
          $result=DB::update($tb,$value,"where id='".$id."'");
          $Query= $result ? "更新成功" : "更新失敗";
        }
      }
      return $Query;
    }

    static function sort($Q)
    {
      $tb = DB::get($Q);
      for($i=0; $i< count($_POST["sort_array"]); $i++)
      {
        $where="where id='".$_POST["sort_array"][$i]."'";
        $value="sort ='".$i."'";
        $result=DB::update($tb,$value,$where);
      }
      return "排序成功";
    }

    static function quit($Q)
    {
      $id=$_GET['dataID'];
      $tb = DB::get($Q);
      $result=DB::delete($tb,"","where id='".$id."'");
      $Query=$result?"刪除成功！":"刪除失敗";
      return $Query;
    }

    static function searchbox($Q)
    {
      $tb = DB::get($Q);
      $des = new Util();
      $Query.="
      <div class='ui modal'>
        <div class='ui fluid search selection dropdown'>
        <input type='hidden'>
        <i class='dropdown icon'></i>
        <div class='default text'> '點擊' 您的目標...</div>
        <div class='menu'>
      ";
      $attr = $_GET['attr'];
      $result=DB::select($tb,"*");
      for($i=0;$i< mysql_num_rows($result); $i++)
      {
        $row = mysql_fetch_assoc($result);
        $Query.="<div class='item searchItem' data-id='".$row['id']."'>$row[$attr]</div>";
      }
      $Query.="</div></div></div>";
      return $Query;
    }
    static function status($Q)
    {
      $tb = DB::get($Q);
      if($_GET['dataID']) {
        $result=DB::select($tb,"*","where id='".$_GET['dataID']."'");
        $row = mysql_fetch_assoc($result);
        $status = $row['status'] == 0 ? 1 : 0;
        $mes = $row['status'] == 0 ? "已 開 啟" : "已 關 閉";
        $value = "status = '".$status."'";
        $result=DB::update($tb,$value,"where id='".$_GET['dataID']."'");
        $Query=$result? $mes :"切換失敗";
      }
      return $Query;
    }   
}