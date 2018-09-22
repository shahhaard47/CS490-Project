<?php  

#$data = json_decode(file_get_contents('php://input'), true);
#echo $data["user"];
#echo $data["pass"];

$jsondata = file_get_contents('php://input'); #check contents and send to debbie
#echo $rawdata;

$data = json_decode($jsondata, true);
$credentials = array(
    'ucid' => $data["user"],
    'pass' => $data["pass"]
    // '_eventId_proceed' => ''
); 

if($data["user"] && $data["pass"])
{
	echo "COOL: ".$data["user"]."\n"; #checking
	
	#curl send to debbie
	$curl_opts = array(CURLOPT_POST => 1,
		   	   CURLOPT_URL => 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back.php',
		   	   CURLOPT_POSTFIELDS => $jsondata,
			   CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch);
	#need to check $result and then send json to Emad

	#login spoofing
	$loginUrl = 'https://aevitepr2.njit.edu/myhousing/login.cfm';
	$ch2 = curl_init();
	//Set the URL to work with
	curl_setopt($ch, CURLOPT_URL, $loginUrl);
 
	// ENABLE HTTP POST
	curl_setopt($ch, CURLOPT_POST, 1);
 
	//Set the post parameters
	curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 'ucid='.$username.'&pass='.$password);


	//Handle cookies for the login
	// curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
 
	//Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
	//not to print out the results of its query.
	//Instead, it will return the results as a string return value
	//from curl_exec() instead of the usual true/false.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

 
	//execute the request (the login)
	$store = curl_exec($ch);
	// echo $store;

	$success = strpos($store, "302 Found");

	if ($success !== false){
	   echo "NJIT says welcome!";
	}
	else {
     	     echo "NJIT is a no go";
	}
}
else
{
	echo "NOT COOL";
}

?>