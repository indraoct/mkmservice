<?php
/**
 * Secuity encrypt decrypt
 * Author       : Indra Octama
 * Create Date  : 15 Desember 2015
 * Credit To    : https://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
 */

class security {
     
    private $_encrypt_method;
    private $_secret_key;
    private $_secret_iv;
    
        public function __construct() {

            $this->_encrypt_method = "AES-256-CBC";
            $this->_secret_key = '1029384756qpwoeiruty6574839201';
            $this->_secret_iv = '999338476279100kdllkdj';
        }
      
        public  function wrap($txt){
          
          return $this->encrypt_decrypt("encrypt", $txt);
            
        }

        public  function baca($txt){
          
          return $this->encrypt_decrypt("decrypt", $txt);
            
        }

        
       private function encrypt_decrypt($action, $string) {
            $output = false;

            // hash
            $key = hash('sha256', $this->_secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $this->_secret_iv), 0, 16);

            if( $action == 'encrypt' ) {
                $output = openssl_encrypt($string, $this->_encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            }
            else if( $action == 'decrypt' ){
                $output = openssl_decrypt(base64_decode($string), $this->_encrypt_method, $key, 0, $iv);
            }

            return $output;
        }

    
}


