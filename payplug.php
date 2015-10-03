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
			<div id="payplugA" class="bouton fr" onClick="f_payplugArchiv();" title="<?php echo _("Archives");?>"><?php echo _("Archives");?></div>
			<div id="payplugC" class="bouton <?php if(!file_exists('../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo 'danger '; ?>fr" onClick="f_payplugConfig();" title="<?php echo _("Configure Payplug plugin");?>"><?php echo _("Config");?></div>
			<div id="payplugV" class="bouton fr current" onClick="f_payplugVente();" title="<?php echo _("Sales list");?>"><?php echo _("Sales");?></div>
			<div id="payplugD" class="bouton fr current" title="<?php echo _("Payment Details");?>" style="display:none;"><?php echo _("Payment Details");?></div>
			<h2><?php echo _("Payplug");?></h2>
			<div id="payplugConfig" style="display:none;">
				<img style="float:right;margin:10px;" src="uno/plugins/payplug/img/payplugLogo.png" />
				<p><?php echo _("This plugin allows you to add different Payplug buttons in your website.");?></p>
				<p><?php echo _("It is used with the button") .'<img src="uno/plugins/payplug/ckpayplug/icons/ckpayplug.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("added to the text editor when the plugin is enabled.");?></p>
				<p><?php echo _("Create your account on");?>&nbsp;<a href='https://www.payplug.fr/'>Payplug</a>.</p>
				<h3><?php echo _("Default Settings");?> :</h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Account");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;">
						<?php
						if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo '<span style="color:green;font-weight:700">OK</span>';
						else echo '<span style="color:red;font-weight:700">'._("No account.").'</span>';
						?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td><label><?php echo _("Account Email");?></label></td>
						<td><input type="text" class="input" name="payplugMail" id="payplugMail" style="width:150px;" /></td>
						<td><em><?php echo _("Email address for the Payplug account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Account Password");?></label></td>
						<td><input type="password" class="input" name="payplugPass" id="payplugPass" style="width:150px;" /></td>
						<td><em><?php echo _("Password for the Payplug account. Encrypted record with payplug key.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Mode");?></label></td>
						<td>
							<select name="payplugMod" id="payplugMod">
								<option value="prod"><?php echo _("Production");?></option>
								<option value="test"><?php echo _("Test (sandbox)");?></option>
							</select>
						</td>
						<td><em><?php echo _("Production = Real payment ; Test = Factice to test account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Notify URL");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/payplug/ipn.php';?></td>
						<td><em><?php echo _("Local File for Payplug Instant Payment Notification (IPN)"); ?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Return URL");?></label></td>
						<?php $q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $home = $a['nom']; ?>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=ok';?></td>
						<td><em><?php echo _("Return URL after success payment. (This is the active page)"); ?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Failure URL");?></label></td>
						<td style="vertical-align:middle;padding:0 10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html?payplug=error';?></td>
						<td><em><?php echo _("Return URL after error in payment. (This is the active page)"); ?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo _("Options :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("External use");?></label></td>
						<td><input type="checkbox" name="payplugExt" id="payplugExt" /></td>
						<td><em><?php echo _("Use Payplug from another plugin : complete system with cart or digital product.");?></em></td>
					</tr>
				</table>
				<br />
				<div id="btSavePayplug" class="bouton <?php if(!file_exists('../../data/_sdata-'.$sdata.'/_payplug/parameters.json')) echo 'danger '; ?>fr" onClick="f_save_payplug();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
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
		if(file_put_contents('../../data/_sdata-'.$sdata.'/payplug.json', $out)) echo _('Setup OK');
		else echo '!'._('Impossible setup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'._("List of the Payplug payments").' :</h3>';
		echo '<style>
				#payplugVente table tr{border-bottom:1px solid #888;}
				#payplugVente table th{text-align:center;padding:5px 2px;font-weight:700;}
				#payplugVente table td{text-align:left;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
				#payplugVente table tr.PayplugTreatedYes td{color:green;}
				#payplugVente table td.yesno{text-decoration:underline;cursor:pointer;}
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
			echo '<tr><th>'._("Date").'</th><th>'._("Type").'</th><th>'._("Name").'</th><th>'._("Address").'</th><th>'._("Article").'</th><th>'._("Price").'</th><th>'._("Treated").'</th></tr>';
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
					echo '<td style="text-align:center" '.(!$r['treated']?'onClick="f_treated_payplug(this,\''.$r['idTransaction'].'\',\''._("No").'\')"':'').($r['treated']?'>'._("Yes"):' class="yesno">'._("No")).'</td>';
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
				if(file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo _('Treated');
				exit;
				}
			}
		echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['f']) && rename('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['f'],'../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['f'])) echo _('Restored');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		if(!is_dir('../../data/_sdata-'.$sdata.'/_payplug/archive')) mkdir('../../data/_sdata-'.$sdata.'/_payplug/archive',0711);
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json') && rename('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json','../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['id'].'.json')) echo _('Archived');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		if (is_dir('../../data/_sdata-'.$sdata.'/_payplug/archive') && $h=opendir('../../data/_sdata-'.$sdata.'/_payplug/archive'))
			{
			$o = '<div id="payplugArchData"></div><div>';
			while(($d=readdir($h))!==false)
				{
				$ext=explode('.',$d); $ext=$ext[count($ext)-1];
				if($d!='.' && $d!='..' && $ext=='json')
					{
					$o .= '<div class="payplugListArchiv" onClick="f_payplugViewA(\''.$d.'\');">'.$d.'</div>';
					}
				}
			closedir($h);
			echo $o.'</div><div style="clear:left;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'viewA':
		if(isset($_POST['arch']) && file_exists('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['arch']))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/archive/'.$_POST['arch']);
			$a = json_decode($q,true); $o = '<h3>'._('Archives').'</h3><table class="payplugTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			echo $o.'</table><div class="bouton fr" onClick="f_payplugRestaurOrder(\''.$_POST['arch'].'\');" title="'._("Restore").'">'._("Restore").'</div><div style="clear:both;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'detail':
		if(isset($_POST['id']) && file_exists('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json'))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json');
			$a = json_decode($q,true); $o = '<h3>'._('Payment Details').'</h3><table class="payplugTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			$o .= '</table>';
			$o .= '<div class="bouton fr" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').' onClick="f_archivOrderPayplug(\''.$_POST['id'].'\',\''._("Are you sure ?").'\')" title="">'._("Archive").'</div>';
			$o .= '<div style="clear:both;"></div>';
			echo $o;
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
