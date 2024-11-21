<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="showlisting.css">
</head>
<body>
    <?php
ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/wp-load.php');
include 'functions.php';
include 'simpleimage.php';

$mandate=htmlspecialchars($_GET["id"]);

$wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";

$pluginpath=plugin_dir_url(__FILE__);
$upload_dir   = trailingslashit( WP_CONTENT_URL ) . 'pictures/';
define( 'UPLOADS', trailingslashit( WP_CONTENT_DIR ) . 'pictures/' );
    


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
    echo "oops: ".$exception->getmessage();
    die;
}

 $array = explode("<<>>",$propertylist->GetSingleProperty_XMLResult); 
 
 foreach($array as $xmlitem ){
     
     $xmlitem = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlitem); 
     $item=simplexml_load_string($xmlitem);
     
     $title="";
     $title.="<div class='Area'>$item->MarketingHeading</div>";
     
     
    $desc ="";    
    $desc.="<div class='MarketingDescription'>";
    $desc.="$item->MarketingDescription</div>";
    
    $time = strtotime($item->Created);
    $newformat = date('d M Y',$time);
    
    $number=$item->ListPrice*1;
    $price="<span class='Price'> ".number_format($number, 0, ',', ' ')."</span>";
    
    $video=""; 
    if ($item->YouTubeVideoUrl<>""){
        $video.="<div ><a class='button' href='.$item->YouTubeVideoUrl.' target='_blank'>Video</a>"."</div><br>";
    }
    $content="";  
    $content.="<div class='featurelist'>Features:<ul>";
    $featurearray = array();  
    foreach($item->Features as $Feature ){   
        foreach($Feature as $feat ){
            if (array_key_exists("$feat->Type", $featurearray)){                       
                $featurearray["$feat->Type"]=$featurearray["$feat->Type"]+1;  
            }else{
                $featurearray["$feat->Type"]=1;                   
            }
        }
    }

    foreach($featurearray as $key => $Feature ){ 
        if ($Feature==1){
            $content.= "<li>$key </li>";
        }else{
            $content.= "<li>$key ($Feature)</li>";
        }
    }
    $content.= "</ul></div>";
    
    if ($item->MandateType=="Sale-remove"){
        ?>
            <!-- MORTGAGE LOAN CALCULATOR BEGIN -->
        <script type="text/javascript">
        mlcalc_default_calculator = 'mortgage_only';
        mlcalc_currency_code      = '';
        mlcalc_amortization       = 'month';
        mlcalc_purchase_price     = '<?php echo $item->ListPrice; ?>';
        mlcalc_down_payment       = 'null';
        mlcalc_mortgage_term      = '25';
        mlcalc_interest_rate      = '9.5';
        mlcalc_property_tax       = 'null';
        mlcalc_property_insurance = 'null';
        mlcalc_pmi                = 'null';
        mlcalc_loan_amount        = '250,000';
        mlcalc_loan_term          = '15';
        </script>
        <script type="text/javascript">if(typeof jQuery == "undefined"){document.write(unescape("%3Cscript src='" + (document.location.protocol == 'https:' ? 'https:' : 'http:') + "//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' type='text/javascript'%3E%3C/script%3E"));mlcalc_jquery_noconflict=1;};</script><div style="font-weight:normal;font-size:9px;font-family:Tahoma;padding:0;margin:0;border:0;text-align:center;background:transparent;color:#EEEEEE;width:150px;" id="mlcalcWidgetHolder"><script type="text/javascript">document.write(unescape("%3Cscript src='https://www.mlcalc.com/widget-narrow.js' type='text/javascript'%3E%3C/script%3E"));</script><a href="https://www.surebondsa.com/" style="font-weight:normal;font-size:9px;font-family:Tahoma;color:#EEEEEE;text-decoration:none;">Surebond Loan Calculator</a></div>
        <!-- MORTGAGE LOAN CALCULATOR END -->
    
        <?php
    }
    $image="";
    $image.="<!-- Container for the image gallery --> ";
    
    $image.="<div class='container'>";
        $counter=0;
        $images=array();
        $thumbs=array();
        $maximages=10;
            $countimage=0;
        foreach($item->Images as $myimage ){
            foreach($myimage->MandateImage as $theimage ){
                if ($countimage<$maximages){
                    $countimage+=1;
                    $url = (string) $theimage->Url;
                    $FileID = (string) $theimage->FileId;
                    $img_src  =  $upload_dir.$FileID.".png";
                    $img_disp =  $upload_dir.$FileID.".png";
                    $thm_disp = $upload_dir.$FileID."_thumb.png";
                    array_push($images,$img_disp);
                    array_push($thumbs,$thm_disp);
                    $counter=$counter+1;
                }
            }
        }
        $counter=$counter-1;
        for ($i = 0; $i < count($images); $i++) {
            $image.= "<!-- Full-width images with number text -->
                    <div class='mySlides'>
                    <div class='numbertext'>".$i." / ".$counter."</div>
                    <img src='".$images[$i]."' style='width:98%'>
                    </div>";
        }
        $image.= " <!-- Next and previous buttons -->
            <a class='prev' onclick='plusSlides(-1)'>&#10094;</a>
            <a class='next' onclick='plusSlides(1)'>&#10095;</a>
            <!-- Image text -->
            ";
        $cl=0;
        
        $image.= " </div>";
        for ($i = 0; $i < count($images); $i++) {
            if ($cl==0){
                $image.= "<div class='row'>";
            }
            $image.= "<img class='column demo cursor' src='".$images[$i]."' onclick='currentSlide(".($i+1).")' alt=''>";
            $cl=$cl+1; 
            if ($cl==6){
                $cl=0;
                $image.="</div>";
            }            
        }

    $image.= "</div>";       
    
    echo "<table>";
    echo "<tr><td>$title</td><td nowrap>$price</td></tr>";
    echo "<tr><td colspan=2>$image</td></tr>";
    echo "<tr><td>$desc</td><td>$video</td></tr>";
    echo "<tr><td colspan=2>$content</td></tr>";
    
    echo "</table>";
}
?>


 

  

 
 

    
    
 
 
 
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