<?php
/**
 * Class UserController
 * 
 * @author Indra Octama <indra.octama@mncgroup.com>
 * @createddate 18 Januari 2016
 */
class UserController extends Controller
{
    private $_http;
    private $_transaction;
    private $_dblog;
    private $_token;
    
    
    public function init(){
        
        Yii::import("application.models.Samsung.LogSamsung");
        Yii::import("application.extensions.samsung.GeneralFunction");
       
        $this->_http = new CHttpRequest();
        $this->_token = Yii::app()->params["token_samsung_service"];

    }

    /**
     * Action Login
     * 
     * @param String $token 
     * @param String $userid 
     * @param String $password 
     * 
     * @return JSON 
     */
    public function actionLogin()
    {
        
        if($this->_http->isPostRequest == true){
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"";
            $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
            
             //log samsung
            $this->__logapi("login_req",$userid,"", http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
            
            if($this->_token == $token){
                //login
                 $slcs = GeneralFunction::getUserProductCode($userid, $password);
                 if($slcs["Result"] == "0"){
                     
                        $get_product_code = GeneralFunction::getUserProductCode($userid, $password);
                                          
                        $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success","data"=>array("product"=>isset($get_product_code["Result"])?(($get_product_code["Result"]=="0")?$get_product_code["Productlist"]:""):"")));
                        header('Content-Type: application/json');
                        echo $response;

                        $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                     
                 }else{
                     
                        $response = CJSON::encode(array("error_code"=>"9999","error_msg"=>"Invalid userid or password"));

                        header('Content-Type: application/json');
                        echo $response;

                        $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));

                 }
            }else{
                    $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
            }
          
        }else{
            throw new CHttpException(403,'Forbidden.');
        }
    }
    
    /**
     * action Register
     * @param String $token
     * @param String $userid 
     * @param String $password
     * 
     * @return JSON
     */
    public function actionRegister()
    {
      if($this->_http->isPostRequest == true){
          
          $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
          $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:0;
          $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
          $vouchercode = isset($_REQUEST["vouchercode"])?$_REQUEST["vouchercode"]:"";
          $imei = isset($_REQUEST["imei"])?$_REQUEST["imei"]:"";
          $productid = isset($_REQUEST["productid"])?$_REQUEST["productid"]:"";
          
          
          //log samsung
            $this->__logapi("register_req",$userid,"", http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
          
            if($this->_token == $token){
                
                $data_array = array(
                                    "userid"=>$userid,
                                    "password"=>$password,
                                    "vouchercode"=>$vouchercode,
                                    "imei"=>$imei);

                        //insert data user moviebay
                        $insertdatauser = GeneralFunction::insertUserSamsung($data_array);

                        
                            if($insertdatauser["error_code"] == "00"){
                                
                                $dataUser = array("userid" => $userid,
                                                  "password" => $password,
                                                  "product_id" => $productid,
                                                   );
                               //register to SLCS 
                               $registerslcs =   GeneralFunction::registerUserSlcs($dataUser);
                               
                               if($registerslcs == true){
                                   
                                    //get product code
                                    $get_product_code = GeneralFunction::getUserProductCode($userid,$password);
                                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success","data"=>array("product"=>isset($get_product_code["Result"])?(($get_product_code["Result"]=="00")?$get_product_code["Productlist"]:""):""  )));     
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));

                                   
                               }else{
                                    /*
                                     * Delete User Jika
                                     */
                                    GeneralFunction::deleteUserSamsung($userid);
                                    
                                    if($registerslcs == true){
                                        /*
                                         * Jika sukses daftar di slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"5555","error_msg"=>"Email is Exist in SLCS system, try to register another email"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }else{
                                        /*
                                         * Jika tidak sukses daftar slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"6666","error_msg"=>"Failed To Register in SLCS"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }
                               }
                                

                            }else{
                                    //error from model
                                
                                    $response = CJSON::encode(array("error_code"=>"7878","error_msg"=>$insertdatauser["error_msg"]));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));

                            }

                
            }else{
                    $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
            }
          
         
      }else{
          throw new CHttpException(403,'Forbidden.');
      }
        
    }
    
    
    
    /**
     * LOG Moviebay
     */
    private function __logapi($servicename="",$userid="",$email="",$msg_request="",$msg_response="",$request_time="",$response_time=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new LogSamsung();
               
               try{
                       $this->_dblog->attributes  = array("servicename"=>($servicename !="")?$servicename:"",
                                                           "userid"=>$userid,
                                                           "email"=>$email,
                                                           "msg_request"=>($msg_request != "")?$msg_request:"",
                                                           "msg_response"=>($msg_response != "")?$msg_response:"",
                                                           "log_date"=> date("Y-m-d H:i:s"),
                                                           "request_time"=>($request_time != "")?$request_time:"0000-00-00 00:00:00",
                                                           "response_time"=>($response_time != "")?$response_time:"0000-00-00 00:00:00");
                       $this->_dblog->save();
                       $this->_transaction->commit();
               }catch(Exception $e){
                   $this->_transaction->rollback();
               }     
    }
    
}
