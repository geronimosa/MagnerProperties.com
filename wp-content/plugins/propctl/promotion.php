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
wp_enqueue_style( 'saleslisting', plugins_url( 'saleslisting.css' , __FILE__ ) );

function displayitem($atts = [], $content = null, $tag = ''){
    
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $wporg_atts = shortcode_atts(
        array(
            'title' => 'Featured',
            'images' => 2,
            'align' => 'left',
            'mandate' =>'all',            
        ), $atts, $tag
    );
       
    
    $html="";
    $html.= "<h2 class='ListTitle'>" . esc_html__( $wporg_atts['title'], 'wporg' ) . "</h2>";
    
    $html.="<table class='promotable' border='0'>";
    

    $pluginpath=plugin_dir_url(__FILE__);
    $upload_dir   = trailingslashit( WP_CONTENT_URL ) . 'pictures/';    
    $uploads= trailingslashit( WP_CONTENT_DIR ) . 'pictures/' ;    
    
    $mandate=htmlspecialchars($atts['mandateid']); 
    $mandate=get_option("mandateid",$mandate);
    
    $user=loggedon();
    
    try
        {
            $wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";    
            $soap_client=new SoapClient($wsdl);
            $params = array("MandateID" => $mandate);
            $propertylist=$soap_client->GetSingleProperty_XML($params);            
            $array = explode("<<>>",$propertylist->GetSingleProperty_XMLResult);     
        }
    catch(SoapFault $exception)
        {
            return $exception->getmessage();
        }
     
     foreach($array as $xmlitem ){
        $xmlitem = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlitem); 
        $item=simplexml_load_string($xmlitem);

        $time = strtotime($item->Created);
        $newformat = date('d M Y',$time);
        
        $soldstyle="Active";
        if ($item->ListingStatus=="Sold"){
            $soldstyle="Sold";
        }
        if ($item->MandateType=="Rental"){
            $soldstyle="Rental";
        }
           
            $html.= "<tr>";
            
            $html1="";
            $html2="";
            $html1="<td class='".$soldstyle."'>"; 
            $html1.= ("<b><span class='propertytitle'><a onclick='myFunction()' href='".$pluginpath."showlisting.php?id=".$item->MandateId."' target='MsgWindow'>".$item->MarketingHeading."</a></b></span><br>");
            $html1.= ("<div class='propertydescription'>".$item->MarketingDescription."</div><br>");
            
            if ($user<>""){
               //echo $user;
                
            }else{
                //echo "";
                
            }
            //echo $mandate;
            $suburbname="";
            $mysuburb=Array();
            try
                {
                    $params = array("SuburbId" => $item->SuburbId);
                    $suburb=$soap_client->GetSuburb($params);   
                    $suburbname=$suburb->GetSuburbResult;
                    $mysuburb=json_decode($suburbname);                    
                }
            catch(SoapFault $exception)
                {
                    return $exception->getmessage();
                }
            
            $html1.= ("<span class='propertyarea'> $mysuburb->SuburbName, $mysuburb->City, $mysuburb->Province</span><br>");
            $value=number_format($item->ListPrice*1,0,'.',' ');
            $html1.= ("<span class='propertyprice'>$item->CurrencySymbol $value </span><br>");
           // $html1.= ("<span class='propertymandate'><b>Mandate Type:</b> ".$item->MandateType." </span><br>");
            $featurearray = array();  
            $featurename=array();
            foreach($item->Features as $Feature ){   
                foreach($Feature as $feat ){
                    if (array_key_exists("$feat->Type", $featurearray)){                       
                        $featurearray["$feat->Type"]=$featurearray["$feat->Type"]+1;  
                        
                    }else{
                        $featurearray["$feat->Type"]=1;                   
                    }
                }
            }
            $features= "<div class='featurelist'><ul>";
            foreach($featurearray as $key => $Feature ){ 
                if ($Feature==1){
                    $features.= "<li>$key </li>";
                }else{
                    $features.= "<li>$key ($Feature)</li>";
                }
            }
            $features.= "</ul></div>";
                
//  
              //  print_r($featurearray);
                //$html1.= "</ul>";
            
            
            if ($item->YouTubeVideoUrl<>""){
                $html1.= "<span class='propertyvideo'>"."<a class='button' onclick='myFunction()' href='".$item->YouTubeVideoUrl."' target='MsgWindow'>Video</a>"."</span><br>";
            }
            $html1.= "</td>";
            $html2.="<td width='200px' nowrap style='border: none;text-align:left;vertical-align:top;padding:0'>";
            $html2.="<div class='propertypics'>";

            $maximages=10;
            $countimage=0;
            foreach($item->Images as $myimage ){
                foreach($myimage->MandateImage as $theimage ){
                    if ($countimage<$maximages){
                        $countimage+=1;
                        $url = (string) $theimage->Url;
                        $FileID = (string) $theimage->FileId;

                        if (!is_null($url) && $url<> "" ){
                            $dir =  $uploads;
                            if ( ! file_exists( $dir ) ) {
                                wp_mkdir_p( $dir );
                            }
                            $img = $dir.$FileID.".png";
                            $thm = $dir.$FileID."_thumb.png";
                            $img_disp = $upload_dir.$FileID.".png";
                            $thm_disp = $upload_dir.$FileID."_thumb.png";
                            $certificate=ABSPATH .$pluginpath."/certificate.pem";

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
                                $html2.= "<a target='_blank' href='".$img_disp."' ><img width='300px' src='".$thm_disp."' ></a>";
                            }

                        }
                    }
                }
            }
            


            $html2.="</div>$features";
            $html2.= "</td>";

            if ($wporg_atts['align']='left'){
                $html.=$html2.$html1;
            } else {
                $html.=$html1.$html2;
            }

            $html.= "</tr>";
            $html.= "<tr><td style='border: none;' colspan=3></td></tr>";
        

    }
    $html.= "</Table>";
    
    $html.= "<script language='Javascript'>";
    
$html.= " function myFunction(url) {
            var myWindow = window.open(url, 'MsgWindow', 'location=no,titlebar=no,status=no,menubar=no,channelmode=yes,toolbar=no,scrollbars=no,resizable=no,top=50,left=10,width=1000,height=650');
            
            }
        </script>    ";

    
    return $html;

}