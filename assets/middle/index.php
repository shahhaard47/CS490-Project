<?php  

#$data = json_decode(file_get_contents('php://input'), true);
#echo $data["user"];
#echo $data["pass"];

$jsondata = file_get_contents('php://input'); #check contents and send to debbie
#echo $rawdata;

$data = json_decode($jsondata, true);

if($data["user"] && $data["pass"])
{
	echo "COOL: ".$data["user"].", ".$data["pass"]; #checking
	
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
	
}
else
{
	echo "NOT COOL";
}

?>