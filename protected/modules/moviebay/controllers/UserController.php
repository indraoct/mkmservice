<?php
/**
 * Class UserController
 * 
 * @author Indra Octama <indra.octama@mncgroup.com>
 * @createddate 11 Desember 2015
 */
class UserController extends Controller
{
    private $_http;
    private $_transaction;
    private $_dblog;
    private $_token;
    private $_checkuser;
    
    
    public function init(){
        
        Yii::import("application.extensions.moviebay.GeneralFunction");
        Yii::import("application.extensions.indovisionanywhere.IndovisionAnywhere");
        Yii::import("application.extensions.ottplaymedia.OttPlaymedia");
        Yii::import("application.models.Moviebay.LogMoviebay");
        Yii::import("application.extensions.iptv.action.*");
       
        $this->_http = new CHttpRequest();
        $this->_token = Yii::app()->params["token_moviebay_service"];

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
            $promo_id = isset($_REQUEST["promo_id"])?$_REQUEST["promo_id"]:"";
                    
            //log moviebay
            $this->__logapi("login_req",$userid,"", http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
                 
                if($this->_token == $token){
                        if(GeneralFunction::detectBusiness($userid,$password) != false){

                                //data user moviebay
                                 $datausermoviebay = GeneralFunction::getUserMoviebay($userid);

                                    if( $datausermoviebay != false){
                                        //check user slcs
                                        $checkuserslcs = GeneralFunction::checkUserSlcs($userid);
                                        
                                        if($checkuserslcs != false){

                                            $userstatusslcs = $checkuserslcs["Status"];
                                            
                                            if($userstatusslcs == "1"){
                                                    //get product code

                                                    if($datausermoviebay["usertype"] == "3"){
                                                        $useridpd = $datausermoviebay["email"];
                                                    }elseif($datausermoviebay["usertype"] == "2"){
                                                        $useridpd = $datausermoviebay["userid"].Yii::app()->params["suffix_ottplaymedia"];
                                                    }elseif($datausermoviebay["usertype"] == "1"){
                                                        $useridpd = $datausermoviebay["userid"];
                                                    }

                                                    $get_product_code = GeneralFunction::getUserProductCode($useridpd, $password);

                                                    //PROMO LOGIC-----------------------------------------------------------------
                                                    $promo_product_id = GeneralFunction::checkPromoID($promo_id);
                                                    //if ada promo_id maka cek apakah product_id sudah ada atau belum 
                                                    if( $promo_product_id != false){
                                                        $data_array = $get_product_code["Productlist"];
                                                            $arr = $data_array["ProductListItem"];
                                                            $productadd = false;
                                                            if(count($arr) > 1){                                             
                                                                foreach($arr as $a){
                                                                    if($a["ProductID"] == $promo_product_id){
                                                                        $productadd = true; // product promo sudah pernah ditambahkan
                                                                        break;
                                                                    }
                                                                }
                                                            }else{                                                    
                                                                if($arr["ProductID"] == $promo_product_id){
                                                                    $productadd = true; // product promo sudah pernah ditambahkan
                                                                }
                                                            }
                                                            //if tidak pernah ditambahkan product promo maka tambahkan product promo
                                                            if($productadd == false){
                                                                GeneralFunction::addpromo($userid,$promo_product_id);
                                                            }

                                                    }
                                                    //PROMO LOGIC-----------------------------------------------------------------

                                                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success","data"=>array_merge($datausermoviebay,array("product"=>isset($get_product_code["Result"])?(($get_product_code["Result"]=="0")?$get_product_code["Productlist"]:""):""))));
                                                    header('Content-Type: application/json');
                                                    echo $response;

                                                    $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                                            }else{
                                                
                                                    $response = CJSON::encode(array("error_code"=>"9997","error_msg"=>"User SLCS status Not Active . Message = ".CJSON::encode($checkuserslcs)));
                                                    header('Content-Type: application/json');
                                                    echo $response;

                                                    $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                                                
                                                
                                            }

                                        }else{


                                                $response = CJSON::encode(array("error_code"=>"9998","error_msg"=>"Error Communication To SLCS"));
                                                header('Content-Type: application/json');
                                                echo $response;

                                                $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                                            
                                        }
                                        
                                    }else{

                                        $response = CJSON::encode(array("error_code"=>"12345","error_msg"=>"User Is Exist But Email is empty","usertype"=>GeneralFunction::getBusinessID(GeneralFunction::detectBusiness($userid,$password)),"password"=>$password));

                                        header('Content-Type: application/json');
                                        echo $response;

                                        $this->__logapi("login_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));

                                    }

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
     * @param String $email
     * @param String $gender 
     * @param String $phonenumber
     * @param String $password
     * @param Date  $dateofbirth 
     * @param Integer $usertype
     * 
     * @return JSON
     */
    public function actionRegister()
    {
      if($this->_http->isPostRequest == true){
          
          $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
          $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:0;
          $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
          $name  = isset($_REQUEST["name"])?$_REQUEST["name"]:"";
          $gender = isset($_REQUEST["gender"])?$_REQUEST["gender"]:"";
          $phonenumber = isset($_REQUEST["phonenumber"])?$_REQUEST["phonenumber"]:"";
          $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
          $dateofbirth = isset($_REQUEST["dateofbirth"])?$_REQUEST["dateofbirth"]:"";
          $usertype = isset($_REQUEST["usertype"])?$_REQUEST["usertype"]:"";
          $promo_id = isset($_REQUEST["promo_id"])?$_REQUEST["promo_id"]:"";
          
          //log moviebay
            $this->__logapi("register_req",$userid,$email, http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
             
          
          if($token == $this->_token){
              
              if($usertype != ""){
                  
                    if($usertype == "1"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register Indovision Anywhere Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));


                    }elseif($usertype == "2"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register OTT Playmedia Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                    }elseif($usertype == "3"){
                       //FREE
                         $data_array = array("email"=>$email,
                                            "name"=>$name,
                                            "userid"=>$userid,
                                            "usertype"=>$usertype,
                                            "password"=>$password,
                                            "gender"=>$gender,
                                            "userstatus"=>"1",
                                            "dateofbirth"=>$dateofbirth,
                                            "phonenumber"=>$phonenumber);

                        //insert data user moviebay
                        $insertdatauser = GeneralFunction::insertUserMoviebay($data_array);

                        
                            if($insertdatauser["error_code"] == "00"){
                                
                                $dataUser = array("email" => $data_array["email"],
                                                  "password" => $data_array["password"],
                                                  "product_id" => Yii::app()->params["product_code_moviebayfree"],
                                                  "promo_id" => $promo_id
                                                   );
                               //register to SLCS 
                               $registerslcs =   GeneralFunction::registerUserSlcs($usertype,$dataUser);
                               
                               if($registerslcs == true){
                                   
                                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"success","data"=>$data_array));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                   
                               }else{
                                    /*
                                     * Delete User Jika
                                     */
                                    GeneralFunction::deleteUserMoviebay($userid,$email);
                                    
                                    if($registerslcs == true){
                                        /*
                                         * Jika sukses daftar di slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"5555","error_msg"=>"Email is Exist in SLCS system, try to register another email"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }else{
                                        /*
                                         * Jika tidak sukses daftar slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"6666","error_msg"=>"Failed To Register in SLCS"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }
                               }
                                

                            }else{
                                    //error from model
                                
                                    $response = CJSON::encode(array("error_code"=>"7878","error_msg"=>$insertdatauser["error_msg"]));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                            }

                    }else{
                            $response = CJSON::encode(array("error_code"=>"8787","error_msg"=>"Wrong Usertype"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                    }
                  
              }else{
                
                    $response = CJSON::encode(array("error_code"=>"8989","error_msg"=>"Parameter usertype not exist"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
              }
             
          }else{
              
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                header('Content-Type: application/json');
                echo $response;
                $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
          }
         
      }else{
          throw new CHttpException(403,'Forbidden.');
      }
        
    }
    
    
        /**
     * action Register
     * @param String $token
     * @param String $userid 
     * @param String $email
     * @param String $gender 
     * @param String $phonenumber
     * @param String $password
     * @param Date  $dateofbirth 
     * @param Integer $usertype
     * 
     * @return JSON
     */
    public function actionRegisterexist()
    {
      if($this->_http->isPostRequest == true){
          
          $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
          $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:0;
          $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
          $name  = isset($_REQUEST["name"])?$_REQUEST["name"]:"";
          $gender = isset($_REQUEST["gender"])?$_REQUEST["gender"]:"";
          $phonenumber = isset($_REQUEST["phonenumber"])?$_REQUEST["phonenumber"]:"";
          $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
          $dateofbirth = isset($_REQUEST["dateofbirth"])?$_REQUEST["dateofbirth"]:"";
          $usertype = isset($_REQUEST["usertype"])?$_REQUEST["usertype"]:"";
          $promo_id = isset($_REQUEST["promo_id"])?$_REQUEST["promo_id"]:"";
          
          //log moviebay
            $this->__logapi("register_req",$userid,$email, http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
             
          
          if($token == $this->_token){
              
              if($usertype != ""){
                  
                    if($usertype == "1"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register Indovision Anywhere Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));


                    }elseif($usertype == "2"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register OTT Playmedia Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                    }elseif($usertype == "3"){
                       //FREE
                         $data_array = array("email"=>$email,
                                            "name"=>$name,
                                            "userid"=>$userid,
                                            "usertype"=>$usertype,
                                            "password"=>$password,
                                            "gender"=>$gender,
                                            "userstatus"=>"1",
                                            "dateofbirth"=>$dateofbirth,
                                            "phonenumber"=>$phonenumber);

                        //insert data user moviebay
                        $insertdatauser = GeneralFunction::insertUserMoviebay($data_array);

                        
                            if($insertdatauser["error_code"] == "00"){
                                
                                $dataUser = array("email" => $data_array["email"],
                                                  "password" => $data_array["password"],
                                                  "product_id" => Yii::app()->params["product_code_moviebayfree"],
                                                  "promo_id" => $promo_id
                                                   );
                               //register to SLCS 
                               $registerslcs =   GeneralFunction::registerUserSlcsExist($usertype,$dataUser);
                               
                               if($registerslcs == true){
                                   
                                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"success","data"=>$data_array));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                   
                               }else{
                                    /*
                                     * Delete User Jika
                                     */
                                    GeneralFunction::deleteUserMoviebay($userid,$email);
                                    
                                    if($registerslcs == true){
                                        /*
                                         * Jika sukses daftar di slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"5555","error_msg"=>"Email is Exist in SLCS system, try to register another email"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }else{
                                        /*
                                         * Jika tidak sukses daftar slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"6666","error_msg"=>"Failed To Register in SLCS"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }
                               }
                                

                            }else{
                                    //error from model
                                
                                    $response = CJSON::encode(array("error_code"=>"7878","error_msg"=>$insertdatauser["error_msg"]));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                            }

                    }else{
                            $response = CJSON::encode(array("error_code"=>"8787","error_msg"=>"Wrong Usertype"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                    }
                  
              }else{
                
                    $response = CJSON::encode(array("error_code"=>"8989","error_msg"=>"Parameter usertype not exist"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
              }
             
          }else{
              
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                header('Content-Type: application/json');
                echo $response;
                $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
          }
         
      }else{
          throw new CHttpException(403,'Forbidden.');
      }
        
    }
    
    
       /**
     * action Register VIP
     * @param String $token
     * @param String $userid 
     * @param String $email
     * @param String $gender 
     * @param String $phonenumber
     * @param String $password
     * @param Date  $dateofbirth 
     * @param Integer $usertype
     * 
     * @return JSON
     */
    public function actionRegistervip()
    {
      if($this->_http->isPostRequest == true){
          
          $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
          $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:0;
          $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
          $name  = isset($_REQUEST["name"])?$_REQUEST["name"]:"";
          $gender = isset($_REQUEST["gender"])?$_REQUEST["gender"]:"";
          $phonenumber = isset($_REQUEST["phonenumber"])?$_REQUEST["phonenumber"]:"";
          $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
          $dateofbirth = isset($_REQUEST["dateofbirth"])?$_REQUEST["dateofbirth"]:"";
          $usertype = isset($_REQUEST["usertype"])?$_REQUEST["usertype"]:"";
          $promo_id = isset($_REQUEST["promo_id"])?$_REQUEST["promo_id"]:"";
          
          //log moviebay
            $this->__logapi("register_req",$userid,$email, http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
             
          
          if($token == $this->_token){
              
              if($usertype != ""){
                  
                    if($usertype == "1"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register Indovision Anywhere Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));


                    }elseif($usertype == "2"){
                        
                            $response = CJSON::encode(array("error_code"=>"1212","error_msg"=>"System Can Not Register OTT Playmedia Yet"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                    }elseif($usertype == "3"){
                       //FREE
                         $data_array = array("email"=>$email,
                                            "name"=>$name,
                                            "userid"=>$userid,
                                            "usertype"=>$usertype,
                                            "password"=>$password,
                                            "gender"=>$gender,
                                            "userstatus"=>"1",
                                            "dateofbirth"=>$dateofbirth,
                                            "phonenumber"=>$phonenumber);

                        //insert data user moviebay
                        $insertdatauser = GeneralFunction::insertUserMoviebay($data_array);

                        
                            if($insertdatauser["error_code"] == "00"){
                                
                                $dataUser = array("email" => $data_array["email"],
                                                  "password" => $data_array["password"],
                                                  "product_id" => "OTTSuperGalaxy",
                                                  "promo_id" => $promo_id
                                                   );
                               //register to SLCS 
                               $registerslcs =   GeneralFunction::registerUserSlcsExist($usertype,$dataUser);
                               
                               if($registerslcs == true){
                                   
                                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"success","data"=>$data_array));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                   
                               }else{
                                    /*
                                     * Delete User Jika
                                     */
                                    GeneralFunction::deleteUserMoviebay($userid,$email);
                                    
                                    if($registerslcs == true){
                                        /*
                                         * Jika sukses daftar di slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"5555","error_msg"=>"Email is Exist in SLCS system, try to register another email"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }else{
                                        /*
                                         * Jika tidak sukses daftar slcs
                                         */
                                        $response = CJSON::encode(array("error_code"=>"6666","error_msg"=>"Failed To Register in SLCS"));
                                        header('Content-Type: application/json');
                                        echo $response;
                                        $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                        
                                    }
                               }
                                

                            }else{
                                    //error from model
                                
                                    $response = CJSON::encode(array("error_code"=>"7878","error_msg"=>$insertdatauser["error_msg"]));
                                    header('Content-Type: application/json');
                                    echo $response;
                                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                            }

                    }else{
                            $response = CJSON::encode(array("error_code"=>"8787","error_msg"=>"Wrong Usertype"));
                            header('Content-Type: application/json');
                            echo $response;
                            $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                    }
                  
              }else{
                
                    $response = CJSON::encode(array("error_code"=>"8989","error_msg"=>"Parameter usertype not exist"));
                    header('Content-Type: application/json');
                    echo $response;
                    $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
              }
             
          }else{
              
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                header('Content-Type: application/json');
                echo $response;
                $this->__logapi("register_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
          }
         
      }else{
          throw new CHttpException(403,'Forbidden.');
      }
        
    }
    
    /**
     * Action Edit User
     * 
     * @param String $token 
     * @param String $userid 
     * @param String $userstatus 
     * @param String $newpassword 
     * @param String $email
     * 
     * @return JSON 
     */
    public function actionEdit()
    {
        if($this->_http->isPostRequest == true){
            
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"0";
            $userstatus = isset($_REQUEST["userstatus"])?$_REQUEST["userstatus"]:1;
            $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
            $newpassword = isset($_REQUEST["newpassword"])?$_REQUEST["newpassword"]:"";
            $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
            $name = isset($_REQUEST["name"])?$_REQUEST["name"]:"";
            $gender = isset($_REQUEST["gender"])?$_REQUEST["gender"]:"";  
            $dateofbirth = isset($_REQUEST["dateofbirth"])?$_REQUEST["dateofbirth"]:"";
            $phonenumber = isset($_REQUEST["phonumber"])?$_REQUEST["phonenumber"]:"";
            
             //log moviebay
            $this->__logapi("edituser_req",$userid,$email, http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
            
            if($token == $this->_token){
                
                $this->_checkuser = new QueryOrderProductMain(($userid != "0")?$userid:$email, $password);
                $chekuser = CJSON::decode($this->_checkuser->QueryOrderProductAction(),true);
                
                if($chekuser["Result"] == "0"){
                        $response = GeneralFunction::editUserMoviebay($email,$userid,$userstatus,$newpassword,$name,$gender,$dateofbirth,$phonenumber);

                        if($response == true){

                                $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"success"));
                                header('Content-Type: application/json');
                                echo $response;
                                $this->__logapi("edituser_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                        }else{

                                $response = CJSON::encode(array("error_code"=>"9993","error_msg"=>"Failed To Update User"));
                                header('Content-Type: application/json');
                                echo $response;
                                $this->__logapi("edituser_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                        }
                }else{
                    
                    $response = CJSON::encode(array("error_code"=>"9999","error_msg"=>"Invalid userid or password"));

                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("edituser_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                }
                
            }else{
                
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                header('Content-Type: application/json');
                echo $response;
                $this->__logapi("edituser_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
            }
            
            
        }else{
            throw new CHttpException(403,'Forbidden.');
        }
    }
    
    /**
     * Action Update Email
     * 
     * @param String $token
     * @param String $userid
     * @param String $usertype
     * @param String $password 
     * @param String $email
     * 
     * @return JSON
     */
    public function actionUpdateEmail(){
        
         if($this->_http->isPostRequest == true){
             
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"";
            $usertype = isset($_REQUEST["usertype"])?$_REQUEST["usertype"]:"";
            $password = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
            $email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
            
            //log moviebay
            $this->__logapi("updateemail_req",$userid,$email, http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
            
            
            if($this->_token == $token){
                    if(GeneralFunction::isEmail($email) == true){

                                $ia = new IndovisionAnywhere();
                                $ottplay = new OttPlaymedia();


                                //Indovision Anywhere
                                if($usertype == "1"){

                                            $data_arrayia = array("email"=>$email,
                                                                       "userid"=>$userid,
                                                                       "name"=>"",
                                                                       "usertype"=>"1",
                                                                       "password"=>$password,
                                                                       "gender"=>"",
                                                                       "userstatus"=>"",
                                                                       "dateofbirth"=>"",
                                                                       "phonenumber"=>"");

                                               //insert data user moviebay
                                               $insertdatamoviebay = GeneralFunction::insertUserMoviebay($data_arrayia);
                                               
                                               if($insertdatamoviebay["error_code"] == "00"){
                                                        //get product code
                                                         $get_product_code = GeneralFunction::getUserProductCode($userid,$password);
  
                                                 
                                                            $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success","data"=>array_merge($data_arrayia,array("product"=>isset($get_product_code["Result"])?(($get_product_code["Result"]=="00")?$get_product_code["Productlist"]:""):""  ))));     
                                                            header('Content-Type: application/json');
                                                            echo $response;
                                                            $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                               }else{
                                                            $response = CJSON::encode($insertdatamoviebay);
                                                            header('Content-Type: application/json');
                                                            echo $response;
                                                            $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                                                
                                               }



                                //OTT Playmedia    
                                }elseif($usertype == "2"){

                                    $dataottplay = CJSON::decode($ottplay->getUserData($userid),true);

                                    if($dataottplay["error_code"] == "00"){
                                       
                                            $data_arrayottplay = array("email"=>$email,
                                                                        "userid"=>isset($dataottplay["data"]["userid"])?$dataottplay["data"]["userid"]:"",
                                                                        "name"=>isset($dataottplay["data"]["name"])?$dataottplay["data"]["name"]:"",
                                                                        "usertype"=>"2",
                                                                        "password"=>$password,
                                                                        "gender"=>isset($dataottplay["data"]["gender"])?$dataottplay["data"]["gender"]:"",
                                                                        "userstatus"=>isset($dataottplay["data"]["userstatus"])?$dataottplay["data"]["userstatus"]:"",
                                                                        "dateofbirth"=>isset($dataottplay["data"]["dateofbirth"])?$dataottplay["data"]["dateofbirth"]:"",
                                                                        "phonenumber"=>isset($dataottplay["data"]["phonenumber"])?$dataia["data"]["phonenumber"]:""
                                                );

                                                //insert data user moviebay
                                                  GeneralFunction::insertUserMoviebay($data_arrayottplay);
                                                  
                                                  //get product code
                                                  $get_product_code = GeneralFunction::getUserProductCode(isset($dataottplay["data"]["userid"])?($dataottplay["data"]["userid"]):"",$password);

                                             $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success","data"=>array_merge($data_arrayottplay,array("product"=>isset($get_product_code["Result"])?(($get_product_code["Result"]=="00")?$get_product_code["Productlist"]:""):""  ))));     
                                             header('Content-Type: application/json');
                                             echo $response;
                                             $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));
                                    }else{


                                            $response = CJSON::encode(array("error_code"=>"9997","error_msg"=>"User OTTPlaymedia Not Exist"));

                                            header('Content-Type: application/json');
                                            echo $response;

                                            $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                    }


                                }else{

                                        $response = CJSON::encode(array("error_code"=>"9998","error_msg"=>"Wrong User Type"));

                                        header('Content-Type: application/json');
                                        echo $response;

                                        $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                                }
                    }else{

                        $response = CJSON::encode(array("error_code"=>"9996","error_msg"=>"Wrong Format Email"));
                        header('Content-Type: application/json');
                        echo $response;
                        $this->__logapi("updateemail_res",$userid,$email, "", $response, "", date("Y-m-d H:i:s"));

                    }
                    
            }else{
                
                $response = CJSON::encode(array("error_code"=>"1111","error_msg"=>"Wrong Token"));
                header('Content-Type: application/json');
                echo $response;
                $this->__logapi("updateemail_res",$userid,$email,"", $response, "", date("Y-m-d H:i:s"));
                
            }
             
         }else{
             throw new CHttpException(403,'Forbidden.');
         }
        
        
    }
    
    /**
     * Unbind
     * 
     * @param String $token token
     * @param String $userid userid
     * 
     * @return JSON response sucess / failed
     */
    public function actionUnbind(){
        
        if($this->_http->isPostRequest == true){
        
                $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
                $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"";

                //log moviebay
                $this->__logapi("unbinding_req",$userid,"", http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
            
                //get user moviebay
                $dataUser = GeneralFunction::getUserMoviebay($userid);
                  
                
            if($this->_token == $token){
                
               
                
                //if indovisian anywhere && ott playmedia 
                if($dataUser["usertype"] == 1 || $dataUser["usertype"] == 2 ){
                    $unbind = new UnbindMain($dataUser["userid"]);
                    $dataJSON = $unbind->UnbindAction();
                }else{
                    $unbind = new UnbindMain($userid);
                    $dataJSON = $unbind->UnbindAction();
                }
                
                $data = CJSON::decode($dataJSON,true);
                
                if($data["Result"] == "0"){
                    
                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));
                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("unbinding_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                    
                }else{
                    
                    $response = CJSON::encode(array("error_code"=>"99","error_msg"=>$data["Errordesc"]));
                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("unbinding_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                    
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }    
                
        }else{
            throw new CHttpException(403,'Forbidden.');
        }

    }
    
    /**
     * Forgot Password
     * 
     * @param String $token token
     * @param String $userid userid
     * @param String $newpassword newpassword
     * 
     * @return JSON response sucess / failed
     */
    
    public function actionForgotpassword(){
        
        if($this->_http->isPostRequest == true){
            
            $token = isset($_REQUEST["token"])?$_REQUEST["token"]:"";
            $userid = isset($_REQUEST["userid"])?$_REQUEST["userid"]:"";
            $newpassword = isset($_REQUEST["newpassword"])?$_REQUEST["newpassword"]:"";

                //log moviebay
                $this->__logapi("forgotpassword_req",$userid,"", http_build_query($_REQUEST), "", date("Y-m-d H:i:s"));
            
            //check if token is true 
            if($this->_token == $token){
              
                //action resetpassword
                $response = GeneralFunction::resetPassword($userid, $newpassword);
                
                if($response == true){
                    
                    $response = CJSON::encode(array("error_code"=>"00","error_msg"=>"Success"));
                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("forgotpassword_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                    
                    
                }else{
                    
                    $response = CJSON::encode(array("error_code"=>"99","error_msg"=>"Failed to reset password"));
                    header('Content-Type: application/json');
                    echo $response;

                    $this->__logapi("forgotpassword_res",$userid,"", "", $response, "", date("Y-m-d H:i:s"));
                    
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
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
