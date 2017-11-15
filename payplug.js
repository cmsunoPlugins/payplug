//
// CMSUno
// Plugin payplug
//
function f_save_payplug(){
	jQuery(document).ready(function(){
		var act=document.getElementById('activ').checked?1:0;
		var mail=document.getElementById("payplugMail").value;
		var pass=document.getElementById("payplugPass").value;
		var mod=document.getElementById("payplugMod").options[document.getElementById("payplugMod").selectedIndex].value;
		var off=(document.getElementById('ckpayplugoff').checked?1:0);
		jQuery.post('uno/plugins/payplug/payplug.php',{'action':'save','unox':Unox,'act':act,'mail':mail,'pass':pass,'mod':mod,'ckpayplugoff':off},function(r){
			f_alert(r);
			document.getElementById("btSavePayplug").className='bouton fr';
			if(pass.length>5&&mail.length>5)setTimeout(function(){location.reload();},1000);
		});
	});
}
function f_load_payplug(){
	jQuery(document).ready(function(){
		jQuery.ajax({type:'POST',url:'uno/plugins/payplug/payplug.php',data:{'action':'load','unox':Unox},dataType:'json',async:true,success:function(r){
			if(r.act!=undefined&&r.act==1)document.getElementById('activ').checked=true;else document.getElementById('activ').checked=false;
			if(r.mail!=undefined)document.getElementById('payplugMail').value=r.mail;
			document.getElementById('payplugPass').value='';
			if(r.mod){
				t=document.getElementById("payplugMod");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.mod){to[v].selected=true;v=to.length;}}
			}
			if(r.ckpayplugoff!=undefined&&r.ckpayplugoff)document.getElementById('ckpayplugoff').checked=true;
		}});
	});
}
function f_treated_payplug(f,g,h){
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'treated','unox':Unox,'id':g},function(r){f_alert(r);});
	f.parentNode.className="PayplugTreatedYes";
	f.innerHTML=h;f.className="";f.onclick="";
}
function f_archivOrderPayplug(f,g){if(confirm(g)){jQuery.post('uno/plugins/payplug/payplug.php',{'action':'archiv','unox':Unox,'id':f},function(r){f_alert(r);if(r.substr(0,1)!='!')f_payplugVente();});}}
function f_payplugRestaurOrder(f){jQuery.post('uno/plugins/payplug/payplug.php',{'action':'restaur','unox':Unox,'f':f},function(r){f_alert(r);f_payplugArchiv();});}
function f_payplugViewA(f){
	jQuery('#payplugArchData').empty();
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'viewA','unox':Unox,'arch':f},function(r){jQuery('#payplugArchData').append(r);jQuery('#payplugArchData').show();});
}
function f_payplugArchiv(){
	jQuery('#payplugArchiv').empty();
	document.getElementById('payplugArchiv').style.display="block";
	document.getElementById('payplugConfig').style.display="none";
	document.getElementById('payplugVente').style.display="none";
	document.getElementById('payplugDetail').style.display="none";
	document.getElementById('payplugA').className="bouton fr current";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr";
	document.getElementById('payplugD').style.display="none";
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'viewArchiv','unox':Unox},function(r){jQuery('#payplugArchiv').append(r);jQuery('#payplugArchData').hide();});
}
function f_payplugConfig(){
	document.getElementById('payplugArchiv').style.display="none";
	document.getElementById('payplugConfig').style.display="block";
	document.getElementById('payplugVente').style.display="none";
	document.getElementById('payplugDetail').style.display="none";
	document.getElementById('payplugA').className="bouton fr";
	document.getElementById('payplugC').className="bouton fr current";
	document.getElementById('payplugV').className="bouton fr";
	document.getElementById('payplugD').style.display="none";
}
function f_payplugVente(){
	document.getElementById('payplugArchiv').style.display="none";
	document.getElementById('payplugConfig').style.display="none";
	jQuery('#payplugVente').empty();document.getElementById('payplugVente').style.display="block";
	document.getElementById('payplugDetail').style.display="none";
	document.getElementById('payplugA').className="bouton fr";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr current";
	document.getElementById('payplugD').style.display="none";
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'vente','unox':Unox,'udep':Udep},function(r){jQuery('#payplugVente').append(r);});
}
function f_payplugDetail(f){
	jQuery('#payplugDetail').empty();
	document.getElementById('payplugArchiv').style.display="none";
	document.getElementById('payplugConfig').style.display="none";
	document.getElementById('payplugVente').style.display="none";
	document.getElementById('payplugDetail').style.display="block";
	document.getElementById('payplugA').className="bouton fr";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr";
	document.getElementById('payplugD').style.display="block";
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'detail','unox':Unox,'id':f},function(r){
		if(r.substr(0,1)!='!')jQuery('#payplugDetail').append(r);
		else f_alert(r);
	});
}
function f_supp_payplug(f,g){
	f.parentNode.parentNode.removeChild(f.parentNode);
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'supptest','unox':Unox,'file':g},function(r){f_alert(r);});
}
//
f_load_payplug();f_payplugVente();
