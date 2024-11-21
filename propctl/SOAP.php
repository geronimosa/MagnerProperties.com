<?php


/* Production SOAP */
$login = "ExdevMagner";
$password = "M@gn3r";
$url = "https://exdev.server.propctrl.com/v5.4/Basic/AgencyIntegration.svc?wsdl";
$certificate="/var/www/vhosts/surebondsa.com/magnerproperties.co.za/propctl/Exdev PropCtrl.cer";

$__LTE_SOAP_wsdl = $url;
$__LTE_username = $login;
$__LTE_password = $password;
$__LTE_DB_Session_ID = "LTE_SESSION_ID";
/*-----------------*/


function getSoapClient($strSoapWsdl = '') {

    $soapClient = null;
    
    if (trim($strSoapWsdl) != '') {
        $opts = array(
            'ssl' => array(
                'ciphers' => 'RC4-SHA',
                'verify_peer' => true,
                "allow_self_signed"=>false,
                'verify_peer_name' => true
            )
        );
        $certificate="/var/www/vhosts/surebondsa.com/magnerproperties.co.za/propctl/property24testingca.cer";
        echo "<pre>\t data \t\n";
       // echo file_get_contents($certificate);
        echo "</pre>";
        
        $soapClient = new SoapClient($strSoapWsdl,array(
            'local_cert' => $certificate,
            'trace' => 1,
            'exceptions' => true, 
            "features" => SOAP_USE_XSI_ARRAY_TYPE,
            "uri"           => "urn:xmethods-delayed-quotes",
            "style"         => SOAP_RPC,
            "use"           => SOAP_ENCODED,
            "soap_version"  => SOAP_1_2,
            "stream_context" => $opts
            ));
        //echo file_get_contents($soapClient);
       

    }
    return $soapClient;
}

function __construct($username, $password) {
	$WSDL='https://www1.gsis.gr/webtax2/wsgsis/RgWsPublic/RgWsPublicPort?WSDL';
	$location='https://www1.gsis.gr/webtax2/wsgsis/RgWsPublic/RgWsPublicPort';
	$strWSSENS='http://schemas.xmlsoap.org/ws/2002/07/secext';
	
	// Code copied and modified from http://www.php.net/manual/en/soapclient.soapclient.php#97273
	
	$objSoapVarUser = new SoapVar($username, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS); 
	$objSoapVarPass = new SoapVar($password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS); 

	$objWSSEAuth = (object) array('Username' => $objSoapVarUser,  'Password' => $objSoapVarPass);
	$objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);

	$objWSSEToken = (object ) array('UsernameToken' => $objSoapVarWSSEAuth); 
	$objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS); 

	$objSoapVarHeaderVal = new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS); 
	$objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal);

	$this->objClient = new SoapClient($WSDL, array('trace' => 0)); 
	$this->objClient->__setLocation($location);
	$this->objClient->__setSoapHeaders(array($objSoapVarWSSEHeader)); 		
}

function getSessionID($soapClient = null, $currSessionID = null,$strUsername = '', $strPassword = '',  $dbSessionName = '') {

//    echo "DB Session $currSessionID<br />";
    if (isset($soapClient) && isset($currSessionID) && (trim($currSessionID) != '') && isset($db_conn) && (trim($dbSessionName) != '')) {
        $currSessionID = checkCurreSessionID($soapClient,$currSessionID); // just checks if Ihave local session ID in the DB to send.
    }
    if (!isset($currSessionID) || (trim($currSessionID) == '')) {
        $str_md5_password = md5($strPassword);
        $login_params = array('Username' => $strUsername
                                ,'Password' => $str_md5_password
                                );
        $response = $soapClient->__soapCall('ListOfContinentsByCode', $login_params);

        if (isset($response["intReturnCode"]) && ((int)($response["intReturnCode"]) == 1)) {
            $currSessionID = $response["strSessionID"];
            //updateDbSoapSession($db_conn, $dbSessionName, $currSessionID);
        } else {
            $currSessionID = null;
        }
    }
    return $currSessionID;
}


function getDeviceRequests($soapClient = null, $currSessionID = null){
    $strResponse = '';
    if (isset($soapClient) && isset($currSessionID) && (trim($currSessionID) != '')) {
            $req_prams = array('strSessionID' => $currSessionID
                                ,'strProduct' => 'lte');
        $response = $soapClient->__soapCall('getDeviceRequests', $req_prams);
        if (isset($response["intReturnCode"]) && ((int)($response["intReturnCode"]) == 1)) {
            $strResponse = $response["strResults"];
        } else {
            echo "Could not get Device requests.<br />";
        }
    } else {
        echo "Could not get Soap Session.<br />";
    }
    return $strResponse;
}













