<?php
/**
 * Class Ott
 * @Description : Perfect Log OTT
 */
class ClassOtt{
    
    
    private $_ott;
    private $_dblog;
    private $_transaction; 
    private $_request_time;
    
    
    public function __construct(){
     
       /**
         * OTTXML
         */
        Yii::import("application.extensions.ott.OTTXML");
       
        $this->_ott = new OTTXML();
        
        /**
         * Log DB
         */
        
        Yii::import("application.models.ott.OttServices");
     
        /**
         * IP Address untuk tembak data ke Server EPG SLCS
         */  
        $this->_url = Yii::app()->params["url_service_ott"];
        /**
         * 
         */
        $this->_request_time = date("Y-m-d H:i:s");
        
        
    }
    
    
    public function AccountCreation($Username,$EmailAddress,$Password,$ProductID,$type){
   
        $objData = $this->_ott->__AccountCreation($Username, $EmailAddress, $Password, $ProductID, $type);
       
        $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("accountcreation",$Username,$EmailAddress, http_build_query(array("Username"=>$Username,"EmailAddress"=>$EmailAddress,"Password"=>$Password,"ProductID"=>$ProductID,"type"=>$type)),  CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        return $return;
        
    }  
    
    public function SubscribeProduct($Username,$ProductID){
        
        $objData = $this->_ott->__SubscribeProduct($Username, $ProductID);
       
         $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("subscribeproduct",$Username,"", http_build_query(array("Username"=>$Username,"ProductID"=>$ProductID)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        return $return;
    }
    
    public function Unbinding($Username){
       
        $objData = $this->_ott->__Unbinding($Username);
       
         $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("unbinding",$Username,"", http_build_query(array("Username"=>$Username)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        return $return;
    }
    
    public function Suspension($Username){
        
        $objData = $this->_ott->__Suspension($Username);
        
        $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("suspension",$Username,"", http_build_query(array("Username"=>$Username)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        return $return;
    }
    
    public function Resetpassword($Username,$Newpassword){
        
        $objData = $this->_ott->__Resetpassword($Username, $Newpassword);
       
        $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("resetpassword",$Username,"", http_build_query(array("Username"=>$Username,"Newpassword"=>$Newpassword)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        return $return;
    }
    
    public function Reactivation($Username){
       
        $objData = $this->_ott->__Reactivation($Username);
       
        
        $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("reactivation",$Username,"", http_build_query(array("Username"=>$Username)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
        
        
        return $return;
    }
    
    public function AccountCancelation($Username,$Email){
        
        $objData = $this->_ott->__AccountCancelation($Username, $Email);
       
        $return["Result"] = $objData["Result"];
        $return["Errordesc"] =  $objData["Errordesc"]->__toString();
        
        $this->__logapi("accountcancelation",$Username,$Email, http_build_query(array("Username"=>$Username,"EmailAddress"=>$Email)),CJSON::encode($return), $this->_request_time, date("Y-m-d H:i:s"));
    
        
        return $return;
        
    }
    
    
    
    /*
     * LOG OTT
     */
    private function __logapi($servicename="",$userid="",$email="",$msg_request="",$msg_response="",$request_time="",$response_time="",$ip_address="",$mac_address=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new OttServices();
               
               try{
                       $this->_dblog->attributes  = array( "id"=> date("YmdHis").mt_rand(10,99),
                                                           "servicename"=>($servicename !="")?$servicename:"",
                                                           "userid"=>($servicename !="")?$userid:"",
                                                           "email"=>($servicename !="")?$email:"",
                                                           "msg_request"=>($msg_request != "")?$msg_request:"",
                                                           "msg_response"=>($msg_response != "")?$msg_response:"",
                                                           "log_date"=> date("Y-m-d H:i:s"),
                                                           "request_time"=>($request_time != "")?$request_time:"0000-00-00 00:00:00",
                                                           "response_time"=>($response_time != "")?$response_time:"0000-00-00 00:00:00",
                                                           "ip_address"=>($ip_address != "")?$ip_address:"",
                                                           "mac_address"=>($mac_address != "")?$mac_address:"");
                       $this->_dblog->save();
                       $this->_transaction->commit();
               }catch(Exception $e){
                   $this->_transaction->rollback();
               }     
    }
    
    
    
}
