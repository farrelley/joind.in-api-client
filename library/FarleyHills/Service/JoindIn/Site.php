<?php

/**
 * Site - get the current status of the web service
 * @author Shaun Farrell
 *
 */

class FarleyHills_Service_JoindIn_Site extends FarleyHills_Service_JoindIn
{
	/**
	 * api endpoint for site methods
	 * @var string
	 */
	protected static $_endPoint = 'site';
	
	/**
	 * supported site methods
	 * @var $_supportedMethods array
	 */
	protected $_supportedMethods = array(
		'status',
	);

	/**
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $responseFormat
	 */
	public function __construct($username = null, $password = null, $responseFormat = null)
	{
		$this->setResponseFormat($responseFormat);
		parent::__construct($username, $password);
	}
	
	/**
	 * Get site's current status
	 * @param string $testString
	 * @return Zend_Http_Response
	 */
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