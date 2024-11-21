<?php

// Show all information, defaults to INFO_ALL
//phpinfo();
ini_set('display_errors',1);
error_reporting(E_ALL);

$wsdl = "http://listing.magnerproperties.co.za/Prop24.asmx";
$soapClient = new SoapClient($wsdl);

$client = $soapClient ;
$currSessionID=null;
$currSessionID=getSessionID( $client,$currSessionID,$login,$password,$sessionName);
echo "test";
print_r($currSessionID);
echo "test";

$client.Close();


?>