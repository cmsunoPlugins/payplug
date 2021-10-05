<?php
if(!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/_sdata-'.$sdata.'/payplug.json')) {
	$lang0 = $lang;
	$q1 = file_get_contents('data/_sdata-'.$sdata.'/payplug.json');
	$a1 = json_decode($q1,true);
	if(!empty($a1['lng'])) $lang = $a1['lng'];
	include('plugins/payplug/lang/lang.php');
	$Uhead .= '<link rel="stylesheet" type="text/css" href="uno/plugins/payplug/ckpayplug/css/ckpayplugBtn0.css" />'."\r\n";
	if(!empty($a1['act'])) {
		// JSON : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}},"digital":"Ubusy|readme","ship":"4","name":"Sting","adre":"rue du lac 33234 PLOUG"}
		// n=nom, p=prix, i=ID, q=quantite
		// OK : ?payplug=ok&digit=mapage|monplugin|123456789123
		$o = '<div class="w3-panel w3-hide w3-red popAlert" id="popAlert"></div>';
		$o .= '<div class="w3-row-padding popPayment" style="color:#333;">';
			$o .= '<div class="w3-col m7"><h3>'.T_('Payment Details').'</h3><table class="popAdress">';
				$o .= '<tr><td>'.T_("First Name").'*</td><td><input class="w3-input" id="popFa" type="text"></td></tr>';
				$o .= '<tr><td>'.T_("Name").'*</td><td><input class="w3-input" id="popNa" type="text"></td></tr>';
				$o .= '<tr><td>'.T_("Mail").'*</td><td><input class="w3-input" id="popMa" type="text"></td></tr>';
			$o .= '</table></div>';
			$o .= '<div class="w3-col m5"><h3>'.T_('Pay').'</h3>';
				$o .= '<div><a href="JavaScript:void(0);" onClick="payplugCart2(payplugdatas);"><img src="uno/plugins/payplug/img/payplug-btn.png" class="w3-hover-opacity" alt="payplug" title="payplug" /></a></div>';
		$o .= '</div></div>';
		$o .= '<div id="popAlert"></div>';

		$tmp = "<script type=\"text/javascript\">var payplugdatas;"; // f : {"prod":{"0":{"n":"Rencontre Premium","p":48,"i":"","q":1}},"digital":"rencontre-premium|rencontreP","Ubusy":"rencontre-premium"}
		$tmp .= "function payplugCart2(f){var b=0,re=/\S+@\S+\.\S+/,d=0,r=0,dg=0,p=0,fa=document.getElementById('popFa').value,na=document.getElementById('popNa').value,ma=document.getElementById('popMa').value;f=JSON.parse(f);";
			$tmp .= "if(fa.length>2&&na.length>2&&re.test(ma))b=1;";
			$tmp .= "if(b==0)payplugAlert('".T_('Fields are mandatory')."');";
			$tmp .= "else if(b&&f['prod']){";
				$tmp .= "var x=new XMLHttpRequest();";
				$tmp .= "x.open('POST','uno/plugins/payplug/payplugCart.php',true);";
				$tmp .= "r=Math.random().toString().substr(2);"."\r\n";
				$tmp .= "p='action=url&fname='+encodeURIComponent(fa)+'&name='+encodeURIComponent(na)+'&mail='+encodeURIComponent(ma)+'&cur=EUR&cart='+encodeURIComponent(JSON.stringify(f))+'&ipn=".urlencode($a1['url'])."&eru=".urlencode($a1['err'])."&rand='+r+'&oku=".urlencode($a1['home'])."';"."\r\n";
				$tmp .= "x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');x.setRequestHeader('Content-length',p.length);";
				$tmp .= "x.setRequestHeader('X-Requested-With','XMLHttpRequest');";
				$tmp .= "x.setRequestHeader('Connection','close');";
				$tmp .= "x.onreadystatechange=function(){if(x.readyState==4&&x.status==200&&x.responseText.search('ttp'))window.location=x.responseText;};";
				//$tmp .= "x.onreadystatechange=function(){alert(x.readyState+' - '+x.status+' - '+x.responseText);};";
				$tmp .= "x.send(p);";
		$tmp .= "}};";
		$tmp .= "function payplugCart(f){payplugdatas=f;var a=document.getElementById('unoPop');if(a!=null)a.parentNode.removeChild(a);unoPop('".$o."',0);};";
		$tmp .= "function payplugAlert(f){var a=document.getElementById('popAlert');a.className=a.className.replace('w3-hide','w3-show');a.innerHTML=f;setTimeout(function(){a.innerHTML='';a.className=a.className.replace('w3-show','w3-hide');if(a.className.indexOf('w3-hide')==-1)a.className+=' w3-hide';},5000);};";
		$tmp .= "</script>"."\r\n";
		$Ufoot .= $tmp;
		$Uonload .= "if('ok'==unoGvu('payplug')){unoPop('".T_('Thank you for your payment')."',5000);document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}";
		$unoPop = 1; // include unoPop.js in output
	}
	$lang = $lang0;
}
?>
