<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'functions.php';
include 'simpleimage.php';
ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');
ini_set('display_errors',1);
error_reporting(E_ALL);
//echo displaylist();

function displaylist(){
    $wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";
    $soapClient = new SoapClient($wsdl); 
    $propertylist=$soapClient->CurrentProperties_XML();
    $html="<table border='0'>";

     $array = explode("<<>>",$propertylist->CurrentProperties_XMLResult); 
     //print_r ($array);
     foreach($array as $xmlitem ){

        // print_r ($xmlitem);      
        $xmlitem = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlitem); 
        $item=simplexml_load_string($xmlitem);

        // print_r ($item);f<td
        $time = strtotime($item->Created);
        $newformat = date('d M Y',$time);
        $soldstyle="Active";
        if ($item->ListingStatus=="Sold"){
            $soldstyle="Sold";
        }
        if ($item->MandateType=="Rental"){
            $soldstyle="Rental";
        }
        $html.= "<tr><td class='".$soldstyle."'>"; 
        $html.= ("<b><a onclick='myFunction()' href='/propctl/showlisting.php?id=".$item->ListingNumber."' target='MsgWindow'>".$item->MarketingHeading."</a></b><br>");
        $html.= ($item->MarketingDescription."<br>");
        $html.= ("<b>Area:</b>".$item->AddressLine."<br>");
        $html.= ("<b>Created:</b>".$newformat."<br>");
        $html.= ("<b>list Price:</b>".$item->ListPrice."<br>");
        $html.= ("<b>list status:</b>".$item->ListingStatus."<br>");
        $html.= ("<b>Mandate Type:</b>".$item->MandateType."<br>");
        
        if ($item->YouTubeVideoUrl<>""){
            $html.= "<li>"."<a onclick='myFunction()' href='".$item->YouTubeVideoUrl."' target='MsgWindow'>View Video</a>"."</li>";
        }
        $html.= "</td><td style='border: none;text-align:left;vertical-align:top;padding:0'>";
        $a=1;
        $html.= "<ul>"; 
        if ($a==2){
            

            foreach($item->Features as $Feature ){        
                foreach($Feature as $feat ){
                    if (($feat->Type<>"")) {
                        $html.= "<li>:".$feat->Type." ".($feat->Description)." ".($feat->Value)." "."</li>";       
                        $html.= "<ul>";
                        foreach($feat->Options as $Option ){
                            if ($Option->Description<>""){
                                $html.= "<li>".$Option->Description." ".($Option->TagType)." "." "."</li>";
                            }
                        }
                        $html.= "</ul>";
                    }
                }   

            }
        }

        
        $html.= "</ul>"; 
        $html.= "</td><td nowrap style='border: none;text-align:left;vertical-align:top;padding:0'>";
        // print_r($item->Images);

        $maximages=2;
        $countimage=0;
        
        if ($a==1){
            foreach($item->Images as $myimage ){
                foreach($myimage->MandateImage as $theimage ){
                    if ($countimage<$maximages){
                        $countimage+=1;
                        $url = (string) $theimage->Url;
                        $FileID = (string) $theimage->FileId;
                        
                        if (!is_null($url) && $url<> "" ){
                            $img = ABSPATH ."/propctl/pictures/".$FileID.".png";
                            $thm = ABSPATH ."/propctl/pictures/".$FileID."_thumb.png";
                            $img_disp = "/propctl/pictures/".$FileID.".png";
                            $thm_disp = "/propctl/pictures/".$FileID."_thumb.png";

                            $certificate=ABSPATH ."/propctl/certificate.pem";

                            /**$arrContextOptions=array(
                                "ssl"=>array(
                                "verify_peer"=>false,
                                "verify_peer_name"=>false,
                                "local_cert" => $certificate,
                                "allow_self_signed"=>true,  
                                "cafile" => $certificate
                                ),
                            ); **/             
                            if (!file_exists($img)){
                                file_put_contents( $img, file_get_contents_curl($url));
                            }
                            if (!file_exists($thm)){
                                $mythumb=thumbnail($img,$thm);
                            }
                            $html.= "<a target='_blank' href='".$img_disp."' > <img width='150px' src='".$thm_disp."' ></a>";
                        }
                    }
                }
            }
        }



        $html.= "</td></tr>";
        $html.= "<tr><td style='border: none;' colspan=3></td></tr>";

    }
    $html.= "</Table>";
    
    $html.= "<script language='Javascript'>";
    
$html.= " function myFunction(url) {
            var myWindow = window.open(url, 'MsgWindow', 'location=no,titlebar=no,status=no,menubar=no,channelmode=yes,toolbar=no,scrollbars=no,resizable=no,top=50,left=10,width=800,height=650');
            
            }
        </script>    ";

    
    
    return $html;

}