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
ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');
ini_set('display_errors',0);
error_reporting(E_ALL);
include 'functions.php';
include 'simpleimage.php';

$mandate=htmlspecialchars($_GET["id"]);

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";

try
{
$soap_client=new SoapClient($wsdl);
 $functions = $soap_client->__getFunctions ();
var_dump ($functions);
$params = array("MandateID" => $mandate);
$propertylist=$soap_client->GetSingleProperty($params);
}
catch(SoapFault $exception)
{
    echo $exception->getmessage();
    die;
}


?>

<table border="0">
    <tr>
        <th></th>
        <th></th>
        <th></th> 
    </tr>
<?php
//print_r($propertylist->CurrentPropertiesResult);
 $array = explode("<<>>",$propertylist->GetSinglePropertyResult); 
 
 foreach($array as $jsonitem ){
     $item=json_decode($jsonitem);
     
     $soldstyle="Active";
    if ($item->ListingStatus=="Sold"){
        $soldstyle="Sold";
    }
    echo "<tr><td valign='top' class=".$soldstyle." >"; 
 echo ("<b>".$item->MarketingHeading."</b><br>");
    echo ("<b>Area:</b>".$item->AddressLine."<br>");
    
    $time = strtotime($item->Created);

    $newformat = date('d M Y',$time);


    echo ("<b>Created:</b>".$newformat."<br>");
    
    echo ($item->MarketingDescription."<br>");
    echo "</td><td valign='top' nowrap>";
    foreach($item->Features as $Description ){
        if ($Description->Description<>""){
            echo ($Description->Description);
            echo ("<br>");
        }        
    }
    if ($item->YouTubeVideoUrl<>""){
        Echo "<a href='".$item->YouTubeVideoUrl."' target='_blank'>View Video</a>";
    }
    echo "</td><td width='50%'>";
    echo "<!-- Container for the image gallery --> ";
    echo "<div class='container'>";
    $counter=0;
    $images=array();
    $thumbs=array();
    
    foreach($item->Images as $myimage ){

        if(!is_null($myimage->Url)){
            $url = $myimage->Url;

            $img = "pictures/".$myimage->FileId.".png";
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
            $mythumb=thumbnail("pictures/".$myimage->FileId.".png","pictures/".$myimage->FileId."_thumb.png");
            
           // echo "<a target='_blank' href='pictures/".$myimage->FileId.".png' > <img width='150px' src='".$mythumb."' ></a>";
            array_push($images,"pictures/".$myimage->FileId.".png");
            array_push($thumbs,"pictures/".$myimage->FileId."_thumb.png");
            
            $counter=$counter+1;
            
            

        }
        
    }
    $counter=$counter-1;
    
    for ($i = 0; $i < count($images); $i++) {

        Echo "<!-- Full-width images with number text -->
                <div class='mySlides'>
                <div class='numbertext'>".$i." / ".$counter."</div>
                <img src='".$images[$i]."' style='width:100%'>
                </div>";
    }
    
     echo " <!-- Next and previous buttons -->
        <a class='prev' onclick='plusSlides(-1)'>&#10094;</a>
        <a class='next' onclick='plusSlides(1)'>&#10095;</a>
        <!-- Image text -->
        <div class='caption-container'>
        <p id='caption'></p>
        </div>";
     
    echo "<div class='row'>";
    
    echo " </div>";
    
    for ($i = 0; $i < count($images); $i++) {

        Echo "<div class='column'>
             <img class='demo cursor' src='".$images[$i]."' style='width:100%' onclick='currentSlide(".($i+1).")' alt=''>
            </div>";
    }
            
     Echo "</div>";       
            
   
    
    echo "</td></tr>";
}

?>

</table>


 

  

 
 

    
    
 
 
 
 <script language='Javascript'>
 var slideIndex = 1;
showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
} 
 </script>
 
 </body>
 </html>