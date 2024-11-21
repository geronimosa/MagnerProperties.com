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
ini_set('display_errors',1);
error_reporting(E_ALL);
include 'functions.php';
include 'simpleimage.php';

$mandate=htmlspecialchars($_GET["id"]);

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";

try
{
$soap_client=new SoapClient($wsdl);
//$functions = $soap_client->__getFunctions ();
//var_dump ($functions);
$params = array("MandateID" => $mandate);
$propertylist=$soap_client->GetSingleProperty_XML($params);
}
catch(SoapFault $exception)
{
    echo $exception->getmessage();
    die;
}



//print_r($propertylist->CurrentPropertiesResult);
 $array = explode("<<>>",$propertylist->GetSingleProperty_XMLResult); 
 echo "<table align='center' class1 ='magnertable' border='0'>";
 
 foreach($array as $xmlitem ){
     
     $xmlitem = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlitem); 
     $item=simplexml_load_string($xmlitem);
     
     if (strlen($item)<>0){
     
    echo "<tr>
          <th colspan='3' align='center' ><div class='captionarea'><b>".$item->MarketingHeading."</b></div></th>
      </tr>";


    $soldstyle="Active";
        if ($item->ListingStatus=="Sold"){
            $soldstyle="Sold";
        }
        if ($item->MandateType=="Rental"){
            $soldstyle="Rental";
        }
        
    echo "<tr><td valign='top' style='padding-top:100px;'  class=".$soldstyle." > <div class = 'contentarea' >"; 
   
    echo ("");
    echo ($item->MarketingDescription."<br>");
    echo ("<b>Area:</b>".$item->AddressLine."<br>");
    echo ("<b>Price:</b>".$item->ListPrice."<br>");
    echo ("<b>Type:</b>".$item->MandateType."<br>");
    
    if ($item->YouTubeVideoUrl<>""){
        Echo "<a href='".$item->YouTubeVideoUrl."' target='_blank'>View Video</a>"."<br>";
    }
    $time = strtotime($item->Created);
    $newformat = date('d M Y',$time);


    echo ("<b>Loaded:</b>".$newformat."<br>");
    
    foreach($item->Features as $Description ){
        if ($Description->Description<>""){
            echo ($Description->Description);
            echo ("<br>");
        }        
    }
    
    echo " </div></td><td width='10' class = 'contentarea' valign='top' nowrap>";
    
    
    echo "</td><td width='50%' style='padding-top:10px;'>";
    echo "<!-- Container for the image gallery --> ";
    
    echo "<div class='container'>";
        $counter=0;
        $images=array();
        $thumbs=array();
        $maximages=10;
        $countimage=0;
        
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        $upload_dir   = $rootDir . '/propctl/pictures/';
        define( 'UPLOADS', $upload_dir  );
        $dir =  UPLOADS;
        foreach($item->Images as $myimage ){
            foreach($myimage->MandateImage as $theimage ){
                if ($countimage<$maximages){
                    $countimage+=1;
                    $url = (string) $theimage->Url;
                    $FileID = (string) $theimage->FileId;                    
                    $img = $dir.$FileID.".png";
                    $thm = $dir.$FileID."_thumb.png";                            
                    if (!file_exists($img)){
                        file_put_contents( $img, file_get_contents_curl($url));
                    }
                    if (!file_exists($thm)){  
                        $mythumb=thumbnail($img,$thm);
                    }
                    
                    $img_src  = "/propctl/pictures/".$FileID.".png";
                    $thm_disp = "/propctl/pictures/".$FileID."_thumb.png";


               // echo "<a target='_blank' href='pictures/".$myimage->FileId.".png' > <img width='150px' src='".$mythumb."' ></a>";
                array_push($images,$img_src);
                array_push($thumbs,$thm_disp);

                $counter=$counter+1;
                }
            }
        }

        $counter=$counter-1;

        for ($i = 0; $i < count($images); $i++) {

            Echo "<!-- Full-width images with number text -->
                    <div class='mySlides'>
                    <div class='numbertext'>".$i." / ".$counter."</div>
                    <img src='".$images[$i]."' style='width:98%'>
                    </div>";
        }

        echo " <!-- Next and previous buttons -->
            <a class='prev' onclick='plusSlides(-1)'>&#10094;</a>
            <a class='next' onclick='plusSlides(1)'>&#10095;</a>
            <!-- Image text -->
            ";

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
else
{
    echo "<tr><td colspan=2>Property no longer on the system.</td></tr>";
}
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