<?php
/**
 * TestindovisionController
 */
class TestindovisionController extends Controller{
    
    private $_url = "https://202.147.197.39/moviebay";
    
    public function __curl($data_string){
        
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
    
    public function actionGetUser(){
        
        $request = '{"requesttype":"infoID","customerID":"401002540453"}';
        
        $data = $this->__curl($request);
        
        var_dump($data);
        
    }

    
    
}
