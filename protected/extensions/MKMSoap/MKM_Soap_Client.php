<?php
/*
 * Author : Indra Octama
 */
//custom soap client
//add timeout property for handling soap timeout
//credit to: https://therobzone.wordpress.com/2009/10/21/timing-out-php-soap-calls/

class MKM_Soap_Client extends SoapClient
{
	private $_operationTimeout;
	private $_connectionTimeout;
	private $_curlErrNo;
	private $_lastRequestHeader;
	private $_lastRequest;
	public $testRequest;
	private $_lastResponseHeader;
	private $_lastResponse;
        
        
 
	public function setOperationTimeout($timeout)
	{
		if (!is_int($timeout) && !is_null($timeout))
		{
			throw new Exception("Invalid timeout value");
		}
 
		$this->_operationTimeout = $timeout;
	}

	public function setConnectionTimeout($timeout)
	{
		if (!is_int($timeout) && !is_null($timeout))
		{
			throw new Exception("Invalid timeout value");
		}
 
		$this->_connectionTimeout = $timeout;
	}
	
	public function __doRequest($request, $location, $action, $version, $one_way = FALSE)
	{
		
		

               
		$this->_lastResponseHeader = '';
		
		if(!isset($this->_connectionTimeout))
			throw new Exception('Connection timeout is not set');
		if(!isset($this->_operationTimeout))
			throw new Exception('Operation timeout is not set');	
		
		// Call via Curl and use the timeout
		$curl=curl_init($location);

		curl_setopt($curl, CURLOPT_VERBOSE, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('soapAction: '.$action,	//THIS HEADER IS IMPORTANT AND HAS TO EXIST!
													 "Expect:",
													 "Content-Type: text/xml; charset=UTF-8",
                                                                                                         "Accept-Encoding: utf-8",
												
													));
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->_operationTimeout);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->_connectionTimeout);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this,'readResponseHeader')); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$response=curl_exec($curl);
		$this->_lastRequestHeader=curl_getinfo($curl,CURLINFO_HEADER_OUT);

		$this->_lastRequest= json_encode($request);		
		$this->_lastResponse=json_encode($response);

		
		if (curl_errno($curl))
		{
			$this->_curlErrNo=curl_errno($curl);
		}
 		
 		curl_close($curl);	
		
		if(!$one_way){
			return ($response);
                }      
	}
	
	public function __getLastRequestHeaders() {
		return $this->_lastRequestHeader;
	}
	
	public function __getLastResponseHeaders() {
		return $this->_lastResponseHeader;
	}
	
	public function __getLastRequest() {
		return json_decode($this->_lastRequest);
	}
	
	public function __getLastResponse() {
		return json_decode($this->_lastResponse);
	}
	
	public function getCurlErrNo() {
		return $this->_curlErrNo;
	}
	
	public function readResponseHeader($curl, $header) {
		$this->_lastResponseHeader .= $header;
		return strlen($header);
	}


	
}
