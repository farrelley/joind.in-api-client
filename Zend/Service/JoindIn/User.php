<?php

/**
 * User.php - get information on users
 * @author Shaun Farrell
 * @version $Id$
 *
 */

class Zend_Service_JoindIn_User extends Zend_Service_JoindIn
{
	/**
	 * class api endpoint
	 * @var string
	 */
	protected static $_endPoint = 'user';
	
	/**
	 * supported endppoint methods
	 * @var $_supportedMethods array
	 */
	protected $_supportedMethods = array(
		'getDetail',
		'getComments',
		'validate',
		'getProfile',
	);

	/**
	 * constructor
	 * @param string $username
	 * @param string $password
	 * @param string $responseFormat
	 * @return void
	 */
	public function __construct($username = null, $password = null, $responseFormat = null)
	{
		$this->setResponseFormat($responseFormat);
		parent::__construct($username, $password);
	}
	
	/**
	 * Get detail of a user, given either user ID or username
	 * @param string $userId
	 * @return Zend_Http_Response
	 */
	protected function _getDetail($userId)
	{
		if ("" === $userId) {
			require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "No username or user id was specified.";
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
		} 
		$methodType = 'getdetail';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'uid' => $userId
			)
		);
		$this->_authenticationRequired = true;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
	
	/**
	 * Get the user's talk and event comments
	 * @param string $username joindin user
	 * @param string $type type of comment [optional]
	 * @return Zend_Http_Response
	 */
	protected function _getComments($username, $type = NULL)
	{	
		$methodType = 'getcomments';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'username' => $username
			)
		);
		
		$supportedCommentTypes = array('event', 'talk');
		if (!is_null($type)) {
			if (!in_array(strtolower($type), $supportedCommentTypes)) {
				require_once 'Zend/Service/JoindIn/Exception.php';
            	$exceptionMessage  = "Unsupported comment type '%s' used";
            	$exceptionMessage = sprintf($exceptionMessage, $type);
            	throw new Zend_Service_JoindIn_Exception($exceptionMessage);
			}
			$action['data']['type'] = $type;
		}
		
		$this->_authenticationRequired = false;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
	
	/**
	 * Check login/password to check login
	 * @param string $username
	 * @param string $password
	 * @return Zend_Http_Response
	 */
	protected function _validate($username, $password) 
	{
		if ('' === $username || '' === $password) {
			require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Username and password are required.";
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
		}
		
		$methodType = 'validate';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'uid' => $username,
				'pass' => md5($password)
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
	 * Request the information for a certain speaker profile
	 * @param string $speakerAccessId
	 * @return Zend_Http_Response
	 */
	protected function _getProfile($speakerAccessId) 
	{
		$methodType = 'getprofile';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'spid' => $speakerAccessId
			)
		);
		$this->_authenticationRequired = false;
		$this->_init();
		$response = $this->_post(self::$_endPoint. '/' . $methodType, $action);
		Zend_Debug::dump($speakerAccessId);
		if ('array' === $this->getResponseFormat()) {
			return Zend_Json::decode($response->getBody());
		}
		return $response->getBody();
	}
}