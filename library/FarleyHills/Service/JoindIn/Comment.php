<?php

/**
 * Comments
 * @author Shaun Farrell
 *
 */
class FarleyHills_Service_JoindIn_Comment extends FarleyHills_Service_JoindIn
{
protected static $_endPoint = 'comment';
	
	protected $_supportedMethods = array(
		'getDetail',
		'markSpam', //TODO: add Method
	);

	public function __construct($username = null, $password = null, $responseFormat = null)
	{
		$this->setResponseFormat($responseFormat);
		parent::__construct($username, $password);
	}
	
	protected function _getDetail($commentId, $commentType)
	{
		$supportedCommentTypes = array('event', 'talk');
		if (!in_array(strtolower($commentType), $supportedCommentTypes)) {
			require_once 'FarleyHills/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported comment type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $commentType);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
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