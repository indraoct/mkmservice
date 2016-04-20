<?php

class UnbindMain{
    
    private $_UserID;
    private $_wsdl;
    private $_dblog;
    private $_client;
    private $_data;
    private $_transaction;
            
    public function __construct($UserID) {
       
        /*
         * Import library and models
         */
        
        Yii::import('application.extensions.MKMSoap.MKM_Soap_Client');
        Yii::import('application.extensions.iptv.abstract.Unbind.*');
        Yii::import('application.models.iptv.IptvServices');
        
        
        $this->_UserID = $UserID;
        $this->_wsdl = Yii::app()->params["wsdl_iptv"];
        $this->_dblog = new IptvServices();
    }
    
    public function UnbindAction(){
        
         $req_date = date("Y-m-d H:i:s");
        
         set_time_limit ( 0 );
         ini_set ( "default_socket_timeout", 1 );
         ini_set ( "soap.wsdl_cache_enabled", 0 );
         
        
        try{
            $this->_client = new MKM_Soap_Client($this->_wsdl, array('encoding' => 'UTF8', 'trace' => 1,'exceptions' => 1));
            $this->_client->setConnectionTimeout(360);
            $this->_client->setOperationTimeout(360);
            
                $request = new reqUnbindSTB();
                $request->UserID = $this->_UserID;

                $request1 = new UnbindSTB();
                $request1->reqUnbindSTB = $request;


                $this->_data = $this->_client->UnbindSTB($request1);
                
                $this->__logapi("unbind",$this->_UserID,$this->_client->__getLastRequest(),$this->_client->__getLastResponse(),$req_date,date("Y-m-d H:i:s"));
                
                 /*
                  * Karena balasan return = null maka alternatif adalah dengan cara sbb:
                  */
                   if($this->_client->__getLastResponse() != null){                 
                        $objXml = new SimpleXMLElement($this->_client->__getLastResponse());
                        $dataXml = $objXml->children("SOAP-ENV", true)->Body->children("BOSS", true)->UnbindSTBRsp->children();
                        
                        if(!isset($dataXml->Result)){
                            $dataXml->Result = "";
                        }
                        
                        
                        if(!isset($dataXml->Errordesc)){
                            $dataXml->Errordesc = "";
                        }
                        
                            return CJSON::encode(array("Result"=>$dataXml->Result->__toString(),
                                                  "Errordesc"=>$dataXml->Errordesc->__toString()));
                   }else{
                            return CJSON::encode(array("Result"=>"99",
                                                  "Errordesc"=>"Failed To get Response"));
                       
                   }
            
        }catch(Exception $e) {

                 $this->__logapi("bind",$this->_UserID,$this->_client->__getLastRequest(),$e->getMessage(),$req_date,date("Y-m-d H:i:s"));
            
                 return CJSON::encode(array("Result"=>"99",
                                   "Errordesc"=>$e->getMessage()));
        }
       
        
    }
    
    
    
     /*
     * log API IPTV
     */
    
    private function __logapi($servicename="",$userid="",$msg_request="",$msg_response="",$request_time="",$response_time="",$ip_address="",$mac_address=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new IptvServices();
               
               try{
                       $this->_dblog->attributes  = array( "id"=> date("YmdHis").mt_rand(10,99),
                                                           "servicename"=>($servicename !="")?$servicename:"",
                                                           "userid"=>$userid,
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

