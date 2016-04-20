<?php
/**
 * Class GeneralFunction Samsung
 * @author Indra Octama <indra.ctama@mncgroup.com>
 * @createddate 18 Januari 2016
 */
class GeneralFunction{

   
    /**
     * insertUserSamsung
     * 
     */
    public static function insertUserSamsung(array $data){
       
       Yii::import("application.models.Samsung.UserSamsung"); 
       Yii::import("application.extensions.samsung.security");
       
       $transaction = Yii::app()->db->beginTransaction();
       $db = new UserSamsung();
       $security = new security();
       try{ 
            $db->attributes = array("userid"=>$data["userid"],
                                "userid_prefix" =>$data["userid"].Yii::app()->params["suffix_samsung"],
                                "usertype"=>"4",
                                "password"=>$security->wrap($data["password"]),
                                "vouchercode"=>$data["vouchercode"],
                                "imei"=>$data["imei"],
                                "created_date"=>date("Y-m-d H:i:s")
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
     * Delete User Samsung
     * 
     */
    public static function deleteUserSamsung($userid){
        
        Yii::import("application.models.Samsung.UserSamsung");
        
         /*
          * Jika Email
          */
         $model = UserSamsung::model()->findByPk($userid);
         $model->delete();
         
         /*
          * Jika User ID Delete By User ID
          */
         UserSamsung::model()->delete("`userid` = :userid ", array(':userid' => $userid));
         
        
    } 
    
    public static function getUserSamsung($userid){
        
        Yii::import("application.models.Samsung.UserSamsung");
       
        
         $model = new UserSamsung();
           
                
            $data = $model->findAll(array(
                        'condition'=>'userid=:userid',
                        'params'=>array('userid'=>$userid),
                    ));
                   
            
            if($data != null){
                foreach($data as $dt){
                 
                      $value[] = $dt->attributes;
                    
                }
                
                return $value[0];
            }else{
                return false;
            }
        
    }
    
    /**
     * getUserProductCode
     * @Description : method to get Prodcut code from userid
     */
    public static function getUserProductCode($userid,$password){
        
        Yii::import("application.extensions.iptv.action.*");
        
        //class
        $QueryOrderProduct = new QueryOrderProductMain($userid, $password); 
       
        //action
        $responseJSON = $QueryOrderProduct->QueryOrderProductAction();
        
        //response       
        $response = CJSON::decode($responseJSON,true);
       
        
        return $response;
        
                  
    }

    /**
     * register user slcs
     * @Description : method to insert data user to slcs
     */
    public static function registerUserSlcs(array $dataUser){
 
        
        Yii::import("application.extensions.iptv.action.*");
        Yii::import("application.extensions.ott.*");
        
        /**
         * class ott
         */
        $ott = new ClassOtt();
        
            //create account
            $data = $ott->AccountCreation($dataUser["userid"],$dataUser["userid"].Yii::app()->params["suffix_samsung"], $dataUser["password"], $dataUser["product_id"], "2");
 
            if(isset($data['Result'])){
                if($data['Result'] == "0"){
                    return true;
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
             
             for($i =0;$i < count($productcode);$i++){
               $ott->SubscribeProduct($userid, $productcode[$i]);
             }
            
             return true;
             
         }else{
             return false;
         }
         
     }
     
     /**
      * Load Model UserSamsung
      */
     public static function loadModelUserSamsung($userid) {
         
        Yii::import("application.models.Samsung.UserSamsung"); 
         
        $model = UserSamsung::model()->findByPk($userid);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
  
   
    
}
