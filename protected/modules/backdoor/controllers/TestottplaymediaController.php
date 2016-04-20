<?php
/**
 * @author  Indra Octama
 * @createddate 31 Maret 2016
 * @purpose testing if userid still has response or not
 */
class TestottplaymediaController extends Controller{
 
    private $_http;
    
    public function init(){
        
        Yii::import("application.extensions.ottplaymedia.OttPlaymedia");
        $this->_http = new CHttpRequest();
        
    }
    
    /**
     * get user id
     * 
     * @throws CHttpException
     */
    public function actionGetuserid(){
       
        if($this->_http->isPostRequest == true){
            
            $userid = isset($_POST["userid"])?strip_tags($_POST["userid"]):"";
            
            if($userid != ""){
                $ottplaymedia = new OttPlaymedia();
                
                $data =  $ottplaymedia->getUserData($userid);
                
                var_dump($data);
                
            }else{
                header('Content-Type: application/json');
                echo CJSON::encode(array("error_code"=>"userid is empty or wrong format!!"));
            }
            
        }else{
            throw new CHttpException(403,'Forbidden.');
        }
    }
   
}