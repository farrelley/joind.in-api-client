<?php

/**
 * Events
 * @author Shaun Farrell
 * @version $Id$
 */

class Zend_Service_JoindIn_Event extends Zend_Service_JoindIn
{
	protected static $_endPoint = 'event';
	
	protected $_supportedMethods = array(
		'getDetail',
		'add', //TODO: Implement
		'getTalks',
		'getListing',
		'attend', //TODO: Implement
		'addComment', //TODO: Implement
		'getComments',
		'getTalkComments',
		'addTrack', //TODO: Implement
	);

	/**
	 * constructor
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
	 * Get the details for a given event number
	 * @param int|string $eventId
	 * @return Zend_Http_Response
	 */
	protected function _getDetail($eventId)
	{
		$methodType = 'getdetail';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'event_id' => $eventId
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
	 * Gets the talks assoiated with an event
	 * @param int|string $eventId
	 * @return Zend_Http_Response
	 */
	protected function _getTalks($eventId)
	{
		$methodType = 'gettalks';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'event_id' => $eventId
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
	 * Gets the event listing for various types
	 * @param string $eventType
	 * @return Zend_Http_Response
	 */
	protected function _getListing($eventType)
	{
		$methodType = 'getlist';
		$supportedEventTypes = array('hot', 'upcoming', 'past', 'pending');
		if (!in_array(strtolower($eventType), $supportedEventTypes)) {
			require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported event type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $eventType);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
		}
		
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'event_type' => $eventType
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
	 * Get all comments associated with an event
	 * @param int|string $eventId
	 * @return Zend_Http_Response
	 */
	protected function _getComments($eventId)
	{
		$methodType = 'getcomments';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'event_id' => $eventId
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
	 * Get all comments associated with sessions at an event. Private comments are not shown, 
	 * results are returned in date order with newest first.
	 * @param int|string $eventId
	 * @return Zend_Http_Response
	 */
	protected function _getTalkComments($eventId)
	{
		$methodType = 'gettalkcomments';
		$action = array(
			'type' => $methodType, 
			'data' => array(
				'event_id' => $eventId
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