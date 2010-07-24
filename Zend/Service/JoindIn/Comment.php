<?php

/**
 * Comments
 * @author Shaun Farrell
 *
 */

class Zend_Service_JoindIn_Comment extends Zend_Service_JoindIn
{
	/**
	 * comment endpoing
	 * @var string
	 */
	protected static $_endPoint = 'comment';
	
	/**
	 * supported comment methods
	 * @var array
	 */
	protected $_supportedMethods = array(
		'getDetail',
		'markSpam', //TODO: add Method
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
	 * Get detail of an event comment with a given ID
	 * @param string|int $commentId
	 * @param string $commentType
	 * @return Zend_Http_Response
	 */
	protected function _getDetail($commentId, $commentType)
	{
		$supportedCommentTypes = array('event', 'talk');
		if (!in_array(strtolower($commentType), $supportedCommentTypes)) {
			require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported comment type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $commentType);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
		}
			
		$methodType = 'getdetail';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'cid' => $commentId,
				'rtype' => $commentType
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