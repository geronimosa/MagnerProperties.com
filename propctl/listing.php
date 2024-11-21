<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="showlisting.css">
</head>
<body>
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
include 'functions.php';
include 'simpleimage.php';
ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";

$soapClient = new SoapClient($wsdl); 

//try
//{
//$soap_client=new SoapClient($wsdl);
//$functions = $soapClient->__getFunctions ();
//var_dump ($functions);
//$params = array("MandateID" => $mandate);
//$propertylist=$soap_client->GetSingleProperty($params);
//}
//catch(SoapFault $exception)
//{
//    echo $exception->getmessage();
//    die;
//}

$propertylist=$soapClient->CurrentProperties();

?>

<table border="0">
    <tr>
        <th></th>
        <th></th>
        <th></th> 
    </tr>
<?php
// print_r($propertylist->CurrentPropertiesResult);

 $array = explode("<<>>",$propertylist->CurrentPropertiesResult); 
 
 foreach($array as $jsonitem ){
     //var_dump ($jsonitem);
     
    $item=json_decode($jsonitem);
    //var_dump ($item);
    $time = strtotime($item->Created);
    $newformat = date('d M Y',$time);
     
    echo "<tr><td valign='top'>"; 
    echo ("<b>".$item->MarketingHeading."</b><br>");
    echo ($item->MarketingDescription."<br>");
    echo ("<b>Area:</b>".$item->AddressLine."<br>");
    echo ("<b>Created:</b>".$newformat."<br>");
    echo ("<b>list Price:</b>".$item->ListPrice."<br>");
    
   // print_r($item)."<br>";
    
    echo "</td><td valign='top' nowrap>";
    
    
    echo "<ul>";    
    foreach($item->Features as $Feature ){
        if ($Feature->Type<>""){  
            //$feat=$soapClient->Features($Feature->Type);
            $feat=$Feature->Type;
            echo "<li>".$feat." ".($Feature->Description)." ".($Feature->Value)." "."</li><ul>";
            foreach($Feature->Options as $Option ){
                echo "<li>"." ".($Option->Description)." "." "."</li>";
                //var_dump ($Option);
            }
            echo "</ul>";
        } 
        
    }
      
    
    if ($item->YouTubeVideoUrl<>""){
        Echo "<li>"."<a href='".$item->YouTubeVideoUrl."' target='_blank'>View Video</a>"."</li>";
    }
    echo "</ul>"; 
    echo "</td><td>";
    
    foreach($item->Images as $myimage ){
//print_r($myimage);
        if(!is_null($myimage->Url)){
            $url = $myimage->Url;

            $img = "pictures/".$myimage->FileId.".png";
            $thm = "pictures/".$myimage->FileId."_thumb.png";
            
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
            if (!file_exists($img)){
                file_put_contents( $img, file_get_contents_curl($url));
            }
            if (!file_exists($thm)){
                $mythumb=thumbnail($img,$thm);
            }
            echo "<a target='_blank' href='".$img."' > <img width='150px' src='".$thm."' ></a>";

        }
    }
    
    
    
    echo "</td></tr>";
    echo "<tr><td colspan=3><hr></td></tr>";
}

?>

</table>

</Body>
</html>
