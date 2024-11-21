
<!-- Import bootstrap cdn -->
	

<!-- Import jquery cdn -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
                integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
		crossorigin="anonymous">
	</script>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
		crossorigin="anonymous">
	</script>
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
ini_set('display_errors',0);
error_reporting(E_ALL);
wp_enqueue_style( 'saleslisting', plugins_url( 'saleslisting.css' , __FILE__ ) );
//wp_enqueue_style("bootstrap","https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css");
//wp_enqueue_script("jquery","https://code.jquery.com/jquery-3.5.1.slim.min.js");
//wp_enqueue_script("bootstrap.bundle","https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js");


function displaylist($atts = [], $content = null, $tag = ''){
    
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $wporg_atts = shortcode_atts(
        array(
            'title' => 'Magner Properties Sales Listing',
            'images' => 2,
            'align' => 'left',
            'mandate' =>'all',            
        ), $atts, $tag
    );
    
    $wsdl = "http://listing.magnerproperties.co.za/Magner.asmx?WSDL";
    
    $soapClient = new SoapClient($wsdl); 
    $propertylist=$soapClient->CurrentProperties_XML();
    $html='';
    
    $html .= '<h2 class="ListTitle">' . esc_html__( $wporg_atts['title'], 'wporg' ) . '</h2>';
    $html.="<table class='displaytable' border='0'>";
    
    $pluginpath=plugin_dir_url(__FILE__);
    

    

     $array = explode("<<>>",$propertylist->CurrentProperties_XMLResult); 
     
     
     foreach($array as $xmlitem ){
        // print_r ($xmlitem);      
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
        
        if ($wporg_atts['mandate']=='all' xor $wporg_atts['mandate']==$item->MandateType ){

            $suburbname="";
            $mysuburb=Array();
            try
                {
                   // print_r("suburb:");
                    $params = array("SuburbId" => $item->SuburbId);
                   // print_r($params);
                    $suburb=$soapClient->GetSuburb($params);   
                    $suburbname=$suburb->GetSuburbResult;
                   // print_r($suburbname);
                    $mysuburb=json_decode($suburbname);                    
                }
            catch(SoapFault $exception)
                {
                    $mysuburb=array("SuburbId" => $item->SuburbId);
//                    return $exception->getmessage();
                }

            $heading="";
            $heading="<table width=100%><tr>";
            $heading.="<td align='center' valign='middle'>$item->MarketingHeading</td>";
            $heading.="</tr></table>";
            
            $images=MakeImage($item,$wporg_atts);

            $description="";
            $description.= "<Table width=100%><tr> ";
            $description.= ("<td width='70%' valign='top'><div class='propertydescription'>".$item->MarketingDescription."</div></td>");            
            $description.= "<td width='30%' valign='top'>";
            $description.= "<div class='featurelist'>Features:<ul>";
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
                            $description.= "<li>$key </li>";
                        }else{
                            $description.= "<li>$key ($Feature)</li>";
                        }
                    }
            $description.= "</ul></div>";
            $description.= "</td>";
            $description.= "</tr></table>";

            $title="";
            $title.= ("<table><tr><td align='left'><span class='propertyarea'><b>Suburb:</b> $mysuburb->SuburbName, $mysuburb->City, $mysuburb->Province</span><br>");
            $title.= ("(".$item->MandateId.")  ");
            $value=number_format($item->ListPrice*1,0,'.',' ');
            $title.= ("<span class='propertyprice'><b>Price:</b>  ".$value." </span></td>");
            if ($item->YouTubeVideoUrl<>""){
                $title.= "<td><a class='button' onclick='myFunction()' href='".$item->YouTubeVideoUrl."' target='magnerProperty'>Video</a></td>";
            }            
            $title.= "</tr></table>";
            
            $html.= "<Table id='$item->MandateId' name='$item->MarketingHeading' data-toggle='modal' data-target='#magnerProperty' class='displaytable' border='0'>";
            $html.= "<tr><td valign='top'>$heading</td></tr>";
            $html.= "<tr><td valign='top'>$title</td></tr>";
            $html.= "<tr><td valign='top'>$images</td></tr>";
            $html.= "<tr><td valign='top'>$description  <span class='seemore'>Read more...<span></td></tr>";
            $html.= "</table><hr>";

            
            
        }

    }
    $html.= "</Table>";
    
    $html.= '';
    
    
    
    $myscript1=' <!-- Modal -->
                    <div id="magnerProperty" class="modal fade" aria-labelledby="MagnerLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content magner">
                                <div class="modal-header" style="width: 100%; height: 30px;">
                                    <h5 id="MagnerLabel" class="modal-title">Loading your request</h5><button id="submit" class="btn btn-success btn-sm bclose" type="button" data-toggle="modal" data-target="#magnerProperty"> Close </button>
                                    <button class="close closex" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true"> × </span> </button>
                                </div>
                                <div class="modal-body magner-body">
                                    <!-- 
                                        Data passed is displayed in this part of the modal body 
                                    -->
                                    <h6 id="modal_body">No Data </h6>
                                    <h6 id="modal_text">No Data </h6>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
		    ';
    
    $myscript2='<script type="text/javascript">
                $("#magnerProperty").on("show.bs.modal", function(e) {
                    var $relatedTarget = e.relatedTarget.id;
                    var str = "<iframe src=\''.$pluginpath.'showlisting.php?id="
                                   + $relatedTarget
                                  + "\'  class=\'iframe_window\' ></iframe>" ;
                    $("#modal_body").html(str);   
                    
                });
                
                $("#magnerProperty").on("shown.bs.modal", function(e) {
                    var $relatedTarget = e.relatedTarget.name; 
                    var Steve = ""+$relatedTarget;
                    $("#MagnerLabel").html("Selected listing");
                    $("#modal_text").html("");
                });
                
	</script>';
    
    

    $returnstring=$myscript1.$html.$myscript2;
    return $returnstring;

    
    
    
}