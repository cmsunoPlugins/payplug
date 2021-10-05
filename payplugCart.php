<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
include('../../config.php');
// ***** ACTION : URL *******************
if(isset($_POST['action']) && strip_tags($_POST['action'])=='url') {
	if(!empty($_POST['cur']) && !empty($_POST['ipn']) && isset($_POST['oku']) && isset($_POST['eru'])) {
		if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/payplug.json')) {
			$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/payplug.json');
			$k = json_decode($q,true);
			if($k) $secretkey = (!empty($k['key'])?$k['key']:'');
		}
		if(!empty($secretkey)) {
			require_once(dirname(__FILE__).'/ckpayplug/libPayplug/lib/init.php');
			Payplug\Payplug::init(array('secretKey' => $secretkey));
			$cart = ""; $amo = 0; $digit = "";
			$a = json_decode(strip_tags(stripslashes($_POST['cart'])),true);
			if(isset($a['prod'])) foreach($a['prod'] as $r) {
				$cart .= (isset($r['n'])?$r['n']:'').'|'.(isset($r['p'])?$r['p']:'0').'|'.(isset($r['i'])?$r['i']:'').'|'.(isset($r['q'])?$r['q']:'1').'|;';
				$amo += (floatval((isset($r['p'])?$r['p']:'0')) * 100 * (isset($r['q'])?$r['q']:'1'));
			}
			if(isset($a['ship']) && $a['ship']) {
				$cart .= 'Shipping cost|'.$a['ship'].'||1|;';
				$amo += floatval($a['ship'])*100; // Shipping Cost
			}
			if(isset($a['digital']) && isset($_POST['rand'])) {
				$digit =  $a['digital'].'|'.$_POST['rand']; // JSON cart with digital ** "digital":"Ubusy|readme" ** - rand : rand JS Key
				$cart .= 'DIGITAL|'.$digit.'|;';
			}
			else if(isset($a['name']) && isset($a['adre']) && isset($a['mail'])) {
				$cart .= 'ADRESS|'.$a['name'].'|'.$a['adre'].'|'.$a['mail'].'|'.$a['Ubusy'].'|;';
			}
			$data = array(
				'amount' => intVal($amo),
				'currency' => strip_tags($_POST['cur']),
				'billing' => array(
					'first_name' => (!empty($k['first_name'])?$k['first_name']:'xxx'),
					'last_name' => (!empty($k['last_name'])?$k['last_name']:'xyxxz'),
					'email' => (!empty($a['mail'])?$a['mail']:(!empty($k['email'])?$k['email']:'xyxxz@example.com')),
					'address1' => (!empty($a['adre'])?$a['adre']:(!empty($k['address1'])?$k['address1']:'17 rue du lac')),
					'postcode' => (!empty($k['postcode'])?$k['postcode']:'75014'),
					'city' => (!empty($k['city'])?$k['city']:'PARIS'),
					'country' => (!empty($k['country'])?$k['country']:'FR'),
				),
				'shipping' => array(
					'first_name' => (!empty($k['first_name'])?$k['first_name']:'xxx'),
					'last_name' => (!empty($k['last_name'])?$k['last_name']:'xyxxz'),
					'email' => (!empty($a['mail'])?$a['mail']:(!empty($k['email'])?$k['email']:'xyxxz@example.com')),
					'address1' => (!empty($a['adre'])?$a['adre']:(!empty($k['address1'])?$k['address1']:'17 rue du lac')),
					'postcode' => (!empty($k['postcode'])?$k['postcode']:'75014'),
					'city' => (!empty($k['city'])?$k['city']:'PARIS'),
					'country' => (!empty($k['country'])?$k['country']:'FR'),
					'delivery_type' => (!empty($k['delivery_type'])?$k['delivery_type']:'DIGITAL_GOODS')
				),
				'notification_url' => strip_tags($_POST['ipn']),
				'hosted_payment' => array(
					'return_url' => strip_tags($_POST['oku']).($digit?'&digit='.urlencode($digit):''),
					'cancel_url' => strip_tags($_POST['eru'])
					),
				'metadata' => array(
					'customData' => $cart
					)
				);
			$payment = \Payplug\Payment::create($data);
			$payment_url = $payment->hosted_payment->payment_url;
			$payment_id = $payment->id;
			echo $payment_url;
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
// CMSUNO JSON CART FORMAT : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}},"digital":"Ubusy|readme","ship":"4","name":"Sting","adre":"rue du lac 33234 PLOUG","mail":"bob@example.com"}
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
* 
* ******************* API V2 - 2020 **********************
* Example
* {
  "id": "pay_5iHMDxy4ABR4YBVW4UscIn",
  "object": "payment",
  "is_live": true,
  "amount": 3300,
  "amount_refunded": 0,
  "authorization": null,
  "currency": "EUR",
  "created_at": 1434010787,
  "installment_plan_id": null,
  "is_paid": true,
  "paid_at": 1555073519,
  "is_refunded": false,
  "is_3ds": false,
  "save_card": false,
  "card": {
    "last4": "1800",
    "country": "FR",
    "exp_month": 9,
    "exp_year": 2017,
    "brand": "Mastercard",
    "id": null
  },
  "billing": {
    "title": "mr",
    "first_name": "John",
    "last_name": "Watson",
    "email": "john.watson@example.net",
    "mobile_phone_number": null,
    "landline_phone_number": null,
    "address1": "221B Baker Street",
    "address2": null,
    "postcode": "NW16XE",
    "city": "London",
    "state": null,
    "country": "GB",
    "language": "en"
  },
  "shipping": {
    "title": "mr",
    "first_name": "John",
    "last_name": "Watson",
    "email": "john.watson@example.net",
    "mobile_phone_number": null,
    "landline_phone_number": null,
    "address1": "221B Baker Street",
    "address2": null,
    "postcode": "NW16XE",
    "city": "London",
    "state": null,
    "country": "GB",
    "language": "en",
    "delivery_type": "BILLING"
  },
  "hosted_payment": {
    "payment_url": "https://secure.payplug.com/pay/5iHMDxy4ABR4YBVW4UscIn",
    "return_url": "https://example.net/success?id=42",
    "cancel_url": "https://example.net/cancel?id=42",
    "paid_at": 1434010827,
    "sent_by": null
  },
  "notification": {
    "url": "https://example.net/notifications?id=42",
    "response_code": 200
  },
  "failure": null,
  "description": null,
  "metadata": {
    "customer_id": 42
  }
}

*/

?>
