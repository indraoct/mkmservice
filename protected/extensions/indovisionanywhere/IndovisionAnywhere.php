<?php

/**
 * Class IndovisionAnywhere
 * @author : Indra Octama
 * @created_date : 11 Desember 2015
 */

class IndovisionAnywhere{
 
    private $_url;
    private $_token;
    private $_dblog;
    private $_transaction;
    
    public function __construct() {
        
        Yii::import("application.models.Indovision.LogIndovision");
        
        $this->_url = "";
        $this->_token = "";
        
    }
    
    /**
     * curl POST to server Indovision Anywhere 
     */
    
    private function __curl(array $data_array){
        
        $data_string = http_build_query($data_array);
        try{
            $ch = curl_init($this->_url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                 
            curl_setopt($ch, CURLOPT_POSTFIELDS,
               $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     

            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }catch(Exception $e){
            
            return CJSON::encode(array("error_code"=>"4545","error_msg"=>$e->getMessage()));
        }

    }
    
    /**
     * getUserData
     * @param Integer $userid
     * 
     * @return JSON 
     */
    public function getUserData($userid = "",$password = ""){
        
        /**
         * BELOM FUNGSI
         * INDRA OCTAMA
         * 22 Desember 2015
         */
        
        return CJSON::encode(array("error_code"=>"99","error_msg"=>"Login with Indovision Anywhere not yet implemented"));
       
        $data_array = array("token"=>$this->_token,
                            "action"=>"GetUserData",
                            "userid"=>$userid,
                            "password"=>$password
                            );
        //request
        $this->__logapi("GetUserData_req", http_build_query($data_array), "", date("Y-m-d H:i:s"));
        
        $response = $this->__curl($data_array);
        
         //response
        $this->__logapi("GetUserData_res", "", $response, "", date("Y-m-d H:i:s"));
        
        return $response;
        
    }
    
    /**
     * LOG OTT
     * Indovision Anywhere
     */
    private function __logapi($servicename="",$msg_request="",$msg_response="",$request_time="",$response_time="",$ip_address="",$mac_address=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new LogIndovision();
               
               try{
                       $this->_dblog->attributes  = array("servicename"=>($servicename !="")?$servicename:"",
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
