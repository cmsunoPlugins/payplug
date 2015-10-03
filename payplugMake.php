<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/_sdata-'.$sdata.'/payplug.json'))
	{
	$q1 = file_get_contents('data/_sdata-'.$sdata.'/payplug.json');
	$a1 = json_decode($q1,true);
	$Uhead .= '<link rel="stylesheet" type="text/css" href="uno/plugins/payplug/ckpayplug/css/ckpayplugBtn0.css" />'."\r\n";
	if($a1['ext'] && (strpos($Ucontent,'payplugCart(')!==false || strpos($Uhtml,'payplugCart(')!==false || strpos($Ufoot,'payplugCart(')!==false)) // paymentMake executed before payplugMake
		{
		// JSON : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}},"digital":"Ubusy|readme","ship":"4","name":"Sting","adre":"rue du lac 33234 PLOUG"}
		// n=nom, p=prix, i=ID, q=quantite
		// OK : ?payplug=ok&digit=mapage|monplugin|123456789123
		$tmp = "<script type=\"text/javascript\">";
		$tmp .= "function payplugCart(f){var d=0,r=0,dg=0,p=0;f=JSON.parse(f);if(f['prod']){";
		$tmp .= "var x=new XMLHttpRequest();x.open('POST','uno/plugins/payplug/payplugCart.php',true);r=Math.random().toString().substr(2);"."\r\n";
		$tmp .= "p='action=url&cur=EUR&cart='+encodeURIComponent(JSON.stringify(f))+'&ipn=".urlencode($a1['url'])."&eru=".urlencode($a1['err'])."&rand='+r+'&oku=".urlencode($a1['home'])."';"."\r\n";
		$tmp .= "x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');x.setRequestHeader('Content-length',p.length);x.setRequestHeader('X-Requested-With','XMLHttpRequest');x.setRequestHeader('Connection','close');";
		$tmp .= "x.onreadystatechange=function(){if(x.readyState==4&&x.status==200&&x.responseText.search('ttp'))window.location=x.responseText;};";
		$tmp .= "x.send(p);";
		$tmp .= "}};";
		$tmp .= "</script>"."\r\n";
		$Ufoot .= $tmp;
		$Uonload .= "if('ok'==unoGvu('payplug')){unoPop('"._('Thank you for your payment')."',5000);document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}";
		$unoPop=1; // include unoPop.js in output
		}
	}
?>