<?php

class EmptyuserController extends Controller{
 
    private $_key;
    private $_pass;
    private $_http;
    private $_security;
    
    public function init(){
        
        Yii::import("application.models.Moviebay.UserMoviebay");
        Yii::import("application.extensions.ott.ClassOtt");
        Yii::import("application.extensions.moviebay.security");
        Yii::import("application.models.Moviebay.TempUserMoviebayExist");
        
        $this->_key = "WnlaOGRSa2dGOFFqSGF1SUtvYmNMUT09";
        $this->_pass = "REEzb0I3K0YxdFBTelNIcllueTNzdz09";
        $this->_http = new CHttpRequest();
        $this->_security = new security();
    }
    
    public function actionDeleteUser(){
        
         if($this->_http->isPostRequest == true){
             
            
            $key = isset($_POST["key"])?$_POST["key"]:""; 
            $pass = isset($_POST["pass"])?$_POST["pass"]:""; 
            
            if($this->_security->wrap($key) == $this->_key && $this->_security->wrap($pass) == $this->_pass ){ 
                $user =  UserMoviebay::model()->findAll();
                $ott = new ClassOtt();

                if($user != null){
                        $j = 0;
                        foreach($user as $u){

                           $data_slcs[] = array("slcs_rec_".$j => $ott->AccountCancelation("MBAY".$u->email, $u->email));

                           $data_email[] = array("email_rec_".$j => UserMoviebay::model()->deleteAll("`email` = :email ", array(':email' => $u->email))); 

                           $j++;
                        }

                        echo CJSON::encode($data_slcs);
                        
                        echo "<br/><br/>";

                        echo CJSON::encode($data_email);
                        
                }else{
                    throw new CHttpException(404,'Empty Record.');
                }  
                
            }else{
                
                throw new CHttpException(403,'Wrong Access.');
            }
                

         }else{
             throw new CHttpException(403,'Forbidden.');
         }
       
        
        
    }
    
    /**
     * Script For Delete User SLCS from temp
     * @throws CHttpException
     */
    public function actionDeleteuserslcs(){
        
         if($this->_http->isPostRequest == true){
            $temp_moviebay = new TempUserMoviebayExist();
            $ott = new ClassOtt();

            $data =   $temp_moviebay->findAll();
            $data_mov =  array();
            foreach ($data as $dt){
                $data_mov[] = $dt->attributes;

            }

            foreach ($data_mov as $dv){
                $dataOtt =  $ott->AccountCancelation("MBAY".$dv["email"], $dv["email"]);
                
                 //TEST DEBUG TO FILE
                        $logFile = "/usr/share/nginx/mkmservice/protected/runtime/log_customer_exist_midleware.txt";
                        $fp=@fopen($logFile,'a'); 
                        @flock($fp,LOCK_EX);
                        @fwrite($fp,  "data slcs -> ".  CJSON::encode($dv)." || response -> ".CJSON::encode($dataOtt)."\n");
                        @flock($fp,LOCK_UN);
                        @fclose($fp);

                 //TEST DEBUG TO FILE
                
            }
             
             
         }else{
            throw new CHttpException(403,'Forbidden.');
         }
       
    }
    
    /**
     * test temp
     */
    public function actionTesttemp(){
        
       if($this->_http->isPostRequest == true){ 
            $temp_moviebay = new TempUserMoviebayExist();

            $data =   $temp_moviebay->findAll();
            $data_mov =  array();
            foreach ($data as $dt){
                $data_mov[] = $dt->attributes;

            }

            echo "<pre>";

            var_dump($data_mov);

            echo "</pre>";
       }else{
           throw new CHttpException(403,'Forbidden.');
       }
    }
    
    
    
    
}
