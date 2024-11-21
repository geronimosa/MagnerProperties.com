<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="showlisting.css">
</head>
<body><?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',0);
error_reporting(E_ALL);
include 'functions.php';
include 'simpleimage.php';

ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";

$soapClient = new SoapClient($wsdl); 

$propertylist=$soapClient->CurrentProperties_XML();

?>

<table border="0">
    <tr>
        <th></th>
        <th></th>
        <th></th> 
    </tr>
<?php
 $array = explode("<<>>",$propertylist->CurrentProperties_XMLResult); 
 foreach($array as $xmlitem ){
     
    // print_r ($xmlitem);      
    $xmlitem = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlitem); 
    $item=simplexml_load_string($xmlitem);

    // print_r ($item);
    $time = strtotime($item->Created);
    $newformat = date('d M Y',$time);
    $soldstyle="Active";
    if ($item->ListingStatus=="Sold"){
        $soldstyle="Sold";
    }
    echo "<tr><td valign='top' class=".$soldstyle." >"; 
    
    
    
    echo ("<b>".$item->MarketingHeading."</b><br>");
    echo ($item->MarketingDescription."<br>");
    echo ("<b>Area:</b>".$item->AddressLine."<br>");
    echo ("<b>Created:</b>".$newformat."<br>");
    echo ("<b>list Price:</b>".$item->ListPrice."<br>");
    echo ("<b>Status:</b>".$item->ListingStatus."<br>");
    
    //print_r($item->Features)."<br>";
    
    echo "</td><td valign='top' nowrap>";
    echo "<ul>";    
    foreach($item->Features as $Feature ){        
        foreach($Feature as $feat ){
            if (($feat->Type<>"")) {
                echo "<li>:".$feat->Type." ".($feat->Description)." ".($feat->Value)." "."</li>";       
                echo "<ul>";
                foreach($feat->Options as $Option ){
                    if ($Option->Description<>""){
                        echo "<li>".$Option->Description." ".($Option->TagType)." "." "."</li>";
                    }
                }
                echo "</ul>";
            }
        }   
        
    }
      
    
    if ($item->YouTubeVideoUrl<>""){
        Echo "<li>"."<a href='".$item->YouTubeVideoUrl."' target='_blank'>View Video</a>"."</li>";
    }
    echo "</ul>"; 
    echo "</td><td>";
    // print_r($item->Images);
    
    foreach($item->Images as $myimage ){
        foreach($myimage->MandateImage as $theimage ){
            $url = (string) $theimage->FileId;

            if (!is_null($url) && $url<> "" ){
                $img = "pictures/".$url.".png";
                $thm = "pictures/".$url."_thumb.png";

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
        
    }
    
    
    
    echo "</td></tr>";
    echo "<tr><td colspan=3><hr></td></tr>";
    
}
echo "</Table>";
?>
</body>
 </html>

