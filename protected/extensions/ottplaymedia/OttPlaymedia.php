<?php

/**
 * Class OttPlaymedia
 * @author : Indra Octama
 * @created_date : 11 Desember 2015
 */

class OttPlaymedia{
 
    private $_url;
    private $_dblog;
    private $_transaction;
    private $_auth_username;
    private $_auth_password;
    
    
    public function __construct() {
        
        Yii::import("application.models.Playmedia.LogPlaymedia");
        Yii::import("application.extensions.ottplaymedia.RestClient");
        
        $this->_url = Yii::app()->params["url_ottplaymedia"];
        $this->_auth_username = Yii::app()->params["auth_username_ottplaymedia"];
        $this->_auth_password = Yii::app()->params["auth_password_ottplaymedia"];
        
    }
    
    /**
     * curl POST to server OTT Playmedia 
     */
    
    private function __curl(array $data_array){
        
        try{
            
            $request = new RestClient($this->_url,'post', $data_array);
            $request->setUsername($this->_auth_username);
            $request->setPassword($this->_auth_password);
            $request->setAcceptType('application/json');
            $resultat = $request->execute();
            
            return $resultat;

        }catch(Exception $e){
            
            return CJSON::encode(array("error_code"=>"4545","error_msg"=>$e->getMessage()));
        }

    }
    
    /**
     * getUserData
     * @param Integer $userid
     */
    public function getUserData($userid = ""){
      
        $data_array = array("userid"=>$userid);
        
        //request
        $this->__logapi("GetUserData_req", http_build_query($data_array), "", date("Y-m-d H:i:s"));
        
        $response = $this->__curl($data_array);
        $response_arr = CJSON::decode($response,true);
         //response
        $this->__logapi("GetUserData_res", "", $response, "", date("Y-m-d H:i:s"));
        
        if($response_arr["error_code"] == "00"){
           
            $data = isset($response_arr["data"])?$response_arr["data"]:array();
            
            if(count($data) > 0){
             
                $data_res = array("data"=>array("name"=>$data[0]["customerName"],
                                  "dateofbirth"=>$data[0]["dateofbirth"],
                                  "gender" => ($data[0]["gender"] == "Pria")?"M":"F",
                                  "phonenumber" => $data[0]["phonenumber"],
                                  "email" => strtolower($data[0]["email"]),
                                  "password" => $data[0]["passwordOTT"],
                                  "userid" => $userid,
                                  "userstatus" => ($data[0]["userstatus"] == "Permanent Activation")?"1":"0"));
                
                unset($response_arr["data"]);
                
                $data_response = array_merge($response_arr,$data_res);
                
                return CJSON::encode($data_response);
                
            }else{
                
               return $response;
            }
            
            
        }else{
        
           return $response;
        }
    }
    
     /*
     * LOG OTT
     */
    private function __logapi($servicename="",$msg_request="",$msg_response="",$request_time="",$response_time="",$ip_address="",$mac_address=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new LogPlaymedia();
               
               try{
                       $this->_dblog->attributes  = array( "id"=> date("YmdHis").mt_rand(10,100), 
                                                           "servicename"=>($servicename !="")?$servicename:"",
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
