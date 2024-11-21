<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors',0);
error_reporting(E_ALL);
include 'functions.php';
include 'simpleimage.php';

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?wsdl";

$soapClient = new SoapClient($wsdl); 

// print_r($soapClient->CurrentProperties());
$agentlist=$soapClient->Agents();
// print_r($agentlist->AgentsResult) ;

?>

<table>
    <tr>
        <th>Agent</th>
        <th>Contact</th>
        <th>Image</th> 
    </tr>
    
            


<?php

 $array = json_decode($agentlist->AgentsResult); 
 
 foreach($array as $item ){
    // print_r($item);
    echo "<tr><td>"; 
    echo ($item->AgentId."<br>");
    echo ($item->LastName."<br>");
    echo ($item->FirstName."<br>");
    echo ($item->EmailAddress."<br>");
        echo "</td><td>";
    foreach($item->TelephoneNumbers as $number ){
        echo ($number->Number."<br>");
    }
    echo "</td><td>";
    if(!is_null($item->Picture->Url)){
        $url = $item->Picture->Url;
    
        $img = "pictures/".$item->AgentId.".png";
        $certificate="/var/www/vhosts/surebondsa.com/magnerproperties.co.za/propctl/certificate.pem";
        
        $arrContextOptions=array(
            "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
            "local_cert" => $certificate,
            "allow_self_signed"=>true,  
            "cafile" => $certificate
            ),
        );  
        // file_put_contents( $img, file_get_contents($url,false,stream_context_create($arrContextOptions)));
        file_put_contents( $img, file_get_contents_curl($url));
        //print_r(file_get_contents_curl($url));
        $mythumb=thumbnail("pictures/".$item->AgentId.".png","pictures/".$item->AgentId."_thumb.png");
        
        echo "<img  src='".$mythumb."' >";
        

    }else{
        echo "No Image"."<br>";
     }
    
     
    echo "</td></tr>";
}

?>

</table>

 
 
 

