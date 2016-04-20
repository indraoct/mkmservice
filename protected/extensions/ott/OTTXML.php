<?php
/**
 * @Author      : Indra Octama
 * @CreatedDate : 14 Mei 2014
 * @Purpose     : Library Send XML to OTT service (SLCS)
 */

class OTTXML{

    private $_url;
    private $_dblog;
    private $_transaction; 
    private $_request_time;
    
    public function __construct() {
        
        /**
         * Log DB
         */
        
        Yii::import("application.models.ott.OttServices");
       
        /**
         * IP Address untuk tembak data ke Server EPG SLCS
         */  
        $this->_url = Yii::app()->params["url_service_ott"];
        
        $this->_request_time = date("Y-m-d H:i:s");
    }
    
    private function __sendXML($xmlRequest){
        
        $ch = curl_init($this->_url);
      
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

     try{   
        $data = curl_exec($ch);
        curl_close($ch);
        $objXml = new SimpleXMLElement($data);
        $xml = $objXml->children("SOAP-ENV", true)->Body->children("BOSS", true)->children();
        
        
     
        return $objXml;
       }  catch (Exception $e){
           return false;
       } 
    }
    
    public function __AccountCreation($Username,$EmailAddress,$Password,$ProductID,$type){
   
   $productlist = '<Productcode>'.$ProductID.'</Productcode>' . PHP_EOL;
        

        $xmlRequest = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:AccountCreation>
   <reqAccountCreation>
    <Username>$Username</Username>
    <EmailAddress>$EmailAddress</EmailAddress>
    <Password>$Password</Password>
    <Areacode>0000</Areacode>
    <Productlist>
     <ProductListItem>
      $productlist
     </ProductListItem>
    </Productlist>
    <Terminaltype>$type</Terminaltype>
    <Timestamp></Timestamp>
   </reqAccountCreation>
  </BOSS:AccountCreation>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        
        $return = $this->__sendXML($xmlRequest);
        
        if($return != false){
           $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->AccountCreationRsp->children();
        }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        }
       
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
        
        
    }  
    
    public function __SubscribeProduct($Username,$ProductID){
        
        $productlist = '<ProductListItem>' . PHP_EOL;
$productlist .= '<Productcode>'.$ProductID.'</Productcode>' . PHP_EOL;
$productlist .= '</ProductListItem>' . PHP_EOL;


$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:SubscribeProduct>
   <reqSubscribeProduct>
    <Username>$Username</Username>
    <Productlist>
         $productlist
    </Productlist>
    <Timestamp></Timestamp>
   </reqSubscribeProduct>
  </BOSS:SubscribeProduct>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
         
         
       $return = $this->__sendXML($xmlRequest);
       if($return != false){ 
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->SubscribeProductRsp->children();
       
       }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        } 
        
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
        
    }
    
    public function __Unbinding($Username){
        
        $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:Unbinding>
   <reqUnbinding>
    <Username>$Username</Username>
    <Timestamp></Timestamp>
   </reqUnbinding>
  </BOSS:Unbinding>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        
        $return = $this->__sendXML($xmlRequest);
      
      if($return != false){  
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->UnbindingRsp->children();
      
       }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        } 
        
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
    }
    
    public function __Suspension($Username){
        
        $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:Suspension>
   <reqSuspension>
    <Username>$Username</Username>
    <Timestamp></Timestamp>
   </reqSuspension>
  </BOSS:Suspension>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        
        $return = $this->__sendXML($xmlRequest);
        
        if($return != false){
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->SuspensionRsp->children();
       
        }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        }
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
    }
    
    public function __Resetpassword($Username,$Newpassword){
        
                $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:ResetPassword>
   <reqResetPassword>
    <Username>$Username</Username>
    <Newpassword>$Newpassword</Newpassword>
    <Timestamp></Timestamp>
   </reqResetPassword>
  </BOSS:ResetPassword>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
    
      $return = $this->__sendXML($xmlRequest);
        
       if($return != false){
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->ResetPasswordRsp->children();
      
        }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        }
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
    }
    
    public function __Reactivation($Username){
        $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:Reactivation>
   <reqReactivation>
    <Username>$Username</Username>
    <Timestamp></Timestamp>
   </reqReactivation>
  </BOSS:Reactivation>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        
        $return = $this->__sendXML($xmlRequest);
        
        if($return != false){
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->ReactivationRsp->children();
        
        }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        }
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
    }
    
    public function __AccountCancelation($Username,$Email){
        
        $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:BOSS="http://tempuri.org/BOSS.xsd">
 <SOAP-ENV:Body>
  <BOSS:AccountCancellation>
   <reqAccountCancellation>
    <Username>$Username</Username>
    <EmailAddress>$Email</EmailAddress>
    <Timestamp></Timestamp>
   </reqAccountCancellation>
  </BOSS:AccountCancellation>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
    
        $return = $this->__sendXML($xmlRequest);
        if($return != false){
        $data = $return->children("SOAP-ENV", true)->Body->children("BOSS", true)->AccountCancellationRsp->children();
       
        }else{
           $data = new stdClass();
           $data->Result = "99";
           $data->Errordesc = "Exception";
        }
            return array("Result"=>(String) $data,
                     "Errordesc"=>$data->Errordesc);
        
        
    }
    

}
?>
