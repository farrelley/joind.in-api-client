<?php

/**
 * 
 * @author Shaun Farrell
 *
 */

class FarleyHills_Service_JoindIn_Site extends FarleyHills_Service_JoindIn
{
	protected static $_endPoint = 'site';
	protected $_supportedMethods = array(
		'status',
	);

	public function __construct($username = null, $password = null, $responseFormat = null)
	{
		$this->setResponseFormat($responseFormat);
		parent::__construct($username, $password);
	}
	
	protected function _status($testString)
	{
		$methodType = 'status';
		$action = array('type' => $methodType, 'data' => array('test_string' => $testString));
		$this->_authenticationRequired = false;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
	
}