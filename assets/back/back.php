<?php

echo "hello";

if(isset($_POST['user'])){
  echo "hello";
}
else{
  echo "bye";
}





$myObj->send="True";
$myJSON=json_encode($myObj);
echo $myJSON;

?>