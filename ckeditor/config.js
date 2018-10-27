/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  config.language = 'zh';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.extraPlugins = 'youtube';
	config.skin = 'moono';
	config.toolbarGroups = [
		{name:"document",groups:["mode"]},
		{name:"insert",groups:["insert"]},
		{name:"links",groups:["links"]},
		{name:"basicstyles",groups:["basicstyles",'Subscript', 'Superscript','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']},
		{ name: 'paragraph', groups: [ 'align' ] },
		{name:"colors",groups:['TextColor']},
		{name:"styles",groups:["styles"]}
		
	]
	config.width = "100%";
	config.height = "300px";
	config.removeButtons = "Strike,Anchor,Styles,Specialchar,Flash,Print,Save,Iframe,Smiley,CreateDiv,NewPage,Preview,Format,BGColor,PageBreak,SpecialChar";		
	config.font_names = 'Arial/Arial, Helvetica, sans-serif;' + 
											'Times New Roman/Times New Roman, Times, serif;' +
    									'Verdana';
	config.font_names = 'Arial;Tahoma;Times New Roman;新細明體;標楷體;微軟正黑體';
};
