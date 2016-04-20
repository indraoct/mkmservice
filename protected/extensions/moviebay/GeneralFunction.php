<?php
/**
 * Class GeneralFunction
 * @author Indra Octama <indra.ctama@mncgroup.com>
 * @createddate 11 Desember 2015
 */
class GeneralFunction{

    /*
     * List Business
     */
    public static function listbusiness(){
        
        $list_business = array("1"=>"IA",
                               "2"=>"OTTPlaymedia",
                               "3"=>"Moviebay");
        
        return $list_business;
       
    }
    
    public static function listbusinessreverse(){
        
        $list_business = array("IA"=>"1",
                               "OTTPlaymedia"=>"2",
                               "Moviebay"=>"3");
        
        return $list_business;
       
    }
    
    /**
     * Convert Gender value
     * @param String $gen 
     * @param String $type
     */
    public static function convertGenderValue($gen,$type = "midleware"){
        /*
         * midleware
         */
        if($type == "midleware"){
            if($gen == "M" || $gen == "male"){
               $return = ($gen == "M")?"male":"male";
               return $return;
            }elseif($gen == "F" || $gen == "female"){
                $return = ($gen == "F")?"female":"female";
            }else{
                return "";
            }   
         /*
          * moviebay
          */   
        }elseif($type == "moviebay"){
            
            if($gen == "M" || $gen == "male"){
               $return = ($gen == "male")?"M":"M";
               return $return;
            }elseif($gen == "F" || $gen == "female"){
                $return = ($gen == "female")?"F":"F";
            }else{
                return "";
            }
            
        }else{
            return "";
        }
       
    }
    
    /**
     * 
     * @param Integer $id
     * @return String businness
     */
    public static function getBusiness($id){
       
        $listbusinness = GeneralFunction::listbusiness();
        return $listbusinness[$id];
    }
    
    /**
     * 
     * @param Integer $id
     * @return String businness
     */
    public static function getBusinessID($string){
       
        $listbusinness = GeneralFunction::listbusinessreverse();
        return $listbusinness[$string];
    }
    /**
     * isEmail
     */
    public static function isEmail($userid){
        
        //is email
        if (!filter_var($userid, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        } else {
            return false;
        }
        
    }
   
    /**
     * detect business
     * @param String $userid
     * @param String $password
     * 
     * @return String
     */
    public static function detectBusiness($userid,$password=""){
        
        Yii::import("application.models.Moviebay.UserMoviebay");
        Yii::import("application.extensions.moviebay.security");
        
        $security = new security();
        
        $model = new UserMoviebay();
        
        //check SLCS 

        $slcs = GeneralFunction::getUserProductCode($userid, $password);
        
         //TEST DEBUG TO FILE
                        $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                        $fp=@fopen($logFile,'a'); 
                        @flock($fp,LOCK_EX);
                        @fwrite($fp,  "check slcs :".CJSON::encode($slcs)."\n");
                        @flock($fp,LOCK_UN);
                        @fclose($fp);

        //TEST DEBUG TO FILE
        
        /*
         * Check User Is Exist in ZTE SLCS 
         */
        if($slcs["Result"] == "0"){
            
                        /*
                         * is Email  
                         */
                        if(GeneralFunction::isEmail($userid)){


                            $criteria = new CDbCriteria();
                            $criteria->addCondition("email = '".$userid."'");
                            $data = UserMoviebay::model()->findAll($criteria);

                            if($data != null){
                                foreach($data as $dt){
                                  $value[] = $dt->attributes;
                                }

                                return GeneralFunction::getBusiness($value[0]["usertype"]);
                            }else{
                                return false;
                            }

                        }else{

                            Yii::import("application.extensions.indovisionanywhere.IndovisionAnywhere");
                            Yii::import("application.extensions.ottplaymedia.OttPlaymedia");

                            $ia = new IndovisionAnywhere();
                            $ottplay = new OttPlaymedia();

                           
                            /*
                             * REMARK SEMENTARA
                             * Author      : Indra Octama
                             * Description : Akan di trace by account number
                             * Edit Date   : 6 January 2016  
                             */
                             //Check Indovision
//                            $dataia = CJSON::decode($ia->getUserData($userid,$password),true);
//                            
//
//                            if($dataia["error_code"] == "00"){
//
//                                //count data
//                                $countia = $model->count(array(
//                                        'condition'=>'userid=:userid',
//                                        'params'=>array(':userid'=>$userid),
//                                    ));
//
//                                //if data usermoviebay not exist && is email data email form API IA
//                                if($countia <= 0 && GeneralFunction::isEmail($dataia["data"]["email"])){
//
//                                      $data_arrayia = array("email"=>isset($dataia["data"]["email"])?$dataia["data"]["email"]:"",
//                                                            "name"=> isset($dataia["data"]["name"])?$dataia["data"]["name"]:"",
//                                                            "userid"=>isset($dataia["data"]["userid"])?$dataia["data"]["userid"]:"",
//                                                            "usertype"=>"1",
//                                                            "password"=>isset($dataia["data"]["password"])?$dataia["data"]["passowrd"]:"",
//                                                            "gender"=>isset($dataia["data"]["gender"])?  GeneralFunction::convertGenderValue($dataia["data"]["gender"],"midleware"):"",
//                                                            "userstatus"=>isset($dataia["data"]["userstatus"])?$dataia["data"]["userstatus"]:"",
//                                                            "dateofbirth"=>isset($dataia["data"]["dateofbirth"])?$dataia["data"]["dateofbirth"]:"",
//                                                            "phonenumber"=>isset($dataia["data"]["phonenumber"])?$dataia["data"]["phonenumber"]:"");
//
//                                      //insert data user moviebay
//                                      $response = GeneralFunction::insertUserMoviebay($data_arrayia);
//
//                                      if($response["error_code"] != "00"){
//                                          return false;
//                                      }
//
//                                }
//
//                                //1 = Indovision Anywhere
//                                return GeneralFunction::getBusiness("1");
//
//                            }
                            
                            /*
                             * SOLUSI SEMENTARA
                             * Author      : Indra Octama
                             * Description : Trace By Code
                             * Edit Date   : 6 January 2016  
                             */
                            
                            //Check Indovision Anywhere
                            $dataia = GeneralFunction::detectUserIndovisionAnywhere($userid,$password);
                            
                            if($dataia != false){
                              
                                //1 = Indovision Anywhere
                                return GeneralFunction::getBusiness("1");
                               
                            }
                            ////////////////END OF SOLUSI SEMENTARA//////////////////////////////////

                            //Check Ott Playmedia
                            $dataottplay = CJSON::decode($ottplay->getUserData($userid),true);
                            
                            //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  "checkottplaymedia : ".CJSON::encode($dataottplay)."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                            //TEST DEBUG TO FILE

                            if($dataottplay["error_code"] == "00"){

                                //count data
                                $countottplay = $model->count(array(
                                        'condition'=>'userid=:userid',
                                        'params'=>array(':userid'=>$userid),
                                    ));

                                //if data usermoviebay not exist && is email data email form API OTT Playmedia
                                if($countottplay <= 0 && GeneralFunction::isEmail($dataottplay["data"]["email"])){

                                    $data_arrayottplay = array("email"=>isset($dataottplay["data"]["email"])?$dataottplay["data"]["email"]:"",
                                                            "name"=> isset($dataottplay["data"]["name"])?$dataottplay["data"]["name"]:"",
                                                            "userid"=>isset($dataottplay["data"]["userid"])?$dataottplay["data"]["userid"]:"",
                                                            "usertype"=>"2",
                                                            "password"=>isset($dataottplay["data"]["password"])?$dataottplay["data"]["password"]:"",
                                                            "gender"=>isset($dataottplay["data"]["gender"])?$dataottplay["data"]["gender"]:"",
                                                            "userstatus"=>isset($dataottplay["data"]["userstatus"])?$dataottplay["data"]["userstatus"]:"",
                                                            "dateofbirth"=>isset($dataottplay["data"]["dateofbirth"])?$dataottplay["data"]["dateofbirth"]:"",
                                                            "phonenumber"=>isset($dataottplay["data"]["phonenumber"])?$dataottplay["data"]["phonenumber"]:"");

                                    //insert data user moviebay
                                      $response = GeneralFunction::insertUserMoviebay($data_arrayottplay);
                                      
                                      //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  CJSON::encode($response)."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                                     //TEST DEBUG TO FILE

                                      if($response["error_code"] != "00"){
                                          return false;
                                      }

                                }

                                //2 = Ott Playmedia
                                return GeneralFunction::getBusiness("2");
                            }
                          //IF IA,OTTPLAYMEDIA,MOVIEBAY NATIVE user not exist
                           return false; 

                        }
        }else{
            
            /*
            * is Email  
            */
           if(GeneralFunction::isEmail($userid)){
                $criteria = new CDbCriteria();
                $criteria->addCondition("email = '".$userid."'");
                $data = UserMoviebay::model()->findAll($criteria);
                
                 if($data != null){
                    foreach($data as $dt){
                      $value[] = $dt->attributes;
                    }
                    
                    //check SLCS 

                    $slcs = GeneralFunction::getUserProductCode($value[0]["userid"], $password);
                    
                    if($slcs["Result"] == "0"){
                       return GeneralFunction::getBusiness($value[0]["usertype"]);
                    }else{
                        return false;
                    } 
                }else{
                    return false;
                }
               
           }else{
                return false;   
           }
        }
        
        
    }
    
    /**
     * insertUserMoviebay
     * 
     */
    public static function insertUserMoviebay(array $data){
       
       Yii::import("application.models.Moviebay.UserMoviebay"); 
       Yii::import("application.extensions.moviebay.security");
       
       $transaction = Yii::app()->db->beginTransaction();
       $db = new UserMoviebay();
       $security = new security();
       try{ 
            $db->attributes = array("email"=>  strtolower($data["email"]),
                                "userid"=>$data["userid"],
                                "usertype"=>$data["usertype"],
                                "password"=>$security->wrap($data["password"]),
                                "name"=>$data["name"],
                                "dateofbirth"=>($data["dateofbirth"] != "")?$data["dateofbirth"]:"1970-10-10",
                                "gender"=>  GeneralFunction::convertGenderValue($data["gender"], "midleware"),
                                "userstatus"=>$data["userstatus"],
                                "phonenumber"=>$data["phonenumber"]
                         ); 

            $db->save();
            $transaction->commit();
            
            if(count($db->errors) > 0){
                return array("error_code"=>"98","error_msg"=>$db->errors);
            }else{
                return array("error_code"=>"00","error_msg"=>"Success");
            }
            
            
       }catch(Exception $e){
           
           $transaction->rollback();
           
           return array("error_code"=>"99","error_msg"=>$e->getMessage());
       }   
        
    }
    
    /**
     * Delete User Moviebay
     * 
     */
    public static function deleteUserMoviebay($userid,$email){
        
        Yii::import("application.models.Moviebay.UserMoviebay");
        
         /*
          * Jika Email
          */
         $model = UserMoviebay::model()->findByPk($email);
         $model->delete();
         
         /*
          * Jika User ID Delete By User ID
          */
         UserMoviebay::model()->delete("`userid` = :userid ", array(':userid' => $userid));
         
        
    } 
    
    public static function getUserMoviebay($userid){
        
        Yii::import("application.models.Moviebay.UserMoviebay");
        Yii::import("application.extensions.moviebay.security");
        
        $security = new security();
        
         $model = new UserMoviebay();
            
            if(GeneralFunction::isEmail($userid)){
                    $data = $model->find(array(
                                'condition'=>'email=:email',
                                'params'=>array(':email'=>$userid),
                            ));
            }else{
                
                    $data = $model->find(array(
                                'condition'=>'userid=:userid',
                                'params'=>array('userid'=>$userid),
                            ));
            }       
            
            if($data != null){
                
                $value = $data->attributes;
                
                $value["password"] = $security->baca($value["password"]);
                $value["gender"] = GeneralFunction::convertGenderValue($value["gender"], "moviebay");
                return $value;
            }else{
                return false;
            }
        
    }
    
    /**
     * Check User SLCS
     * check status
     * 
     */
    public function checkUserSlcs($UserID){
        
        Yii::import("application.extensions.iptv.action.*");
        Yii::import("application.models.Moviebay.UserMoviebay");
        Yii::import("application.extensions.moviebay.security");
      
        $model = new UserMoviebay();
        
        //jika inputan nya email|| usertype : 1,2,3
        if(GeneralFunction::isEmail($UserID)){
            
            $data = $model->find(array(
                                'condition'=>'email=:email',
                                'params'=>array(':email'=>$UserID),
                            ));
            
             
            
            //jika data tidak sama dengan null
            if($data != null){
                    //jika usertype nya moviebay ; 
                    if($data->usertype == "3"){
                        //class query user state
                        $quserstate = new QueryUserStateMain($UserID);
                        $responsequserstate = $quserstate->QueryUserStateAction();
                        
                        $response = CJSON::decode($responsequserstate,true);
                        
                        if($response["Result"] == "0"){
                            //edit user status
                            GeneralFunction::editUserMoviebay($data->email, $data->userid, (int)$response["Status"]);
                            
                            if($response["Status"] == "1"){                                
                                return $response;
                            }else{
                                return $response;
                            }
                            
                        }else{
                            return false;
                        }
                        
                     //jika usertype = 2 (ottplaymedia) ada treatment tersendiri
                    }elseif($data->usertype == "2"){
                        
                        //class query user state
                         $quserstate = new QueryUserStateMain($data->userid.Yii::app()->params["suffix_ottplaymedia"]); // suffix 04
                         $responsequserstate = $quserstate->QueryUserStateAction(); 
                         
                         $response = CJSON::decode($responsequserstate,true);
                        
                        if($response["Result"] == "0"){
                            //edit user status
                            GeneralFunction::editUserMoviebay($data->email, $data->userid,(int)$response["Status"]);
                            
                            if($response["Status"] == "1"){                                
                                return $response;
                            }else{
                                return $response;
                            }
                            
                        }else{
                            return false;
                        }
                        
                    }else{
                        //class query user state
                         $quserstate = new QueryUserStateMain($data->userid);
                         $responsequserstate = $quserstate->QueryUserStateAction(); 
                         
                         $response = CJSON::decode($responsequserstate,true);
                        
                        if($response["Result"] == "0"){
                            //edit user status
                            GeneralFunction::editUserMoviebay($data->email, $data->userid,(int)$response["Status"]);
                            
                            if($response["Status"] == "1"){                                
                                return $response;
                            }else{
                                return $response;
                            }
                            
                        }else{
                            return false;
                        }
                    }
            }else{
                return false;
            }
        //jika inputanya bukan email   (usertype = 1,2)
        }else{
            
            $data = $model->find(array(
                                'condition'=>'userid=:userid',
                                'params'=>array(':userid'=>$UserID),
                            ));
            
            
            if($data != null){
                
                //security
                $security = new security();
                
                //detect business
                $detectbusiness = GeneralFunction::detectBusiness($UserID,$security->baca($data->password));
                
                //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  "detectbusiness on checkslcs".CJSON::encode(GeneralFunction::getBusinessID($detectbusiness))."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                                     //TEST DEBUG TO FILE
                
                if($detectbusiness != false){
                
                    //jika ott playmedia , ada treatment tersendiri
                    if(GeneralFunction::getBusinessID($detectbusiness) == "2"){
                        
                                $quserstate = new QueryUserStateMain($UserID.Yii::app()->params["suffix_ottplaymedia"]); // suffix : 04
                                $responsequserstate = $quserstate->QueryUserStateAction();

                                $response = CJSON::decode($responsequserstate,true);
                                
                                //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  "response slcs on checkslcs".CJSON::encode($response)."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                                     //TEST DEBUG TO FILE

                                //response result 0
                                if($response["Result"] == "0"){
                                    //edit user status
                                    GeneralFunction::editUserMoviebay($data->email, $data->userid,(int)$response["Status"]);

                                    if($response["Status"] == "1"){                                
                                        return $response;
                                    }else{
                                        return $response;
                                    }

                                }else{
                                    return false;
                                }
                    //end of treatment ott playmedia            
                    }else{
                                $quserstate = new QueryUserStateMain($UserID);
                                $responsequserstate = $quserstate->QueryUserStateAction();

                                $response = CJSON::decode($responsequserstate,true);

                                //response result 0
                                if($response["Result"] == "0"){
                                    //edit user status
                                    GeneralFunction::editUserMoviebay($data->email, $data->userid,(int)$response["Status"]);

                                    if($response["Status"] == "1"){                                
                                        return $response;
                                    }else{
                                        return $response;
                                    }

                                }else{
                                    return false;
                                }
                        
                        
                        
                    }
                }else{
                    return false;
                }
                        
            }else{
                return false;
            }
        }
        
    }
    
    /**
     * getUserProductCode
     * @Description : method to get Prodcut code from userid
     */
    public static function getUserProductCode($userid,$password){
        
        Yii::import("application.extensions.iptv.action.*");
        
        /*
         * Ordinary
         */
        
        //class
        $QueryOrderProduct = new QueryOrderProductMain($userid, $password); 
       
        //action
        $responseJSON = $QueryOrderProduct->QueryOrderProductAction();
        
        //response       
        $response = CJSON::decode($responseJSON,true);
       
        /*
         * Ott Playmedia 
         */
        
        //class 
        $QueryOrderProductOttPlaymedia = new QueryOrderProductMain($userid.Yii::app()->params["suffix_ottplaymedia"],$password);        
        //action
        $responseJSONOttPlaymedia = $QueryOrderProductOttPlaymedia->QueryOrderProductAction();
        //response
        $responseOttPlaymedia = CJSON::decode($responseJSONOttPlaymedia,true);
       
        //IF 
        if($response["Result"] == "0"){
           //Ordinary
            return $response;
            
        }else{
            //OttPlaymedia
            return $responseOttPlaymedia;
        }
        
           
        
    }

    /**
     * register user slcs
     * @Description : method to insert data user to slcs
     */
    public static function registerUserSlcs($usertype,array $dataUser){
 
        
        Yii::import("application.extensions.iptv.action.*");
        Yii::import("application.extensions.ott.*");
        
        /**
         * class ott
         */
        $ott = new ClassOtt();
        
        
        
       
       
        //Moviebay Native, selain moviebay native maka sementara false 
        if($usertype == "3"){
            //create account
            $data = $ott->AccountCreation($dataUser["email"], Yii::app()->params["prefix_moviebay"].$dataUser["email"], $dataUser["password"], $dataUser["product_id"], "2");
            
            //TEST DEBUG TO FILE
                        $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_customer_midleware.txt";
                        $fp=@fopen($logFile,'a'); 
                        @flock($fp,LOCK_EX);
                        @fwrite($fp,  "request -> ".  CJSON::encode($dataUser)." || response -> ".CJSON::encode($data)."\n");
                        @flock($fp,LOCK_UN);
                        @fclose($fp);

            //TEST DEBUG TO FILE
            
            //change group for email
            $changegroup1 = new ChangeGroupMain($dataUser["email"], "1031");
            $data1 = CJSON::decode($changegroup1->ChangeGroupAction(),true);
            
            //change group for userid
            $changegroup2 = new ChangeGroupMain(Yii::app()->params["prefix_moviebay"].$dataUser["email"], "1031");
            $data2 = CJSON::decode($changegroup2->ChangeGroupAction(),true);
            
            //PROMO 
            $promo_product_id = GeneralFunction::checkPromoID($dataUser['promo_id']);
            
            if($promo_product_id != false){                
                 //subscribeproduct
                 $ott->SubscribeProduct($dataUser["email"], $promo_product_id);          
            }
            
            if(isset($data['Result'])){
                if($data['Result'] == "0" && $data1['Result'] == "0" && $data2['Result'] == "0"){
                    return true;
                }else{
                    //SLCS DELETE USER IF result NOT TRUE
                    $ott->AccountCancelation("MBAY".$dataUser["email"], $dataUser["email"]);
                    
                   /**
                    *  class Unsubscription
                    */
                    $unsubscription = new UnsubscriptionMain($dataUser["email"]);
                    $unsubscription->UnsubscriptionAction();
                   
                    return false;      
                }
            }else{
                return false;
            }
            
        }else{
            
            return false;
        }
       
    }
    
    /**
     * register exist user slcs
     * @Description : method to insert data user to slcs
     */
    public static function registerUserSlcsExist($usertype,array $dataUser){
 
        
        Yii::import("application.extensions.iptv.action.*");
        Yii::import("application.extensions.ott.*");
        
        /**
         * class ott
         */
        $ott = new ClassOtt();
              
        //Moviebay Native, selain moviebay native maka sementara false 
        if($usertype == "3"){
            
            $ExplodeEmail = explode("@", Yii::app()->params["prefix_moviebay"].$dataUser["email"]); 
            
            //create account
            $data = $ott->AccountCreation($dataUser["email"], $ExplodeEmail[0], $dataUser["password"], $dataUser["product_id"], "2");
            
            //TEST DEBUG TO FILE
                        $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_customer_midleware.txt";
                        $fp=@fopen($logFile,'a'); 
                        @flock($fp,LOCK_EX);
                        @fwrite($fp,  "request -> ".  CJSON::encode($dataUser)." || response -> ".CJSON::encode($data)."\n");
                        @flock($fp,LOCK_UN);
                        @fclose($fp);

            //TEST DEBUG TO FILE
            
            //change group for email
            $changegroup1 = new ChangeGroupMain($dataUser["email"], "1031");
            $data1 = CJSON::decode($changegroup1->ChangeGroupAction(),true);
            
            //change group for userid
            $changegroup2 = new ChangeGroupMain($ExplodeEmail[0], "1031");
            $data2 = CJSON::decode($changegroup2->ChangeGroupAction(),true);
            
            //PROMO 
            $promo_product_id = GeneralFunction::checkPromoID($dataUser['promo_id']);
            
            if($promo_product_id != false){                
                 //subscribeproduct
                 $ott->SubscribeProduct($dataUser["email"], $promo_product_id);          
            }
            
            if(isset($data['Result'])){
                if($data['Result'] == "0" && $data1['Result'] == "0" && $data2['Result'] == "0"){
                    return true;
                }else{
                    //SLCS DELETE USER IF result NOT TRUE
                    $ott->AccountCancelation($ExplodeEmail[0], $dataUser["email"]);
                    
                   /**
                    *  class Unsubscription
                    */
                    $unsubscription = new UnsubscriptionMain($dataUser["email"]);
                    $unsubscription->UnsubscriptionAction();
                   
                    return false;      
                }
            }else{
                return false;
            }
            
        }else{
            
            return false;
        }
       
    }
    
    /**
     * Add Product SLCS
     * 
     */
     public static function addProductSlcs($userid,$productcode = array()){
         
         Yii::import("application.extensions.ott.ClassOtt");
       
         
         if(count($productcode) > 0){
           
            $ott = new ClassOtt();
            
            if(count($productcode) > 1){

                    for($i =0;$i < count($productcode);$i++){
                      $ott->SubscribeProduct($userid, $productcode[$i]);
                    }

                    return true;
            }else{
                $ott->SubscribeProduct($userid, $productcode[0]);
                return true;
            }

         }else{
             return false;
         }
         
     }
     
     /**
      * Load Model UserMoviebay
      */
     public static function loadModelUserMoviebay($email) {
         
        Yii::import("application.models.Moviebay.UserMoviebay"); 
         
        $model = UserMoviebay::model()->findByPk($email);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    
    /**
     * 
     * @param String $email
     * @param String $userid
     * @param String $userstatus
     * @param String $newpassword
     * @param String $email
     *
     */
    public static function editUserMoviebay($email,$userid = 0,$userstatus = 1,$newpassword = "",$name = "",$gender = "",$dateofbirth = "",$phonenumber = ""){
        
        Yii::import("application.extensions.moviebay.security");
        Yii::import("application.extensions.ott.ClassOtt");
        Yii::import("application.models.Moviebay.UserMoviebay"); 
        
        $usermoviebay =  GeneralFunction::getUserMoviebay($email);
        $security = new security();
        
        if($usermoviebay != false){
                
            if($usermoviebay["usertype"] == "3" || $usermoviebay["usertype"] == "2" || $usermoviebay["usertype"] == "1"){
                
                
                $model = UserMoviebay::model()->findByPk($email);
                
                
                //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  "before db update userstatus:".CJSON::encode(array($email,$userid,$userstatus,$newpassword,$name,$gender,$dateofbirth,$phonenumber))."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                //TEST DEBUG TO FILE
                
                $transaction = Yii::app()->db->beginTransaction();
                try{
                        $model->attributes = array("email"=>$email,
                                                   "userstatus"=>(is_integer($userstatus))?$userstatus:$usermoviebay["userstatus"],
                                                   "password"=>($newpassword != "")?$security->wrap($newpassword):$security->wrap($usermoviebay["password"]),
                                                   "name"=>($name != "")?$name:$usermoviebay["name"],
                                                   "gender"=>($gender != "")?GeneralFunction::convertGenderValue($gender, "midleware"):GeneralFunction::convertGenderValue($usermoviebay["gender"],"midleware"),
                                                   "dateofbirth"=>($dateofbirth != "")?$dateofbirth:$usermoviebay["dateofbirth"],
                                                   "phonenumber"=>($phonenumber != "")?$phonenumber:$usermoviebay["phonenumber"]
                                                   );

                        $response_db = $model->save();
                        $transaction->commit();    
                        
                }catch(Exception $e){
                    
                    $response_db = $e->getMessage();
                    $transaction->rollback();
                }

                 //TEST DEBUG TO FILE
                                            $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_ottplaymedia.txt";
                                            $fp=@fopen($logFile,'a'); 
                                            @flock($fp,LOCK_EX);
                                            @fwrite($fp,  "response db update userstaus:".CJSON::encode(array("response save()"=>$response_db,"Error"=>$model->getErrors(),"MODELS"=>$model->attributes))."\n");
                                            @flock($fp,LOCK_UN);
                                            @fclose($fp);

                                     //TEST DEBUG TO FILE
                
                //class ott
                $ott = new ClassOtt();
                    
                //check SLCS password
                if($newpassword != ""){
                    $slcs_password = $ott->Resetpassword($email,$newpassword);
                    $response_slcs_password = ($slcs_password["Result"] == "0")?true:false;
                }else{
                    $response_slcs_password = true;
                }
                                
                //check SLCS userstatus
                if($userstatus != 1){
                    $slcs_status = $ott->Suspension($email);
                    $response_slcs_status = $response_slcs_status = ($slcs_status["Result"] == "0")?true:false;
                }else{ 
                    $response_slcs_status = true;
                }
                
                if($response_db == true && $response_slcs_password == true && $response_slcs_status == true){
                
                    return true;
                    
                }else{
                    
                    return false;
                }
                
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
    
    /**
     * resetPassword
     * 
     * @param String $userid Userid
     * @param String $password Password
     * 
     * 
     */
    public static function resetPassword($userid,$newpassword){
        
        Yii::import("application.models.Moviebay.UserMoviebay");
        
        //get user moviebay
        $dataUser = GeneralFunction::getUserMoviebay($userid);
        
        if($dataUser != false){
        
          $response = GeneralFunction::editUserMoviebay($dataUser["email"],$dataUser["userid"], "1", $newpassword);
           
          return $response;
            
        }else{
            return false;
        }
        
    }
    
    /**
     * Detect User Indovision
     * SOLUSI SEMENTARA
     */
    public function detectUserIndovisionAnywhere ($userid,$password){
        
        //panjang
        $length = strlen($userid);
        
        /**
         * Perubahan length 12 ATAU 9 (VARCHAR) untuk userid khusus INDOVISION ANYWHERE
         * Date : 11 Maret 2016
         */
        if($length == 12 || $length == 9){
            
            Yii::import("application.extensions.iptv.action.QueryOrderProductMain");
            
            /*
             * query orderproduct
             */
            $queryproduct = new QueryOrderProductMain($userid, $password); 
            $returnqueryproductJSON =  $queryproduct->QueryOrderProductAction();
            $returnqueryproduct = CJSON::decode($returnqueryproductJSON,true);
            
            if(isset($returnqueryproduct["Productlist"])){
                $product = $returnqueryproduct["Productlist"]; 
            }else{
                $product = "";
            }
           
            $data_arrayia = array("email"=>"",
                            "name"=> "",
                            "userid"=>$userid,
                            "usertype"=>"1",
                            "password"=>$password,
                            "gender"=>"",
                            "userstatus"=>"1",
                            "dateofbirth"=>"",
                            "phonenumber"=>"",
                            "product"=>$product);

            return $data_arrayia;
            
           
        }else{
            return false;
        }
        

               
        
    }
    
    /**
     * Check Promo ID
     * @param type $promo_id
     * 
     */
    public static function checkPromoID($promo_id){
        
        Yii::import("application.models.Promo.MPromo");
        
        $data = MPromo::model()->findByPk($promo_id);
        
        if($data != null){
           
            return $data->promo_product_id;
            
        }else{
            return false;
        }
        
    }
    
    /**
     * Add product Promo On ACtion Login 
     */
   
    public function addPromo($userid,$promo_product_id){
        
        Yii::import("application.extensions.ott.*");
        $ott = new ClassOtt();    
        $ott->SubscribeProduct($userid, $promo_product_id);
        
        
    }
    
}
