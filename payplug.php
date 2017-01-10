<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
if (!is_dir('../../data/_sdata-'.$sdata.'/_payplug/')) mkdir('../../data/_sdata-'.$sdata.'/_payplug/',0711);
if (!is_dir('../../data/_sdata-'.$sdata.'/_payplug/tmp/')) mkdir('../../data/_sdata-'.$sdata.'/_payplug/tmp/');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/payplug/payplug.css" />
		<div class="blocForm">
			<div id="payplugA" class="bouton fr" onClick="f_payplugArchiv();" title="<?php echo T_("Archives");?>"><?php echo T_("Archives");?></div>
			<div id="payplugC" class="bouton <?php if(!file_exists('../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo 'danger '; ?>fr" onClick="f_payplugConfig();" title="<?php echo T_("Configure Payplug plugin");?>"><?php echo T_("Config");?></div>
			<div id="payplugV" class="bouton fr current" onClick="f_payplugVente();" title="<?php echo T_("Sales list");?>"><?php echo T_("Sales");?></div>
			<div id="payplugD" class="bouton fr current" title="<?php echo T_("Payment Details");?>" style="display:none;"><?php echo T_("Payment Details");?></div>
			<h2><?php echo T_("Payplug");?></h2>
			<div id="payplugConfig" style="display:none;">
				<img style="float:right;margin:10px;" src="uno/plugins/payplug/img/payplugLogo.png" />
				<p><?php echo T_("This plugin allows you to add different Payplug buttons in your website.");?></p>
				<p><?php echo T_("It is used with the button") .'<img src="uno/plugins/payplug/ckpayplug/icons/ckpayplug.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . T_("added to the text editor when the plugin is enabled.");?></p>
				<p><?php echo T_("Create your account on");?>&nbsp;<a href='https://www.payplug.fr/'>Payplug</a>.</p>
				<h3><?php echo T_("Default Settings");?> :</h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("Account");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;">
						<?php
						if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo '<span style="color:green;font-weight:700">OK</span>';
						else echo '<span style="color:red;font-weight:700">'.T_("No account.").'</span>';
						?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Account Email");?></label></td>
						<td><input type="text" class="input" name="payplugMail" id="payplugMail" style="width:150px;" /></td>
						<td><em><?php echo T_("Email address for the Payplug account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Account Password");?></label></td>
						<td><input type="password" class="input" name="payplugPass" id="payplugPass" style="width:150px;" /></td>
						<td><em><?php echo T_("Password for the Payplug account. Encrypted record with payplug key.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Mode");?></label></td>
						<td>
							<select name="payplugMod" id="payplugMod">
								<option value="prod"><?php echo T_("Production");?></option>
								<option value="test"><?php echo T_("Test (sandbox)");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Production = Real payment ; Test = Factice to test account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Notify URL");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/payplug/ipn.php';?></td>
						<td><em><?php echo T_("Local File for Payplug Instant Payment Notification (IPN)"); ?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Return URL");?></label></td>
						<?php $q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $home = $a['nom']; ?>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=ok';?></td>
						<td><em><?php echo T_("Return URL after success payment. (This is the active page)"); ?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Failure URL");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=error';?></td>
						<td><em><?php echo T_("Return URL after error in payment. (This is the active page)"); ?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo T_("Options :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("External use");?></label></td>
						<td><input type="checkbox" name="payplugExt" id="payplugExt" /></td>
						<td><em><?php echo T_("Use Payplug from another plugin : complete system with cart or digital product.");?></em></td>
					</tr>
				</table>
				<br />
				<div id="btSavePayplug" class="bouton <?php if(!file_exists('../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo 'danger '; ?>fr" onClick="f_save_payplug();" title="<?php echo T_("Save settings");?>"><?php echo T_("Save");?></div>
				<div class="clear"></div>
			</div>
			<div id="payplugDetail" style="display:none;"></div>
			<div id="payplugArchiv" style="display:none;"></div>
			<div id="payplugVente"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'load':
		if(file_exists('../../data/_sdata-'.$sdata.'/payplug.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/payplug.json');
			echo stripslashes($q);
			}
		else echo '[]';
		break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $home = $a['nom'];
		$a = Array();
		if(file_exists('../../data/_sdata-'.$sdata.'/payplug.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/payplug.json');
			if($q) $a = json_decode($q,true);
			}
		$a['mail'] = $_POST['mail'];
		$a['mod'] = $_POST['mod'];
		$a['ext'] = ($_POST['ext']?1:0);
		$a['url'] = substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/payplug/ipn.php';
		$a['home'] = substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=ok';
		$a['err'] = substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=error';
		$a['par'] = '../../../data/_sdata-'.$sdata.'/_payplug';
		$out = json_encode($a);
		if(strlen($_POST['pass'])>5)
			{
			require_once(dirname(__FILE__).'/ckpayplug/lib/Payplug.php');
			$parameters = Payplug::loadParameters($_POST['mail'], $_POST['pass'], (($a['mod']=='test')?true:false)); // TRUE = SANDBOX - FALSE = REAL
			$parameters->saveInFile(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/parameters.json');
			}
		if(file_put_contents('../../data/_sdata-'.$sdata.'/payplug.json', $out)) echo T_('Setup OK');
		else echo '!'.T_('Impossible setup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'.T_("List of the Payplug payments").' :</h3>';
		echo '<style>
				#payplugVente table tr{border-bottom:1px solid #888;}
				#payplugVente table th{text-align:center;padding:5px 2px;font-weight:700;}
				#payplugVente table td{text-align:left;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
				#payplugVente table tr.PayplugTreatedYes td{color:green;}
				#payplugVente table td.yesno{text-decoration:underline;cursor:pointer;}
				#payplugVente .payplugArchiv{width:16px;height:16px;margin:0 auto;background-position:-112px -96px;cursor:pointer;background-image:url("'.$_POST['udep'].'includes/img/ui-icons_444444_256x240.png")}
			</style>';
		$tab=''; $d='../../data/_sdata-'.$sdata.'/_payplug/';
		if ($dh=opendir($d))
			{
			while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..' && $file!='parameters.json') $tab[]=$d.$file; }
			closedir($dh);
			}
		if(count($tab))
			{
			echo '<br /><table>';
			echo '<tr><th>'.T_("Date").'</th><th>'.T_("Type").'</th><th>'.T_("Name").'</th><th>'.T_("Address").'</th><th>'.T_("Article").'</th><th>'.T_("Price").'</th><th>'.T_("Treated").'</th><th>'.T_("Del").'</th><th>'.T_("Archive").'</th></tr>';
			$b = array();
			foreach($tab as $r)
				{
				$q=@file_get_contents($r);
				$a=json_decode($q,true);
				$b[]=$a;
				}
			function sortTime($u1,$u2) {return (isset($u2['time'])?$u2['time']:0) - (isset($u1['time'])?$u1['time']:0);}
			usort($b, 'sortTime');
			foreach($b as $r)
				{
				if($r)
					{
					$item = ''; $typ = 'Pay';
					$it = explode("|;", $r['customData']);
					if($it)
						{
						$v=1;
						foreach($it as $r1) if(strpos($r1,'ADRESS|')===false)
							{
							$it1 = explode("|", $r1);
							if($it1[0] && $it1[0]!='DIGITAL')
								{
								$item.=(($it1[0]&&$item)?'<br />':'').$it1[0].' ('.$it1[3].')';
								++$v;
								}
							else if($it1[0]=='DIGITAL') $typ .= '<br />(Digital)';
							}
						}
					echo '<tr'.($r['treated']?' class="PayplugTreatedYes"':'').'>';
					echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_payplugDetail(\''.$r['idTransaction'].'\')">'.$r['idTransaction'].'</span></td>';
					echo '<td style="text-align:center">'.$typ.'</td>';
					echo '<td>'.$r['firstName'].'&nbsp;'.$r['lastName'].'<br />'.$r['email'].'</td>';
					echo '<td style="text-align:center">'.'/'.'</td>'; // Added later
					echo '<td>'.$item.'</td>';
					echo '<td>'.(intval($r['amount'])/100).' Eur</td>';
					echo '<td style="text-align:center;" '.(!$r['treated']?'onClick="f_treated_payplug(this,\''.$r['idTransaction'].'\',\''.T_("Yes").'\')"':'').($r['treated']?'>'.T_("Yes"):' class="yesno">'.T_("No")).'</td>';
					if(isset($r['isTest']) && $r['isTest']==true) echo '<td width="30px" style="cursor:pointer;background:transparent url(\''.$_POST['udep'].'includes/img/close.png\') no-repeat scroll center center;" onClick="f_supp_payplug(this,\''.$r['idTransaction'].'\')">&nbsp;</td>';
					else echo '<td></td>';
					echo '<td><div class="payplugArchiv" onClick="f_archivOrderPayplug(\''.$r['idTransaction'].'\',\''.T_("Are you sure ?").'\')"></div></td>';
					echo '</tr>';
					}
				}
			echo '</table>';
			}
		break;
		// ********************************************************************************************
		case 'treated':
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json');
			if($q)
				{
				$a = json_decode($q,true);
				$a['treated'] = 1;
				$out = json_encode($a);
				if(file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo T_('Treated');
				exit;
				}
			}
		echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		$d = $_POST['f'];
		$a = explode('__',$d);
		if(count($a)>2) $d1 = $a[0].'.json';
		else $d1 = $d;
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$d) && rename('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$d, '../../data/_sdata-'.$sdata.'/_payplug/'.$d1)) echo T_('Restored');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		$p = '../../data/_sdata-'.$sdata.'/_payplug/archive';
		if(!is_dir($p)) mkdir($p,0711);
		$d = $_POST['id'].'.json';
		$q = file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$d);
		if($q) $a = json_decode($q,true);
		else $a = array();
		if(!empty($a['time']) && !empty($a['amount']))
			{
			$d1 = substr($d,0,-5).'__'.$a['time'].'__'.$a['amount'].'__.json';
			}
		else $d1 = $d;
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$d) && rename('../../data/_sdata-'.$sdata.'/_payplug/'.$d, $p.'/'.$d1)) echo T_('Archived');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		$p = '../../data/_sdata-'.$sdata.'/_payplug/archive';
		if(is_dir($p) && $h=opendir($p))
			{
			$b = array();
			while(($d=readdir($h))!==false)
				{
				$ext=explode('.',$d); $ext=$ext[count($ext)-1];
				if($d!='.' && $d!='..' && $ext=='json')
					{
					if(strpos($d,'__')!==false)
						{
						$a = explode('__',$d);
						if(count($a)>2) $b[] = array('idTransaction'=>$a[0], 'time'=>$a[1], 'amount'=>$a[2], 'file'=>$d);
						}
					else
						{
						$q = file_get_contents($p.'/'.$d);
						if($q) $a = json_decode($q,true);
						else $a = array();
						if(!empty($a['time']) && !empty($a['amount']))
							{
							$d1 = substr($d,0,-5).'__'.$a['time'].'__'.$a['amount'].'__.json';
							rename($p.'/'.$d, $p.'/'.$d1);
							}
						}
					
					}
				}
			closedir($h);
			usort($b, function($f,$g) { return $g['time'] - $f['time'];});
			$o = '<div id="payplugArchData"></div><div>';
			foreach($b as $r)
				{
				$o .= '<div class="payplugListArchiv" onClick="f_payplugViewA(\''.$r['file'].'\');">'.$r['idTransaction'].' - '.date('dMy',$r['time']).' - '.substr($r['amount'],0,-2).'&euro;</div>';
				}
			echo $o.'</div><div style="clear:left;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'viewA':
		if(isset($_POST['arch']) && file_exists('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['arch']))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['arch']);
			$a = json_decode($q,true); $o = '<h3>'.T_('Archives').'</h3><table class="payplugTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			echo $o.'</table><div class="bouton fr" onClick="f_payplugRestaurOrder(\''.$_POST['arch'].'\');" title="'.T_("Restore").'">'.T_("Restore").'</div><div style="clear:both;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'detail':
		if(isset($_POST['id']) && file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json'))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json');
			$a = json_decode($q,true); $o = '<h3>'.T_('Payment Details').'</h3><table class="payplugTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			$o .= '</table>';
			$o .= '<div class="bouton fr" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').' onClick="f_archivOrderPayplug(\''.$_POST['id'].'\',\''.T_("Are you sure ?").'\')" title="">'.T_("Archive").'</div>';
			$o .= '<div style="clear:both;"></div>';
			echo $o;
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'supptest':
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['file'].'.json'))
			{
			unlink('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['file'].'.json');
			echo T_('Removed');
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
