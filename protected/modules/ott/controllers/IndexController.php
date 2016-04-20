<?php
/**
 * API OTT 2.0
 * @Author : Indra Octama
 * @CreatedDate : 23 November 2015
 * @Purposed : Rebuilt API OTT
 */
class IndexController extends Controller
{
    
        private $_http;
        private $_access_code;
        private $_ottxml;
    
        /**
         * init function
         */
        public function init(){
            
            Yii::import('application.extensions.ott.ClassOtt');
            
            $this->_http = new CHttpRequest();
            $this->_access_code = Yii::app()->params["access_ott"];
            $this->_ottxml = new ClassOtt();
        }
        
        /**
         * Just For Routing To Forbidden Access
         * 
         */
	public function actionIndex()
	{
		throw new CHttpException(403,'Forbidden.');
	}
        
        /**
         * Create Account OTT
         * @param string $Username Username
         * @param string $EmailAddress Email Address
         * @param string $ProductID ProductID (definition in SLCS Application)
         * @param string $type (as usual put 2 to this parameter)
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionAccountCreation(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $EmailAddress  = isset($_REQUEST["EmailAddress"])?$_REQUEST["EmailAddress"]:"";
                $Password      = isset($_REQUEST["Password"])?$_REQUEST["Password"]:"";
                $ProductID     = isset($_REQUEST["ProductID"])?$_REQUEST["ProductID"]:"";
                $type          = isset($_REQUEST["type"])?$_REQUEST["type"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->AccountCreation($Username, $EmailAddress, $Password, $ProductID, $type);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        
        /**
         * Subscribe Product OTT
         * @param string $Username Username
         * @param string $ProductID ProductID
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        
        public function actionSubscribeProduct(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $ProductID     = isset($_REQUEST["ProductID"])?$_REQUEST["ProductID"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->SubscribeProduct($Username,$ProductID);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        /**
         * Unbinding OTT
         * @param string $Username Username
         * @param string $ProductID ProductID
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionUnbinding(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->Unbinding($Username);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        
        /**
         * Suspension OTT
         * @param string $Username Username
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionSuspension(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->Suspension($Username);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        /**
         * Reset Password OTT
         * @param string $Username Username
         * @param string $Newpassword Newpassword
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionResetPassword(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $Newpassword      = isset($_REQUEST["Newpassword"])?$_REQUEST["Newpassword"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->Resetpassword($Username,$Newpassword);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        
        /**
         * Reactivation OTT
         * @param string $Username Username
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionReactivation(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->Reactivation($Username);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        
        /**
         * AccountCancelation OTT
         * @param string $Username Username
         * @param string $Email Email 
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionAccountCancelation(){
            
            if($this->_http->isPostRequest == true){
                
                $Username      = isset($_REQUEST["Username"])?$_REQUEST["Username"]:"";
                $Email         = isset($_REQUEST["EmailAddress"])?$_REQUEST["EmailAddress"]:"";
                $access_code   = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    
                    $return = $this->_ottxml->AccountCancelation($Username,$Email);
                    $result = array("Result"=>$return['Result'],"Errordesc"=>$return['Errordesc']);
                    
                    header('Content-Type: application/json');
                    echo CJSON::encode($result);
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
}
