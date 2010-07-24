<?php

/**
 * Joind.In Service
 * @author Shaun Farrell
 * @version $Id$
 */

/*
 * @see Zend_Rest_Client
 */
require 'Zend/Rest/Client.php';

/**
 * @see Zend_Json
 */
require 'Zend/Json.php';

class Zend_Service_JoindIn extends Zend_Rest_Client
{
	/**
	 * Joind.In Base URI
	 * @var string
	 */
	const SERVICE_BASE_URI = 'http://test.joind.in';
	
	/**
	 * entry endpoint for the joind.in service
	 * @var string 
	 */
	const API_ENTRY_POINT = '/api';
	
	/**
	 * Default response type
	 * @var $_defaultResponseFormat string
	 */
	protected static $_defaultResponseFormat = 'json';
	
	/**
	 * @var $_authenticationRequired bool
	 */
	protected $_authenticationRequired = true;
	
	/**
	 * @var $_responseFormat string
	 */
	protected $_responseFormat;
	
	/**
	 * @var $_username string
	 */
	protected $_username;
	
	/**
	 * @var $_password string
	 */
	protected $_password;
	
	/**
	 * @var $_localHttpClient Zend_Http_Client
	 */
	protected $_localHttpClient;
	
	/**
	 * @var $_auth string
	 */
	protected $_auth;
	
	/**
	 * @var $_cookieJar Zend_Http_CookieJar
	 */
	protected $_cookieJar;
	
	/**
	 * supported api endpoints
	 * @var $_supportedApiTypes array
	 */
	protected $_supportedApiTypes = array(
		'site',
		'event',
		'talk',
		'user',
		'comment',
    );
    
    /**
     * supported reponse formats
     * @var $_supportedResponseFormats array
     */
    protected $_supportedResponseFormats = array(
    	'json', 
    	'xml',
    	'array'
    );
    
    /**
     * 
     * @param string $username
     * @param string $password
     * @return void
     */
	public function __construct($username = null, $password = null)
	{
		if (!is_null($username)) {
			$this->setUsername($username);
		}
		if (!is_null($password)) {
			$this->setPassword($password);
		}
		$this->setLocalHttpClient(clone self::getHttpClient());
		$this->setUri(self::SERVICE_BASE_URI);
		$this->_localHttpClient->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');
	}
	
   /**
     * Set local HTTP client as distinct from the static HTTP client
     * as inherited from Zend_Rest_Client.
     *
     * @param Zend_Http_Client $client
     * @return Zend_Service_JoindIn
     */
    public function setLocalHttpClient(Zend_Http_Client $client)
    {
        $this->_localHttpClient = $client;
        return $this;
    }
    
	/**
	 * set username
	 * @param string $username
	 * @return Zend_Service_JoindIn
	 */
	public function setUsername($username)
	{
		$this->_username = (string) $username;
		return $this;
	}
	
	/**
	 * set password
	 * @param string $password
	 * @return Zend_Service_JoindIn
	 */
	public function setPassword($password)
	{
		$this->_password = (string) $password;
		return $this;
	}
	
	/**
	 * get username
	 * @return string
	 */
	public function getUsername()
	{
		return $this->_username;
	}
	
	/**
	 * get password 
	 * @return string
	 */
	public function getPassword()
	{
		return $this->_password;
	}
	
	/**
	 * set response format
	 * @param string $format
	 * @return Zend_Service_JoindIn
	 */
	public function setResponseFormat($format)
	{
		if (!in_array(strtolower($format), $this->_supportedResponseFormats)) {
			require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported response type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $format);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
		}
		$this->_responseFormat = (string) $format;
		return $this;
	}
	
	/**
	 * get the response format
	 * @return string
	 */
	public function getResponseFormat()
	{
		if (!$this->_responseFormat) {
			$this->_responseFormat = self::$_defaultResponseFormat;
		} 
		return $this->_responseFormat;
	}
	
	/**
	 * Proxy the Joind.In API Service endponts
	 * @param string $type
	 * @return Zend_Service_JoindIn
	 */
	public function __get($type)
    {
    	if (!in_array($type, $this->_supportedApiTypes)) {
            require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported API type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $type);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
        }
    	$apiComponent = sprintf('%s_%s', __CLASS__, ucfirst($type));
    	require_once str_replace('_', '/', $apiComponent. '.php');
    	
    	if (!class_exists($apiComponent)) {
            require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Nonexisting API component '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $apiComponent);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
        }
        
        $this->_currentApiPart = $type;
        $this->_currentApiComponent = new $apiComponent(
        	$this->getUsername(),
        	$this->getPassword(),
        	$this->getResponseFormat()
        );
        return $this;
    }

    /**
     * Overload Methods
     * @param string $method
     * @param string $params
     * @return void
     */
    public function __call($method, $params)
    {
        if ($this->_currentApiComponent === null) {
           	require_once 'Zend/Service/JoindIn/Exception.php';
            throw new Zend_Service_JoindIn_Exception('No JoindIn API component set');
        }
        
        $methodOriginal = $method;
        $method = sprintf("_%s", strtolower($method));
        
        if (!method_exists($this->_currentApiComponent, $method)) {
            require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Nonexisting API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $method);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
        }
        
        if (!in_array($methodOriginal, $this->_currentApiComponent->_supportedMethods)) {
            require_once 'Zend/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $methodOriginal);
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
        }
        
        return call_user_func_array(
        	array(
            	$this->_currentApiComponent, 
            	$method
            ), 
            $params
		);
    }
    
    /**
     * set up http client 
     * @return void
     */
    protected function _init()
    {
    	$client = $this->_localHttpClient;
        $client->resetParameters();
        if (null == $this->_cookieJar) {
            $client->setCookieJar();
            $this->_cookieJar = $client->getCookieJar();
        } else {
            $client->setCookieJar($this->_cookieJar);
        }
        
        if ($this->_authenticationRequired) {
        	$this->_prepareAuthentication();
        }
    }
    
    /**
     * Prepares the authenication string
     * @return void
     */
    protected function _prepareAuthentication() 
    {
    	Zend_Debug::dump($this->getUsername());
    	if (NULL === $this->getUsername() || NULL === $this->getPassword()) {
    		require_once 'Zend/Service/JoindIn/Exception.php';
    		$exceptionMessage  = "Username or Password not set";
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
    	}
    	
    	$userDefinedResponseFormat = $this->getResponseFormat();
    	$this->setResponseFormat('json');
    	$validation = $this->user->validate($this->getUsername(), $this->getPassword());
    	
    	$validation = Zend_Json::decode($validation);
    	if ("success" !== $validation['msg']) {
    		require_once 'Zend/Service/JoindIn/Exception.php';
    		$exceptionMessage  = "Invlaid Username or Password";
            throw new Zend_Service_JoindIn_Exception($exceptionMessage);
    	}
    	
    	$this->setResponseFormat($userDefinedResponseFormat);
    	$this->_auth = array('auth' => array('user' => $this->getUsername(), 'pass' => md5($this->getPassword())));
    }
   
    /**
     * perform joind.in api post
     * @param string $path
     * @param string $action
     * @return Zend_Http_Response
     */
    protected function _post($path, $action)
    {
        $this->_prepare($path);
        $query = $this->_setupQuery($action);
        $this->_localHttpClient->resetParameters();
        $response = $this->_localHttpClient->setRawData($query, 'text/json');
        return $this->_localHttpClient->request('POST');
    }
    
    /**
     * Build raw api query
     * @param string $action
     * @return string
     */
    protected function _setupQuery($action)
    {
    	$query = array();
    	if ($this->_authenticationRequired) {
    		$query['request'] = $this->_auth;
    	}   	
    	$query['request']['action'] = $action;
    	
    	if ('array' === $this->getResponseFormat()) {
    		$format = 'json';
    	} else {
    		$format = $this->getResponseFormat();
    	}
    	$query['request']['action']['output'] = $format;
    	return Zend_Json::encode($query);
    }
    
    /**
     * prepare uri for api
     * @param string $path
     * @return void
     */
    protected function _prepare($path)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            require_once 'Zend/Rest/Client/Exception.php';
            $exceptionMessage  = 'URI object must be set before '
                . 'performing call';
            throw new Zend_Rest_Client_Exception($exceptionMessage);
        }

        $uri = $this->_uri->getUri();

        if ($path[0] != '/' && $uri[strlen($uri) - 1] != '/') {
            $path = '/' . $path;
        }

		$this->_uri->setPath(self::API_ENTRY_POINT . $path);
        
        /**
         * Get the HTTP client and configure it for the endpoint URI. Do this 
         * each time because the Zend_Http_Client instance is shared among all
         * Zend_Service_Abstract subclasses.
         */
        $this->_localHttpClient->resetParameters()->setUri($this->_uri);
    }   
}