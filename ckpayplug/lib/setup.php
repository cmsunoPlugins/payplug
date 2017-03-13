<?php

exit;

/*
*** Configuration Script for PAYPLUG ***
This script allows the creation of your Payplug key in parameters.json.

	1/ Enable this script by replacing exit; by //exit;
	2/ Complete $payplugMail and $payplugPass below. Set $isTest = true; for SANDBOX mode (test) or = false; for real payment
	3/ At the top of this page, activates this script by replacing exit; by //exit;
	4/ Save the script on your server in ckpaypal/lib/setup.php
	5/ Execute this script in your browser : http://www.yoursite.com/path/to/your/ckpaypal/lib/setup.php - (parameters.json will be created near setup.php)
	6/ Empty $payplugMail and $payplugPass below
	7/ Disable this script by replacing //exit; by exit;
	8/ Replace the script on your server in ckpaypal/lib/setup.php.

Re-execute this script if you change SANDBOX <=> REAL PAYMENT

*/

require_once(dirname(__FILE__).'/Payplug.php');
//
$isTest = true; // true = SANDBOX   -   false = REAL PAYMENT
$payplugMail = '';
$payplugPass = '';
//
$parameters = Payplug::loadParameters($payplugMail, $payplugPass, $isTest);
$parameters->saveInFile(dirname(__FILE__).'/parameters.json');
?>