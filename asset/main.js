//圖片預覽function
function readURL(input){
  var reader = new FileReader();
  var image = $(input).attr("data-imgsrc")+$(input).attr("data-id");
  reader.onload = function (e) {
    $(image).attr('src', e.target.result);
  }
  reader.readAsDataURL(input.files[0]);
}
function getFileName(o){
  var pos=o.lastIndexOf("\\");
  return o.substring(pos+1);  
}

function myMessage(mes,icon,time){
  $(".ui.modals").html("");
  $('.ui.modal').modal("hide");
  var html="<div class='ui basic modal'>";
  html+="<i class='close icon' style='zoom:1.5'></i>"
  html+="<div class='ui icon header'>";
  html+="<i class='"+icon+" icon'></i>";
  html+=mes;
  html+="</div>";
  $("#modal-section").html(html);
  $('.ui.modal').modal('show');
  if(typeof time!=='undefined')
  {
    setTimeout(function(){
      $('.ui.modal').modal("hide");
    },time);
  }
}

function myRefresh(core){
  url = 'index.php?token='+core+'&func=display';
  $.get(url,function(res){
    $(".page-content").html(res);
    $(".ui.dropdown").dropdown();
    $(document).on("click",".data-title",function(){
      var attr = $(this).attr("data-attr");
      var url = 'index.php?token='+core+'&func=searchbox&attr='+attr;
      $.get(url,function(res){
        $(".ui.modals").html("");
        $("#modal-section").html(res);
        $('.ui.modal').modal('show');
        $(".ui.dropdown").dropdown();
        //取得資料
        $(document).on("click",".searchItem",function(){
          var dataID = $(this).attr("data-id");
          var url = 'index.php?token='+core+'&func=display&dataID='+dataID;
          $.get(url,function(res){
            $(".ui.modals").html("");
            $("#modal-section").html(res);
            $('.ui.modal').modal('show');
            $(".ui.dropdown").dropdown();
          })
        })
      })
    })
  })
}

$(document).ready(function(){
  $(".ui.dropdown").dropdown();
  $("#logout").click(function(){
    var logout = $(this).attr("data-core")
    $.get("index.php?token="+logout,function(res){
      myMessage(res,"bell outline",800);
      setTimeout(function(){
        location.reload();
      },800);
    })
  })
})

//選單開關
$(document).on("click",".navbar-toggler,.menu-toggler",function(){
  $("#menubar").sidebar('setting', 'transition', 'push').sidebar('toggle');
})

//選單連結
$(document).on("click",".menu-item",function(){
  var core = $(this).attr("data-core");
  var url = 'index.php?token='+core+'&func=display';
  $.get(url,function(res){
    $("#menubar").sidebar('toggle');
    $(".page-content").html(res);
    $(".ui.dropdown").dropdown();
    //搜尋欄位
    $(document).on("click",".data-title",function(){
      var attr = $(this).attr("data-attr");
      var url = 'index.php?token='+core+'&func=searchbox&attr='+attr;
      $.get(url,function(res){
        $(".ui.modals").html("");
        $("#modal-section").html(res);
        $('.ui.modal').modal('show');
        $(".ui.dropdown").dropdown();
        //取得資料
        $(document).on("click",".searchItem",function(){
          var dataID = $(this).attr("data-id");
          var url = 'index.php?token='+core+'&func=display&dataID='+dataID;
          $.get(url,function(res){
            $(".ui.modals").html("");
            $("#modal-section").html(res);
            $('.ui.modal').modal('show');
            $(".ui.dropdown").dropdown();
          })
        })
      })
    })
  })
})

//選擇分頁
$(document).on("click",".pagebar a",function(){
  var core = $("#refresh").attr("data-core");
  var page = $(this).attr("data-page");
  var url = 'index.php?token='+core+'&func=display&page='+page;
  if(page>0)
  {
    $.get(url,function(res){
      $(".page-content").html(res);
      $(".ui.dropdown").dropdown();
    })
  }
})

//變更一頁數量
$(document).on("change","#change-page-items-amount",function(){
  var core = $(this).attr("data-core");
  var val = $( "#change-page-items-amount option:selected" ).text();
  var url = 'index.php?token='+core+'&val='+val;
  $.get(url,function(res){
    var core = $("#refresh").attr("data-core");
    myRefresh(core);
  })
})

//開啟排序
$(document).on("click","#turnOnSort",function(){
  var core = $(this).attr("data-core");
  var url = 'index.php?token='+core+'&val=all';
  $.get(url,function(res){
    var core = $("#refresh").attr("data-core");
    myRefresh(core);
  })
})

//重新整理
$(document).on("click","#refresh",function(){
  var core = $(this).attr("data-core");
  myMessage("刷新頁面","redo large loading",800);
  myRefresh(core);
})

//新增/編輯資料
$(document).on("click",".editItem",function(){
  var core = $(this).attr("data-core");
  var func = $(this).attr("data-func");
  var dataID = $(this).attr("data-id");
  var url = 'index.php?token='+core+'&func='+func+'&dataID='+dataID;
  $.get(url,function(res){
    $(".ui.modals").html("");
    $("#modal-section").html(res);
    $('.ui.modal').modal('show');
    if($('#ckeditor').length )
    {
      var editor = CKEDITOR.replace('ckeditor', {
        filebrowserImageBrowseUrl : 'http://localhost/sanchuen2018/wmadmin/ckfinder/ckfinder.html?type=Images',
        filebrowserImageUploadUrl : 'http://localhost/sanchuen2018/wmadmin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
      });
      CKFinder.setupCKEditor( editor, '/ckfinder/' );
    }
  })
})

//提交表單
$(document).on("click","#update-form-btn",function(){
  var core = $(this).attr("data-core");
  var func = $(this).attr("data-func");
  var dataID = $(this).attr("data-id");
  var url = 'index.php?token='+core+'&func='+func+'&dataID='+dataID;
  var formData = new FormData($("#update-form")[0])
  if($('#ckeditor').length )
  {
    formData.append('ckeditor', CKEDITOR.instances['ckeditor'].getData());
  }
  $.ajax({
    url: url,
    type: "POST",
    data: formData,			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
    contentType: false,       // The content type used when sending data to the server.
    cache: false,             // To unable request pages to be cached
    processData:false,        // To send DOMDocument or non processed data file it is set to false
    dataType: "text",
    complete:function(res)
    {
      myMessage(res['responseText'],"bell outline");
      myRefresh(core);
    }
  })
})

//檢視資料
$(document).on("click",".showItem",function(){
  var core = $(this).attr("data-core");
  var dataID = $(this).attr("data-id");
  var url = 'index.php?token='+core+'&func=show&dataID='+dataID
  $.get(url,function(res){
    $(".ui.modals").html("");
    $("#modal-section").html(res);
    $('.ui.modal').modal('show');
  })
})

//刪除資料
$(document).on("click",".quitItem",function(){
  var core = $(this).attr("data-core");
  var dataID = $(this).attr("data-id");
  myMessage("確定要刪除嗎?<BR><BR><BR><div class='ui green ok inverted button'>是的</div>","trash");
  $(".ui.green.ok").click(function(){
    var url = 'index.php?token='+core+'&func=quit&dataID='+dataID;
    $.get(url,function(res){
      myMessage(res,"check",800);
      myRefresh(core);
    })
  })
})

//排序資料
$(document).on("mouseenter",".sortItem",function(){
  $("body").css("overflow-y","hidden");
  $( "#sortable" ).sortable({
    update: function(event, ui)
    {
      var sort_array = new Array();
      $('.d-row.ui-state-default').each(function(){
        sort_array.push($(this).attr("data-id"));
      });
      var core = $("#refresh").attr("data-core");
      url = 'index.php?token='+core+'&func=sort';
      $.post(url,{sort_array:sort_array},function(res){
        myMessage(res,"bell outline",800);
        myRefresh(core);
      })
    }
  });
  $( "#sortable" ).sortable("enable");
})

$(document).on("mouseleave",".sortItem",function(){
  $( "#sortable" ).sortable("disable");
   $("body").css("overflow-y","scroll");
})

//切換狀態
$(document).on("change",".switchBtn",function(){
  var core = $(this).attr("data-core");
  var dataID = $(this).attr("data-id");
  var url = 'index.php?token='+core+'&func=status&dataID='+dataID;
  $.get(url,function(){
    myRefresh(core);
  }) 
})

//放大圖片
$(document).on("click",".data-img",function(){
  var src = $(this).attr("src");
  var img = "<img class='bigimg' src="+src+">";
  myMessage(img);
})

//單圖預覽
$(document).on("change","input.imgfile",function(){
  console.log($(this).val());
  readURL(this); 
  var imgid =  $(this).attr("data-id");
  var viewid = "#viewimg"+imgid;
  var nameid = "#imgname"+imgid;
  var img = $(this).val();
  var imgname = getFileName(img);
  $(nameid).html("更新圖片："+imgname);
  $(viewid).slideDown();
  $(".imgOriginal").animate({"zoom":"0.5"})
});

//選擇多圖數量
$(document).on("change","input[id='imgnum']",function(){
  var num = $(this).val();
  var thisid = $(this).attr("data-id");
  var uplid = "#upl-img-"+thisid;
  var string = "";
  var j = 0;
  for (var i=0;i < num;i++){
    j++;
    string += "<div>圖片檔案"+j+"："
    string += "<label class='myfilebtn'>選擇檔案"
    string += "<input type=file name=imgfile"+i+" class=chooseimg id=imgfile data-id=insert"+i+" data-img="+i+">"
    string += "</label>"
    string += "<div id=imgnameinsert"+i+"></div>"
    string += "<img class=viewimg id=viewimg-insert"+i+">"
    string += "</div>"
  };
  $(uplid).html(string);
});

//提交按鈕
$(document).on("click",".submitbtn",function(){
var core = $(this).attr("data-core");
  var formData = $("#login-form").serialize();
  var url='index.php?token='+core;
  $.get(url,formData,function(res){
    myMessage(res,"bell outline",800);
    setTimeout(function(){
      location.reload();
    },800);
  })
})
