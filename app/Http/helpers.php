<?php
function getdefault_currency()
{
	$currency = DB::table('currencies')->where('default_currency', 1)->pluck('currency_symbol');
	return $currency;
}

function sendMessage($message, $device_id)
{
		$content = array(
			"en" => $message
			);
		
		$fields = array(
			'app_id' => "3a2d241a-727b-48b4-894d-aa8c1fd25e93",
			'include_player_ids' => array($device_id),
			'data' => array(),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    	/*print("\nJSON sent:\n");
    	print($fields);*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic NGQ0MmE2MjAtZTNmMy00YmJiLThkYWMtMWY2ODY4YzcwMmRm'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$result = curl_exec($ch);
		curl_close($ch);
		
		return 1;
}

function sendPushNotification($message, $device_id)
{
		$content = array(
			"en" => $message
			);
		
		$fields = array(
			'app_id' => "66b94b6e-cdeb-4241-b2f0-272fc10574f9",
			'include_player_ids' => array($device_id),
			'data' => array(),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    	/*print("\nJSON sent:\n");
    	print($fields);*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic NGQ0MmE2MjAtZTNmMy00YmJiLThkYWMtMWY2ODY4YzcwMmRm'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$result = curl_exec($ch);
		curl_close($ch);
		
		return 1;
}

function sendmail($email, $subject, $msg)
{
		Mail::send([],
		array('msg' => $msg, 'email' => $email, 'subject' => $subject), function($message) use ($msg, $email, $subject)
		{
    		$mail_body = $msg;
    		$message->setBody($mail_body, 'text/html');
    		$message->to($email);
    		$message->subject($subject);
		});
		
		return 1;
}

function sendSMS($type = 2, $mobile, $msg)
{
	$mobile = '+966'.$mobile;
	$seller_id     = "Shuneez";
	$auth_token_key = "A0f28d75dba62d197530d6bff187fee5d";
   
	require_once('sms/sendsms.php');
	
	$sendsms=new \sendsms("http://api.sms.esigntech.com/api/v3",'sms'
	, $auth_token_key, $seller_id);
	$sendsms->send_sms($mobile, $msg,'','xml');
	return 1;
}

function valid_mobile($mobile)
{
	if(substr($mobile, 0,1) == 0)
	{
		$mobile = substr($mobile, 1);
	} 
	return $mobile;
}

function getdays()
{
	$days = array('sunday' => 'Sunday', 'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday');

	return $days;
}

function getDookCounties()
{
	$header = array();
	$header[] = 'Content-type: application/json; charset=utf-8';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Countries");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);
	$countries = json_decode($response);

	return $countries;
}

function getDookCities($contryId)
{
	$header = array();
	$header[] = 'Content-type: application/json; charset=utf-8';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Countries/".$contryId."/cities");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);
	$citylist = json_decode($response);

	return $citylist;
}

function assignOrder($orderId, $driverId, $accessToken)
{
	$fields = ['ordersId' => [$orderId],
			   'driverId' => $driverId
			  ];
	$fields = json_encode($fields);
	$header = array();
	$header[] = 'Content-type: application/json; charset=utf-8';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/FleetOwners/assignOrderToDriver?access_token=".$accessToken);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);

	return 1;
}
	
?>
