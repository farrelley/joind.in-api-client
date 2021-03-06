<?php

/**
 * Joind.In Talk
 * @author Shaun Farrell
 * 
 */

class Zend_Service_JoindIn_Talk extends Zend_Service_JoindIn
{
	/**
	 * api endpoint for talk
	 * @var string
	 */
	protected static $_endPoint = 'talk';
	
	/**
	 * supported api talk methods 
	 * @var array
	 */
	protected $_supportedMethods = array(
		'getDetail',
		'getComments',
		'claim', //TODO: add this method
		'add', //TODO : add this method
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
	 * Get the details for given talk number
	 * @param string|int $talkId
	 * @return Zend_Http_Response
	 *
	 * TODO: add private event
	 */
	protected function _getDetail($talkId)
	{
		$methodType = 'getdetail';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'talk_id' => $talkId
			)
		);
		$this->_authenticationRequired = false;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
	
	/**
	 * Get all comments associated with a talk
	 * @param string|int $talkId
	 * @return Zend_Http_Response
	 */
	protected function _getComments($talkId)
	{
		$methodType = 'getcomments';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'talk_id' => $talkId
			)
		);
		$this->_authenticationRequired = false;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
}