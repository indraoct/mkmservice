<?php

/**
 * API IPTV 2.0
 * @Author : Indra Octama
 * @CreatedDate : 23 November 2015
 * @Purposed : Rebuilt API IPTV
 */
class IndexController extends Controller
{
    
        private $_http;
        private $_access_code;
    
        /**
         * init function   
         */
        public function init(){
            
            Yii::import('application.extensions.iptv.action.*');
            
            $this->_http = new CHttpRequest();
            $this->_access_code = Yii::app()->params["access_iptv"];

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
         * Bind
         * @param string $UserID User ID to be bind
         * @param string $access_code access code to API
         * 
         * @return JSON Example {"Result":"0","Errordesc":"success"}
         */
        
        public function actionBind(){
         
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
               
                if($this->_access_code == $access_code){
                        $class = new BindMain($UserID);

                        header('Content-Type: application/json');
                        echo $class->BindAction();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
                

            
        }
        
        /**
         * Subscription
         * @param string $UserID UserID to be register
         * @param string $LoginName Loginname (distinguish to $UserID)
         * @param string $Password Password
         * @param string $CityCode Citycode based on SLCS application definitio , example : 0000 , it means Central Jakarta
         * @param array $ProductID Example : array("ProductID"=>array("MARS","VENUS"));
         * @param string $UserGroupID Group ID based on SLCS application
         * @param string $TerminalType Example : 1(STB), 2(MOBILE), 3(PAD)
         * @param string $FatherLoginName FatherLogin (if the user is child form some parent user)
         * @param string $access_code access code to API
         * 
         * @return JSON example {"Result":"0","Errordesc":"success"}
         */
        public function actionSubscription(){

            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $LoginName = isset($_REQUEST["LoginName"])?$_REQUEST["LoginName"]:"";
                $Password = isset($_REQUEST["Password"])?$_REQUEST["Password"]:"";
                $CityCode = isset($_REQUEST["CityCode"])?$_REQUEST["CityCode"]:"";
                $ProductID = isset($_REQUEST["ProductID"])?$_REQUEST["ProductID"]:"";
                $UserGroupID = isset($_REQUEST["UserGroupID"])?$_REQUEST["UserGroupID"]:"";
                $TerminalType = isset($_REQUEST["TerminalType"])?$_REQUEST["TerminalType"]:"";
                $FatherLoginName = isset($_REQUEST["FatherLoginName"])?$_REQUEST["FatherLoginName"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
               
                if($this->_access_code == $access_code){
                
                        $class = new SubscriptionMain($UserID, $LoginName, $Password, $CityCode, $ProductID, $UserGroupID, $TerminalType, $FatherLoginName);

                        header('Content-Type: application/json');
                        echo $class->SubscriptionAction();
                        
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }

            }else{
                throw new CHttpException(403,'Forbidden.');
            }    
        }
        
        /**
         * UserState
         * @param string $UserID 
         * @param string $access_code access code to API
         * 
         * @return JSON example : {"Result":"0","Errordesc":"success"}
         */
        public function actionUserstate(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
               
                if($this->_access_code == $access_code){
                        $class = new QueryUserStateMain($UserID);

                        header('Content-Type: application/json');
                        echo $class->QueryUserStateAction();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }        
            }else{
                
                throw new CHttpException(403,'Forbidden.');
            }
            
            
            
        }
        
        /**
         * QueryOrderProduct
         * @param string $UserID UserID  
         * @param string $Password Password 
         * @param string $access_code access code to API
         * 
         * @return JSON example : {"Result":"0","Productlist":{"ProductListItem":[{"ProductID":"Mars"},{"ProductID":"Venus"}]},"Errordesc":"success"}
         */
        
        public function actionQueryOrderProduct(){
            
            if($this->_http->isPostRequest == true){
           
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $Password = isset($_REQUEST["Password"])?$_REQUEST["Password"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
               
                if($this->_access_code == $access_code){    
                    $class = new QueryOrderProductMain($UserID,$Password);

                     header('Content-Type: application/json');
                     echo $class->QueryOrderProductAction();
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
            
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        /**
         * ChangeProducts
         * @param string $UserID
         * @param array $Productlist Example : array(array("ProductID"=>"...","Operation" => ""))  --> 1: Add, 2 : Delete
         * @param string $access_code access code to API
         * 
         * @return JSON  Example : {"Result":"0","Errordesc":"success"}
         */
        
        public function actionChangeProducts(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $Productlist = isset($_REQUEST["Productlist"])?$_REQUEST["Productlist"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                        $class = new ChangeProductsMain($UserID,$Productlist);

                        header('Content-Type: application/json');
                        echo $class->ChangeProductsAction();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                        
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
            
        }
        
        /**
         * ChangeGroup
         * @param string $UserID UserID tobe change group
         * @param string $UserGroupID UserGroupID 
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        
        public function actionChangeGroup(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $UserGroupID = isset($_REQUEST["UserGroupID"])?$_REQUEST["UserGroupID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                        $class = new ChangeGroupMain($UserID,$UserGroupID);
                        
                        header('Content-Type: application/json');
                        echo $class->ChangeGroupAction();
                        
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
        }
        
        /**
         * QuerySTBid
         * @param string $UserID UserID
         * @param string $Password Password
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","STBID":"","Errordesc":"success"}
         */
        public function actionQuerySTBid(){
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $Password = isset($_REQUEST["Password"])?$_REQUEST["Password"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new QuerySTBidMain($UserID,$Password);
                    
                    header('Content-Type: application/json');
                    echo $class->QuerySTBidACtion();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                    
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
        }
        
        /**
         * Unbind
         * @param string $UserID User ID
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        
        public function actionUnbind(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new UnbindMain($UserID);

                    header('Content-Type: application/json');
                    echo $class->UnbindAction();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }

            
        }
        
        /**
         * Suspension
         * @param string $UserID User ID
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionSuspension(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new SuspensionMain($UserID);

                    header('Content-Type: application/json');
                    echo $class->SuspensionAction();
                    
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
        }

        /**
         * Reactivation
          * @param string $UserID User ID
         * @param string $access_code access code to API
         * 
         * @return JSON Example : {"Result":"0","Errordesc":"success"}
         */
        public function actionReactivation(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new ReactivationMain($UserID);
                    
                    header('Content-Type: application/json');
                    echo $class->ReactivationAction();
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }
        }
        
        /**
         * Unsubscription
         * @param string $UserID UserID to be unregistered
         * @param string $access_code access code to API
         * 
         * @return JSON Example {"Result":"0","Errordesc":"success"}
         */
        
        public function actionUnsubscription(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new UnsubscriptionMain($UserID);

                     header('Content-Type: application/json');
                     echo $class->UnsubscriptionAction();
                
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }    
        }
        
        /**
         * ResetPassword
         * @param string $UserID UserID Registered
         * @param string $Password New Passwordassword
         * @param string $access_code access code to API
         * 
         * @return JSON Example {"Result":"0","Errordesc":"success"}
         */
        
        public function actionResetPassword(){
            
            if($this->_http->isPostRequest == true){
                
                $UserID = isset($_REQUEST["UserID"])?$_REQUEST["UserID"]:"";
                $Password = isset($_REQUEST["Password"])?$_REQUEST["Password"]:"";
                $access_code = isset($_REQUEST["access_code"])?$_REQUEST["access_code"]:"";
                
                if($this->_access_code == $access_code){
                    $class = new ResetPasswordMain($UserID, $Password);

                    header('Content-Type: application/json');
                    echo $class->ResetPasswordAction();
                
                }else{
                    throw new CHttpException(403,'Forbidden.');
                }
                
            }else{
                throw new CHttpException(403,'Forbidden.');
            }        

        }
}
