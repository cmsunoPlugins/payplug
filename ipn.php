<?php
include(dirname(__FILE__).'/../../config.php');
if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'))
	{
	$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true);
	$mailAdmin = $b['mel'];
	}
else $mailAdmin = false;
include dirname(__FILE__).'/../../template/mailTemplate.php';
$bottom = str_replace('[[unsubscribe]]','&nbsp;',$bottom);
require_once(dirname(__FILE__).'/ckpayplug/lib/Payplug.php');
Payplug::setConfigFromFile(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/parameters.json');
try
	{
	include(dirname(__FILE__).'/lang/lang.php');
	$ipn = new IPN();
	if(VerifIXNID($ipn->idTransaction,$sdata)==0)
		{
		// SAVE IN FILE : ipn2
		$kv=array("time" => time(), "treated" => 0);
		foreach($ipn as $k=>$v) { $kv[$k] = $v; }
		$ipn2 = json_encode($kv);
		// EXPLORE DATA
		$a = explode("|;", $ipn->customData);
		foreach($a as $r)
			{
			if(strpos($r,'DIGITAL|')!==false)
				{
				$d = explode("|", $r); // 1/ Ubusy ; 2/ shortcode (name) : 3/ key
				$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/markdown.json'); $b1 = json_decode($q,true);
				$q = file_get_contents(dirname(__FILE__).'/../../data/'.$d[1].'/site.json'); $b2 = json_decode($q,true);
				// copy & rename file
				$fi = dirname(__FILE__).'/../../../files/';
				if(!is_dir($fi.'upload/')) mkdir($fi.'upload/');
				if(!file_exists($fi.'upload/index.html')) file_put_contents($fi.'upload/index.html', '<html></html>');
				if(file_exists($fi.$d[2].'/'.$b1[$d[1]]['md'][$d[2]]['k'].$d[2].'.zip')) copy($fi.$d[2].'/'.$b1[$d[1]]['md'][$d[2]]['k'].$d[2].'.zip',$fi.'upload/'.$d[3].$d[2].'.zip');
				$zip = new ZipArchive;
				if($zip->open($fi.'upload/'.$d[3].$d[2].'.zip')===true)
					{
					$zip->addFromString($d[2].'/key.php', '<?php $key = "'.$d[3].'"; ?>');
					$zip->close();
					}
				if(!is_dir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/'))
					{
					mkdir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/');
					file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/index.html', '<html></html>');
					}
				file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/'.$d[3].$d[2].'.json', '{"t":"'.time().'","p":"payplug","d":"'.$d[2].'","k":"'.$d[3].'"}');
				// link to zip in mail
				$msg= $d[2].'.zip :<br />'."\r\n".'<a href="'.$b2['url'].'/files/upload/'.$d[3].$d[2].'.zip">'.$b2['url'].'/files/upload/'.$d[3].$d[2].".zip</a>\r\n<br /><br />\r\n"._('Thank you for your trust, see you soon!')."\r\n";
				// MAIL USER LINK TO ZIP
				mailUser($ipn->email, 'Download - '.$d[2], $msg, $bottom, $top);
				}
			}
		if($mailAdmin)
			{
			// ORDER ?
			$msgOrder = '<p style="text-align:right;">'.date("d/m/Y H:i").'</p><p>'; $b = 0; $p = 0; $name = ''; $mail = ''; $Ubusy = ''; $adre = '';
			$v1 = explode("|;", $ipn->customData);
			if(is_array($v1)) foreach($v1 as $v2)
				{
				$v3 = explode("|", $v2);
				if(is_array($v3) && count($v3)>2 && strpos($v2,'ADRESS|')===false)
					{
					if(!$b) $b=1;
					$msgOrder .= $v3[3].' x '.$v3[0].' ('.$v3[1].'&euro;) = '.($v3[3] * $v3[1]).'&euro;<br />';
					$p += ($v3[3] * $v3[1]);
					}
				if(is_array($v3) && count($v3)>2 && strpos($v2,'ADRESS|')!==false)
					{
					$name = $v3[1]; $adre = $v3[2]; $mail = $v3[3]; $Ubusy = $v3[4];
					}
				}
			if($mail && $Ubusy)
				{
				$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b2 = json_decode($q,true);
				$msg = "<table>";
				foreach($ipn as $k=>$v) $msg .= "<tr><td>".$k."&nbsp:&nbsp</td><td>".$v."</td></tr>\r\n";
				$msg .= "</table>\r\n";
				// MAIL ADMIN PAYMENT
				mailAdmin('Payplug - '._('Payment receipt').' : '.(($ipn->amount)/100).' EUR', $msg, $bottom, $top, $b2['url']);
				$msgOrder .= '</p><p>'._('Total').' : <strong>'.$p.' &euro;</strong></p>';
				$msgOrder = str_replace(".",",",$msgOrder);
				$msgOrder .= '<p>'._('Paid by Payplug').'.</p><hr /><p>'._('Name').' : '.$name.'<br />'._('Address').' : '.$adre.'<br />'._('Mail').' : '.$mail.'</p>';
				if($b)
					{
					// MAIL ADMIN ORDER
					mailAdmin(_('New order by Payplug'). ' - '.$ipn->idTransaction, $msgOrder, $bottom, $top, $b2['url']);
					// MAIL USER ORDER
					$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
					$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'payment', $ipn->idTransaction.'|'.$mail, MCRYPT_MODE_ECB, $iv));
					$info = "<a href='".stripslashes($b2['url']).'/uno/plugins/payment/paymentOrder.php?a=look&b='.urlencode($r)."&t=payplug'>"._("Follow the evolution of your order")."</a>";
					$msgOrderU = $msgOrder.'<br /><p>'._('Thank you for your trust.').'</p><p>'.$info.'</p>';
					mailUser($mail, $b2['tit'].' - '._('Order'), $msgOrderU, $bottom, $top, $b2['url'].'/'.$Ubusy.'.html');
					}
				// ADD MEMO TAX
				$q1 = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/payment.json'); $a1 = json_decode($q1,true);
				$kv['Utax'] = $a1['taa'].'|'.$a1['tab'].'|'.$a1['tac'].'|'.$a1['tad'];
				$kv['Ubusy'] = $Ubusy;
				$ipn2 = json_encode($kv);
				}
			else
				{
				// MAIL ADMIN PAYMENT
				$msg = "<table>";
				foreach($ipn as $k=>$v) $msg .= "<tr><td>".$k."&nbsp:&nbsp</td><td>".$v."</td></tr>\r\n";
				$msg .= "</table>\r\n";
				mailAdmin('Payplug - '._('Payment receipt').' : '.(($ipn->amount)/100).' EUR', $msg, $bottom, $top, $b2['url']);
				}
			}
		file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/'.$ipn->idTransaction.'.json', $ipn2);
		}
	else
		{ // Already done
		file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/tmp/errorRepetition'.$ipn->idTransaction.'.json', $ipn2);
		}
	}
catch (InvalidSignatureException $e)
	{
	// if($mailAdmin) mailAdmin("PayPlug IPN Error", array("Error IPN" => time()), $bottom, $top);
	sleep(2);exit;
	}
//
function VerifIXNID($txn_id,$sdata)
	{ // fonction pour verifier si la depense est deja effectue (1) ou pas (0)
	$a=array();
	if ($h=opendir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/'))
		{
		while (($file=readdir($h))!==false) { if($file==$txn_id.'.json') {closedir($h); return 1;}}
		closedir($h);
		}
	return 0;
	}
//
function mailAdmin($tit, $msg, $bottom, $top, $url)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	$body = '<b><a href="'.$url.'/uno.php" style="color:#000000;">'.$tit.'</a></b><br />'.$rn.$msg.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">MIME-Version: 1.0".$rn."Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn."Content-Type: text/plain; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgT.$rn;
	$msg.=$rn."--".$boundary.$rn."Content-Type: text/html; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgH.$rn.$rn."--".$boundary."--".$rn.$rn."--".$boundary."--".$rn;
	$subject = mb_encode_mimeheader(stripslashes($tit),"UTF-8");
	if(mail($mailAdmin, $subject, stripslashes($msg), $header)) return true;
	else return false;
	}
//
function mailUser($dest, $tit, $msg, $bottom, $top, $url=false)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	if($url) $body = '<b><a href="'.$url.'.html" style="color:#000000;">'.$tit.'</a></b><br />'.$rn.$msg.$rn;
	else $body = "<b>".$tit."</b><br />".$rn.$msg.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">MIME-Version: 1.0".$rn."Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn."Content-Type: text/plain; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgT.$rn;
	$msg.=$rn."--".$boundary.$rn."Content-Type: text/html; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgH.$rn.$rn."--".$boundary."--".$rn.$rn."--".$boundary."--".$rn;
	$subject = mb_encode_mimeheader(stripslashes($tit),"UTF-8");
	if(mail($dest, $subject, stripslashes($msg), $header)) return true;
	else return false;
	}
//
//$er = error_get_last();
//file_put_contents(dirname(__FILE__).'/../../data/errorIPNPayplug'.time().'.txt', $er);
?>
