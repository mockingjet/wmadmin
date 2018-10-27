CKEDITOR.plugins.add('imageupload',
{
    init: function (editor) {
        var pluginName = 'imageupload';
        editor.ui.addButton('imageupload',
        {
        	label : '編輯器圖片檢視器',
					toolbar : 'insert',
          command: 'editorImageSelector',
          icon: this.path + 'Uploadbuttonicon.png',
        });
        var cmd = editor.addCommand('editorImageSelector', { exec: showMyDialog });
    }
});
function showMyDialog(e){
	$(window).scrollTop(0);
	$CKEBox = $("#"+e.name);
	$(this).editorImageSelector($CKEBox);
}