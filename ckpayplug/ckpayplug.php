<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
// ***** ACTION : URL *******************
if(isset($_POST['action']) && strip_tags($_POST['action'])=='url')
	{
	if(isset($_POST['par']) && isset($_POST['amo']) && isset($_POST['cur']) && isset($_POST['ipn']) && isset($_POST['oku']) && isset($_POST['eru']) && isset($_POST['nam']) && isset($_POST['id']) && $_POST['amo'] && $_POST['cur'] && $_POST['ipn'])
		{
		if(file_exists(strip_tags($_POST['par']).'/parameters.json'))
			{
			require_once(dirname(__FILE__).'/lib/Payplug.php');
			Payplug::setConfigFromFile(strip_tags($_POST['par']).'/parameters.json');
			$paymentUrl = PaymentUrl::generateUrl(array(
				'amount' => strip_tags($_POST['amo']),
				'currency' => strip_tags($_POST['cur']),
				'ipnUrl' => strip_tags($_POST['ipn']),
				'returnUrl' => strip_tags($_POST['oku']),
				'cancelUrl' => strip_tags($_POST['eru']),
				'customData' => strip_tags($_POST['nam']).'|'.strip_tags($_POST['amo']).'|'.strip_tags($_POST['id']).'|1||'
				));
			echo $paymentUrl;
			}
		else echo "setup";
		}
	else echo "incomplete";
	}
// ***************************************
else echo "incomplete";
exit();
//
// MEMO
// CMSUNO JSON CART FORMAT : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}}}
/*
Payment fields

Fields marked with an * are required.
Name 	Type 	Description
amount * 	Integer 	Transaction amount, in cents (such as 4207 for 42,07EUR). We advise you to verify that the amount is between the minimum and maximum amounts allowed for your account.
currency * 	String 	Transaction currency. Only EUR is allowed at the moment.
ipnUrl * 	String 	URL pointing to the ipn.php page, to which PayPlug will send payment and refund notifications. This URL must be accessible from anywhere on the Internet (usually not the case in localhost environments).
cancelUrl 	String 	URL pointing to your payment cancelation page, to which PayPlug will redirect your customer if he cancels the payment.
returnUrl 	String 	URL pointing to your payment confirmation page, to which PayPlug will redirect your customer after the payment.
email 	String 	The customer's email address.
firstName 	String 	The customer's first name.
lastName 	String 	The customer's last name.
customer 	String 	The customer ID in your database.
order 	String 	The order ID in your database.
customData 	String 	Additional data that you want to receive in the IPN.
origin 	String 	Information about your website version (e.g., 'My Website 1.2') for monitoring and troubleshooting.

IPN fields
Name 	Type 	Description
state 	String 	The new state of the transaction: paid or refunded.
idTransaction 	Integer 	The PayPlug transaction ID. We recommend you save it and associate it with this order in your database.
amount 	Integer 	Transaction amount, in cents (such as 4207 for 42,07EUR).
email 	String 	The customer's email address, either provided when creating the payment URL or entered manually on the payment page by the customer.
firstName 	String 	The customer's first name, either provided when creating the payment URL or entered manually on the payment page by the customer.
lastName 	String 	The customer's last name, either provided when creating the payment URL or entered manually on the payment page by the customer.
customer 	String 	Customer ID provided when creating the payment URL.
order 	String 	Order ID provided when creating the payment URL.
customData 	String 	Custom data provided when creating the payment URL.
origin 	String 	Information about your website version (e.g., 'My Website 1.2 payplug_php0.9 PHP 5.3'), provided when creating the payment URL, with additional data sent by the library itself.
isTest 	Boolean 	If value is true, the payment was done in Sandbox (TEST) mode.
*/

?>