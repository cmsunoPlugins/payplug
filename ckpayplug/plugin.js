/**
 * Plugin CKPayplug
 * Copyright (c) <2015> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
var ckpayplugMem=0;

//	var s=document.getElementsByTagName('script'),u="",el=document.createElement('script');
//	el.async=false;el.type='text/javascript';
//	for(v=0;v<s.length;v++){if(s[v].src.match('ckpayplug/plugin.js')) u=s[v].src.substr(0,s[v].src.search('ckpayplug/plugin.js'));}
//	if(u!=""){
//		el.src=u+'ckpayplug/ckpayplugConfig.js';
//		(document.getElementsByTagName('HEAD')[0]||document.body).appendChild(el);
//	}

CKEDITOR.plugins.add('ckpayplug',{
	icons:'ckpayplug',
	lang: 'en,fr',
	init:function(editor){
		ckpayplugMem=0;
		var lang=editor.lang.ckpayplug;
		editor.addCommand('ckpayplugDialog',new CKEDITOR.dialogCommand('ckpayplugDialog'));
		editor.ui.addButton('ckpayplug',{
			label:lang.title,
			command:'ckpayplugDialog',
			toolbar:'cmsuno'
		});
		editor.addContentsCss(this.path+'css/ckpayplugBtn0.css' );
		editor.on('doubleclick',function(evt){
			var el=evt.data.element;
			if(!el.isReadOnly()&&el.is('a')&&el.getAttribute('class')=='payplug-btn-payment'){
				ckpayplugMem=el.getAttribute('alt');
				ckpayplugMem=((ckpayplugMem)?ckpayplugMem.split('|'):['','','','','']);
				evt.data.dialog='ckpayplugDialog';
			}
		});
		CKEDITOR.dialog.add('ckpayplugDialog',this.path+'dialogs/ckpayplug.js');
	}
});
