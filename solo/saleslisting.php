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
//wp_enqueue_style( 'saleslisting', plugins_url( 'saleslisting.css' , __FILE__ ) );

function displaylist($atts = [], $content = null, $tag = ''){
    
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $wporg_atts = $atts;
    
    $html="";
    $wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";
    
    
    
    $soapClient = new SoapClient($wsdl); 
    $propertylist=$soapClient->CurrentProperties_XML();
    $html .= '<h2 class="ListTitle">' .  $wporg_atts['title'] . '</h2>';
    $html.="<table border='0'>";
    
    $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    
    //$pluginpath=$_SERVER['REQUEST_URI'];
    
    
    $pluginpath =  '/propctl/';
    
    $upload_dir   = $rootDir . '/propctl/pictures/';
    define( 'UPLOADS', $upload_dir  );
    

     $array = explode("<<>>",$propertylist->CurrentProperties_XMLResult); 
     
     
     
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
        
        if ($wporg_atts['mandate']=='all' xor $wporg_atts['mandate']==$item->MandateType ){
            
        
            $html1="";
            $html2="";

            $html.= "<tr>";

            $html1="<td class='".$soldstyle."'>"; 
            $html1.= ("<b><span class='propertytitle'><a onclick='myFunction()' href='".$pluginpath."showlisting.php?id=".$item->ListingNumber."' target='MsgWindow'>".$item->MarketingHeading."</a></b></span><br>");
            $html1.= ("<span class='propertydescription'>".$item->MarketingDescription."</span><br>");
            $html1.= ("<span class='propertyarea'><b>Area:</b> ".$item->AddressLine."</span><br>");
//            $html1.= ("<b>Created:</b>".$newformat."<br>");
            $value=number_format($item->ListPrice*1,0,'.',' ');
            $html1.= ("<span class='propertyprice'><b>list Price:</b>  ".$value." </span><br>");
//            $html1.= ("<b>list status:</b>".$item->ListingStatus."<br>");
            $html1.= ("<span class='propertymandate'><b>Mandate Type:</b> ".$item->MandateType." </span><br>");

            if ($item->YouTubeVideoUrl<>""){
                $html1.= "<span class='propertyvideo'>"."<a class='propertytitle' onclick='myFunction()' href='".$item->YouTubeVideoUrl."' target='MsgWindow'>View Video</a>"."</span><br>";
            }
            $html1.= "</td>";


            $html2.="<td nowrap style='border: none;text-align:left;vertical-align:top;padding:0'>";
            $html2.="<span class='propertypics'>";

            $maximages=10;
            $countimage=0;
            foreach($item->Images as $myimage ){
                foreach($myimage->MandateImage as $theimage ){
                    if ($countimage<$maximages){
                        $countimage+=1;
                        $url = (string) $theimage->Url;
                        $FileID = (string) $theimage->FileId;

                        if (!is_null($url) && $url<> "" ){
                            $dir =  UPLOADS;
                            if ( ! file_exists( $dir ) ) {
                                wp_mkdir_p( $dir );
                            }
                            $img = $dir.$FileID.".png";
                            $thm = $dir.$FileID."_thumb.png";
                            $img_disp = '/propctl/pictures/'.$FileID.".png";
                            $thm_disp = '/propctl/pictures/'.$FileID."_thumb.png";
                            $certificate=$pluginpath."/certificate.pem";
                           // echo $img."<br>";
                           // echo $thm."<br>";
                                
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
                            $imagestodisplay=$wporg_atts['images']+1;
                            if ($countimage<$imagestodisplay){
                                $html2.= "<a target='_blank' href='".$img_disp."' > <img width='150px' src='".$thm_disp."' ></a>";
                            }

                        }
                    }
                }
            }
            


            $html2.="</span>";
            $html2.= "</td>";

            if ($wporg_atts['align']='left'){
                $html.=$html2.$html1;
            } else {
                $html.=$html1.$html2;
            }

            $html.= "</tr>";
            $html.= "<tr><td style='border: none;' colspan=3></td></tr>";
        }

    }
    $html.= "</Table>";
    
    $html.= "<script language='Javascript'>";
    
$html.= " function myFunction(url) {
            var myWindow = window.open(url, 'MsgWindow', 'location=no,titlebar=no,status=no,menubar=no,channelmode=yes,toolbar=no,scrollbars=no,resizable=no,top=50,left=10,width=800,height=650');
            
            }
        </script>    ";

    
    
    return $html;

}