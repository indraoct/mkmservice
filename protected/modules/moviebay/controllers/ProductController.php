<?php
/**
 * Class ProductController
 * 
 * @author Indra Octama <indra.octama@mncgroup.com>
 * @createddate 11 Desember 2015
 */
class ProductController extends Controller
{
    private $_http;
    private $_transaction;
    private $_dblog;
    private $_token;
    private $_checkuser;
    
    
    public function init(){
        
        Yii::import("application.extensions.moviebay.GeneralFunction");
        Yii::import("application.models.Moviebay.LogMoviebay");
        Yii::import("application.extensions.iptv.action.*");
       
        $this->_http = new CHttpRequest();
        $this->_token = Yii::app()->params["token_moviebay_service"];
        
        
    }

   /**
    * action add product
    */
    public function actionAdd()
    {
       
        if($this->_http->isPostRequest == true ){
            
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"";
            $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
            $productcode = isset($_REQUEST["productcode"])?(is_array($_REQUEST["productcode"])?$_REQUEST["productcode"]:array()):array();

            //log moviebay
            $this->__logapi("addproduct_req",$userid,"", CJSON::encode($_REQUEST), "", date("Y-m-d H:i:s"));
             
            if($token == $this->_token){
                
                $this->_checkuser = new QueryOrderProductMain($userid, $password);
                $chekuser = CJSON::decode($this->_checkuser->QueryOrderProductAction(),true);
                
                if($chekuser["Result"] == "0"){
                    
                    $addproduct = GeneralFunction::addProductSlcs($userid, $productcode);
                    
                    if($addproduct == true){
                        
                        $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));

                        header('Content-Type: application/json');
                        echo $response;

                        $this->__logapi("addproduct_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                        
                    }else{
                        
                        $response = CJSON::encode(array("error_code"=>"9994","error_msg"=>"Failed To Add Product"));

                        header('Content-Type: application/json');
                        echo $response;

                        $this->__logapi("addproduct_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                        
                    }
                    
                }else{
                    $response = CJSON::encode(array("error_code"=>"9999","error_msg"=>"Invalid userid or password"));

                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("addproduct_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                }
                
            }else{
             
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("addproduct_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));

            }
        }else{
            throw new CHttpException(403,'Forbidden.'); 
        }
    }
    
    
    /**
     * change product vip
     * @throws CHttpException
     */
    public function actionChangeproductvip(){
        
        
        if($this->_http->isPostRequest == true ){
            
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
            $productcode = isset($_REQUEST["product_id"])?$_REQUEST["product_id"]:"";

            
            if($token == $this->_token){
                    //log moviebay
                    $this->__logapi("addproduct_req",$email,"", CJSON::encode($_REQUEST), "", date("Y-m-d H:i:s"));

                    $usermoviebay = GeneralFunction::getUserMoviebay($email);

                    if($usermoviebay != null){

                       if($usermoviebay["usertype"] == "3"){ 

                          $addproduct =  GeneralFunction::addProductSlcs($email, array(0=>$productcode));
                          
                          if($addproduct == true){
                        
                                $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }else{

                                $response = CJSON::encode(array("error_code"=>"9994","error_msg"=>"Failed To Add Product"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }

                       }elseif($usermoviebay["usertype"] == "2"){
                           
                           $addproduct =  GeneralFunction::addProductSlcs($usermoviebay["userid"].Yii::app()->params["suffix_ottplaymedia"], array(0=>$productcode));
                           
                           if($addproduct == true){
                        
                                $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }else{

                                $response = CJSON::encode(array("error_code"=>"9994","error_msg"=>"Failed To Add Product"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }
                           
                       }else{
                           
                           $addproduct =  GeneralFunction::addProductSlcs($usermoviebay["userid"], array(0=>$productcode));
                           
                           if($addproduct == true){
                        
                                $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }else{

                                $response = CJSON::encode(array("error_code"=>"9994","error_msg"=>"Failed To Add Product"));

                                header('Content-Type: application/json');
                                echo $response;

                                $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                           }
                           
                       } 

                    }else{
                        
                        $response = CJSON::encode(array("error_code"=>"9999","error_msg"=>"Invalid userid or email"));

                        header('Content-Type: application/json');
                        echo $response;

                        $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));

                    }
                    
            }else{
                
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("addproduct_res",$email,"", "", $response, "", date("Y-m-d H:i:s"));
                
                
            }
                   
        }else{
            throw new CHttpException(403,'Forbidden.'); 
        }
        
        
    }
    
    
    /**
     * LOG Moviebay
     */
    private function __logapi($servicename="",$userid="",$email="",$msg_request="",$msg_response="",$request_time="",$response_time="",$ip_address="",$mac_address=""){

               $this->_transaction = Yii::app()->db->beginTransaction();

               $this->_dblog = new LogMoviebay();
               
               try{
                       $this->_dblog->attributes  = array( "id"=> date("YmdHis").mt_rand(10,99), 
                                                           "servicename"=>($servicename !="")?$servicename:"",
                                                           "userid"=>$userid,
                                                           "email"=>$email,
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
