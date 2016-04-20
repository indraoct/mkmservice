<?php

class ChangeProductsMain{
    
    private $_UserID;
    private $_Productlist;
    private $_wsdl;
    private $_dblog;
    private $_client;
    private $_data;
    private $_transaction;
            
    public function __construct($UserID,$ProductList) {
       
        /*
         * Import library and models
         */
        
        Yii::import('application.extensions.MKMSoap.MKM_Soap_Client');
        Yii::import('application.extensions.iptv.abstract.ChangeProducts.*');
        Yii::import('application.models.iptv.IptvServices');
        
        
        $this->_UserID = $UserID;
        $this->_Productlist = $ProductList;
        
        $this->_wsdl = Yii::app()->params["wsdl_iptv"];
        
    }
    
    public function ChangeProductsAction(){
        
         $req_date = date("Y-m-d H:i:s");
        
         set_time_limit ( 0 );
         ini_set ( "default_socket_timeout", 1 );
         ini_set ( "soap.wsdl_cache_enabled", 0 );
        
        
        try{
            
            
            $this->_client = new MKM_Soap_Client($this->_wsdl, array('encoding' => 'UTF8', 'trace' => 1,'exceptions' => 1));
            $this->_client->setConnectionTimeout(360);
            $this->_client->setOperationTimeout(360);
            
            if(is_array($this->_Productlist)){
                    for($i=0;$i<count($this->_Productlist);$i++){

                        $request[$i] = new ChangeListItem();
                        $request[$i]->ProductID = $this->_Productlist[$i]['ProductID'];
                        $request[$i]->Operation = $this->_Productlist[$i]['Operation'];
                        
                        $requestx[$i] = new Productlist();
                        $requestx[$i]->ChangeListItem = $request[$i];
                        
                        
                    }
                }else{
                        $request = new ChangeListItem();
                        $request->ProductID = $this->_Productlist['ProductID'];
                        $request->Operation = $this->_Productlist['Operation'];
                        
                        $requestx = new Productlist();
                        $requestx->ChangeListItem = $request;
                    
                }

                $request1 = new reqChangeProducts();
                $request1->UserID = $this->_UserID;
                $request1->Productlist = $request;                

                $request2 = new ChangeProducts();
                $request2->reqChangeProducts = $request1;


                $this->_data = $this->_client->ChangeProducts($request2);
                   
            
                    $this->__logapi("changeproducts",$this->_UserID,$this->_client->__getLastRequest(),$this->_client->__getLastResponse(),$req_date,date("Y-m-d H:i:s"));
            
                    return CJSON::encode(array("Result"=> $this->_data->Result,
                                            "Errordesc"=> $this->_data->Errordesc));
            

            
            
        } catch (Exception $e) {

             $this->__logapi("changeproducts",$this->_UserID,$this->_client->__getLastRequest(),$e->getMessage(),$req_date,date("Y-m-d H:i:s"));
            
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
