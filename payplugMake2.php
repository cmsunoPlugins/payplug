<?php
if(!isset($_SESSION['cmsuno'])) exit();
// Activates external method PAYPLUG in Payment Plugin if not
if(file_exists('data/payment.json')) {
	$q = file_get_contents('data/payment.json'); $b = json_decode($q,true);
	if(empty($b['method'])) $b['method'] = array('payplug'=>1);
	else if(empty($b['method']['payplug'])) $b['method']['payplug'] = 1;
	else $b = 0;
	if($b) file_put_contents('data/payment.json',json_encode($b));
}
?>
