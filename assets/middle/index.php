<?php  

$jsondata = file_get_contents('php://input'); #check contents and send to debbie
#echo $rawdata;

$data = json_decode($jsondata, true);
$credentials = array(
    'ucid' => $data["user"],
    'pass' => $data["pass"]
    // '_eventId_proceed' => '' // for original njit login site with sessionid
);

// initialize both as failed
$success = 'success';
$failure = 'failure';
$auth_results = array(
	'back' => $failure,
	'njit' => $failure
);

if($data["user"] && $data["pass"])
{	
	//*	curl send to debbie
	$curl_opts = array(CURLOPT_POST => 1,
		   	   CURLOPT_URL => 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back.php',
		   	   CURLOPT_POSTFIELDS => $jsondata,
			   CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch);
	#need to check $result
	if (strpos($result, "Success") !== false){
		$auth_results['back'] = $success;
	} // else it'll stay $failure

	#login spoofing
	$loginUrl = 'https://aevitepr2.njit.edu/myhousing/login.cfm';
	$ch2 = curl_init();
	//Set the URL to work with
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
	// ENABLE HTTP POST
	curl_setopt($ch, CURLOPT_POST, 1);
	//Set the post parameters
	curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
 
	//execute the request (the login)
	$store = curl_exec($ch);

	$loginsuccess = strpos($store, "302 Found");
	if ($loginsuccess !== false){
		// echo "NJIT says welcome!";
		$auth_results['njit'] = $success;
	} // else $auth_results['njit'] will stay equal to $false
	// else {
	// 	// echo "NJIT is a no go";
	// }
	$auth_results_json = json_encode($auth_results);
	echo $auth_results_json;
}
else
{ // didn't receive proper format data request
	echo "NOT COOL";
}

?>