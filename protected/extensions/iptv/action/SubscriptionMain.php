<?php

class SubscriptionMain{
    
    private $_UserID;
    private $_Login_name;
    private $_Password;
    private $_Citycode;
    private $_ProductID;
    private $_UsergroupID;
    private $_TerminalType;
    private $_Fatherloginname;
    private $_wsdl;
    private $_dblog;
    private $_client;
    private $_data;
    private $_transaction;
            
    public function __construct($UserID,$Login_name,$Password,$Citycode,$ProductID,$UserGroupID,$TerminalType,$Fatherloginname) {
       
        /*
         * Import library and models
         */
        
        Yii::import('application.extensions.MKMSoap.MKM_Soap_Client');
        Yii::import('application.extensions.iptv.abstract.Subscription.*');
        Yii::import('application.models.iptv.IptvServices');
        
        
        $this->_UserID = $UserID;
        $this->_Login_name = $Login_name;
        $this->_Password = $Password;
        $this->_Citycode = $Citycode;
        $this->_ProductID = $ProductID;
        $this->_UsergroupID = $UserGroupID;
        $this->_TerminalType = $TerminalType;
        $this->_Fatherloginname = $Fatherloginname;
        
        $this->_wsdl = Yii::app()->params["wsdl_iptv"];
        
    }
    
    public function SubscriptionAction(){
        
         $req_date = date("Y-m-d H:i:s");
        
         set_time_limit ( 0 );
         ini_set ( "default_socket_timeout", 1 );
         ini_set ( "soap.wsdl_cache_enabled", 0 );
        
        
        try{
            
            
            $this->_client = new MKM_Soap_Client($this->_wsdl, array('encoding' => 'UTF8', 'trace' => 1,'exceptions' => 1));
            $this->_client->setConnectionTimeout(360);
            $this->_client->setOperationTimeout(360);
            
            $request2 = new reqSubscription();
                    $request2->UserID =  $this->_UserID;
                    $request2->Login_name =  $this->_Login_name;
                    $request2->Password = $this->_Password;
                    $request2->Citycode = $this->_Citycode;
                    
                    /*
                     * Array data / single data
                     */
                    
                    if(is_array($this->_ProductID)){
                       for($i=0;$i<count($this->_ProductID);$i++){
                           $request1[$i] = new ProductListItem();
                           $request1[$i]->ProductID = $this->_ProductID[$i];
                   
                           $request[$i] = new Productlist();
                           $request[$i]->ProductListItem = $request1[$i];
                           
                       }
                       $request2->Productlist = $request1;
                    }else{
                           
                           $request1 = new ProductListItem();
                           $request1->ProductID = $this->_ProductID;
                           
                           $request = new Productlist();
                           $request->ProductListItem = $request1;
                           
                       $request2->Productlist = $request1;        
                   
                           
                    }
                    
                    $request2->UsergroupID =$this->_UsergroupID;
                    $request2->TerminalType=$this->_TerminalType;
                    $request2->Fatherloginname= $this->_Fatherloginname;

                    $request3 = new Subscription();
                    $request3->reqSubscription = $request2;
                     
                    $this->_data = $this->_client->Subscription($request3);
                   
            
                    $this->__logapi("subscription",$this->_UserID,$this->_client->__getLastRequest(),$this->_client->__getLastResponse(),$req_date,date("Y-m-d H:i:s"));
            
                    return CJSON::encode(array("Result"=> $this->_data->Result,
                                            "Errordesc"=> $this->_data->Errordesc));
            

            
            
        } catch (Exception $e) {

             $this->__logapi("subscription",$this->_UserID,$this->_client->__getLastRequest(),$e->getMessage(),$req_date,date("Y-m-d H:i:s"));
            
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
