/**
 * Plugin CKPayplug
 * Copyright (c) <2015> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
CKEDITOR.dialog.add('ckpayplugDialog',function(editor){
	var lang=editor.lang.ckpayplug,payplugData={},d;
	var payplugButton=function(d){
		var x=new XMLHttpRequest(),p=encodeURI('action=url&par='+payplugPar+'&amo='+d.price+'&cur='+d.curr+'&nam='+d.name+'&id='+d.idnum+'&ipn='+payplugUrl+'&oku='+payplugHome+'&eru='+payplugErr);
		x.open("POST","uno/plugins/payplug/ckpayplug/ckpayplug.php",true);
		x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		x.setRequestHeader('X-Requested-With','XMLHttpRequest');
		x.setRequestHeader("Content-length",p.length);
		x.setRequestHeader("Connection","close");
		x.onreadystatechange=function(){
			if (x.readyState==4 && x.status==200){
				var y=x.responseText,o;
				if(y!='setup'&&y!='miss'){
					o='<a class="payplug-btn-payment" target="_blank" ';
					o+='href="'+y+'" ';
					o+='alt="'+d.name+'|'+d.idnum+'|'+d.price+'|'+d.curr+'|'+d.label+'" ';
					o+='title="'+d.name+((d.idnum!='')?' (id:'+d.idnum+')':'')+' - '+(d.price)/100+d.curr+'">'+d.label+'</a>';
					editor.insertHtml(o);
					CKEDITOR.dialog.getCurrent().hide();
					return;
				}
				else if(y=='setup')document.getElementById('ckpayplugValid').innerHTML=lang.setup;
				else document.getElementById('ckpayplugValid').innerHTML=lang.miss;
			}
		};
		x.send(p);
	};
	return{
		title:lang.buttonPayplug,
		minWidth:250,
		minHeight:250,
		contents:[{
			id:'ckpayplug0',
			label:'',
			title:'',
			expand:false,
			padding:0,
			elements:[
			{
				type:'text',
				id:'ckpayplugName',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.labelitemName+' *',
				commit:function(){payplugData.name=this.getValue();}
			},{
				type:'text',
				id:'ckpayplugIdnum',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.labelIdnum,
				commit:function(){payplugData.idnum=this.getValue();}
			},{
				type:'text',
				id:'ckpayplugPrice',
				label:lang.labelprice+' *',
				labelStyle:'display:block;line-height:1.6em;',
				commit:function(){
					payplugData.price=this.getValue();
					payplugData.price=payplugData.price.replace(',','.');
					payplugData.price*=100;
				}
			},{
				type:'select',
				id:'ckpayplugCurr',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.labelcurrency,
				items:[['EUR']],
				style:'max-width:100px;',
				commit:function(){payplugData.curr=this.getValue();}
			},{
				type:'text',
				id:'ckpayplugLabel',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.textbutton,
				'default':lang.labelbutton,
				commit:function(){payplugData.label=this.getValue();}
			},{
				type:'html',
				html:'<div id="ckpayplugValid" style="color:red;font-weight:700;margin-top:20px;"></div>'
			}]
		}],
		onOk:function(){
			this.commitContent();
			payplugButton(payplugData,this);
			this.dialogObj.show();
		},
		onShow:function(){
			var dia=CKEDITOR.dialog.getCurrent();
			if(ckpayplugMem[0])dia.getContentElement('ckpayplug0','ckpayplugName').setValue(ckpayplugMem[0]);
			if(ckpayplugMem[1])dia.getContentElement('ckpayplug0','ckpayplugIdnum').setValue(ckpayplugMem[1]);
			if(ckpayplugMem[2])dia.getContentElement('ckpayplug0','ckpayplugPrice').setValue((ckpayplugMem[2])/100);
			if(ckpayplugMem[3])dia.getContentElement('ckpayplug0','ckpayplugCurr').setValue(ckpayplugMem[3]);
			if(ckpayplugMem[4])dia.getContentElement('ckpayplug0','ckpayplugLabel').setValue(ckpayplugMem[4]);
			payplugData={};
			document.getElementById('ckpayplugValid').innerHTML='';
			return;
		}
	};
});
//
var tag=document.getElementsByTagName('span'),v;
for(v in tag){if((' '+tag[v].className+' ').indexOf(' cke_button__ckpayplug_icon ')>-1)tag[v].onclick=function(){ckpayplugMem=['','','','',''];};}
