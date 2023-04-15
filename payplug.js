//
// CMSUno
// Plugin payplug
//
function f_save_payplug(){
	let key=document.getElementById("payplugKey").value;
	let lng=document.getElementById("langPayplug").options[document.getElementById("langPayplug").selectedIndex].value;
	let x=new FormData();
	x.set('action','save');
	x.set('unox',Unox);
	x.set('ubusy',Ubusy);
	x.set('key',key);
	x.set('lng',lng);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		document.getElementById("btSavePayplug").className='bouton fr';
		if(pass.length>5&&mail.length>5)setTimeout(function(){location.reload();},1000);
	});
}
function f_load_payplug(){
	let x=new FormData();
	x.set('action','load');
	x.set('unox',Unox);
	fetch('uno/plugins/payplug/payplug.php?r='+Math.random(),{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		if(r.key!=undefined)document.getElementById('payplugKey').value=r.key;
		if(r.lng!=undefined&&r.lng)document.getElementById('langPayplug').value=r.lng;
	});
}
function f_treated_payplug(f,g,h){
	let x=new FormData();
	x.set('action','treated');
	x.set('unox',Unox);
	x.set('id',g);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f.parentNode.className="PayplugTreatedYes";
		f.innerHTML=h;f.className="";f.onclick="";
	});
}
function f_archivOrderPayplug(f,g){
	if(confirm(g)){
		let x=new FormData();
		x.set('action','archiv');
		x.set('unox',Unox);
		x.set('id',f);
		fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			f_alert(r);
			if(r.substr(0,1)!='!')f_payplugVente();
		});
	}
}
function f_payplugRestaurOrder(f){
	let x=new FormData();
	x.set('action','restaur');
	x.set('unox',Unox);
	x.set('f',f);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f_payplugArchiv();
	});
}
function f_payplugViewA(f){
	document.getElementById('payplugArchData').innerHTML='';
	let x=new FormData();
	x.set('action','viewA');
	x.set('unox',Unox);
	x.set('arch',f);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('payplugArchData').insertAdjacentHTML('beforeend',r);
		document.getElementById('payplugArchData').style.display="block";
	});
}
function f_payplugArchiv(){
	document.getElementById('payplugArchiv').innerHTML='';
	document.getElementById('payplugArchiv').style.display="block";
	document.getElementById('payplugConfig').style.display="none";
	document.getElementById('payplugVente').style.display="none";
	document.getElementById('payplugDetail').style.display="none";
	document.getElementById('payplugA').className="bouton fr current";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr";
	document.getElementById('payplugD').style.display="none";
	let x=new FormData();
	x.set('action','viewArchiv');
	x.set('unox',Unox);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('payplugArchiv').insertAdjacentHTML('beforeend',r);
		document.getElementById('payplugArchData').style.display="none";
	});
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
	document.getElementById('payplugVente').innerHTML='';
	document.getElementById('payplugVente').style.display="block";
	document.getElementById('payplugDetail').style.display="none";
	document.getElementById('payplugA').className="bouton fr";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr current";
	document.getElementById('payplugD').style.display="none";
	let x=new FormData();
	x.set('action','vente');
	x.set('unox',Unox);
	x.set('udep',Udep);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('payplugVente').insertAdjacentHTML('beforeend',r);
	});
}
function f_payplugDetail(f){
	document.getElementById('payplugDetail').innerHTML='';
	document.getElementById('payplugArchiv').style.display="none";
	document.getElementById('payplugConfig').style.display="none";
	document.getElementById('payplugVente').style.display="none";
	document.getElementById('payplugDetail').style.display="block";
	document.getElementById('payplugA').className="bouton fr";
	document.getElementById('payplugC').className="bouton fr";
	document.getElementById('payplugV').className="bouton fr";
	document.getElementById('payplugD').style.display="block";
	let x=new FormData();
	x.set('action','detail');
	x.set('unox',Unox);
	x.set('id',f);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		if(r.substr(0,1)!='!')document.getElementById('payplugDetail').insertAdjacentHTML('beforeend',r);
		else f_alert(r);
	});
}
function f_supp_payplug(f,g){
	f.parentNode.parentNode.removeChild(f.parentNode);
	let x=new FormData();
	x.set('action','suppsandbox');
	x.set('unox',Unox);
	x.set('file',g);
	fetch('uno/plugins/payplug/payplug.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
//
f_load_payplug();f_payplugVente();
