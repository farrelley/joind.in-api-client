<?php

/**
 * Joind.In Service
 * @author Shaun Farrell
 * @version $Id$
 */

class FarleyHills_Service_JoindIn extends Zend_Rest_Client
{	
	const SERVICE_BASE_URI = 'http://www.joind.in';
	const API_ENTRY_POINT = '/api';
	
	protected static $_defaultResponseFormat = 'json';
	protected $_authenticationRequired = true;
	
	protected $_responseFormat;
	protected $_username;
	protected $_password;
	protected $_localHttpClient;
	protected $_auth;
	protected $_cookieJar;
	
	protected $_supportedApiTypes = array(
		'site',
		'event',
		'talk',
		'user',
		'comment',
    );
    
    protected $_supportedResponseFormats = array(
    	'json', 
    	'xml',
    	'array'
    );
    
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
     * @return self
     */
    public function setLocalHttpClient(Zend_Http_Client $client)
    {
        $this->_localHttpClient = $client;
        return $this;
    }
	
	public function setUsername($username)
	{
		$this->_username = (string) $username;
		return $this;
	}
	
	public function setPassword($password)
	{
		$this->_password = (string) $password;
		return $this;
	}
	
	public function getUsername()
	{
		return $this->_username;
	}
	
	public function getPassword()
	{
		return $this->_password;
	}
	
	public function setResponseFormat($format)
	{
		if (!in_array(strtolower($format), $this->_supportedResponseFormats)) {
			require_once 'FarleyHills/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported response type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $format);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
		}
		$this->_responseFormat = (string) $format;
		return $this;
	}
	
	public function getResponseFormat()
	{
		if (!$this->_responseFormat) {
			$this->_responseFormat = self::$_defaultResponseFormat;
		} 
		return $this->_responseFormat;
	}
	
	public function __get($type)
    {
    	if (!in_array($type, $this->_supportedApiTypes)) {
            require_once 'FarleyHills/Service/JoindIn/Exception.php';
            $exceptionMessage  = "Unsupported API type '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $type);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
        }
    	$apiComponent = sprintf('%s_%s', __CLASS__, ucfirst($type));
    	require_once str_replace('_', '/', $apiComponent. '.php');
    	
    	if (!class_exists($apiComponent)) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Nonexisting API component '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $apiComponent);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
        }
        
        echo $apiComponent . "<br />";
        
        $this->_currentApiPart = $type;
        $this->_currentApiComponent = new $apiComponent(
        	$this->getUsername(),
        	$this->getPassword(),
        	$this->getResponseFormat()
        );
        return $this;
    }

    public function __call($method, $params)
    {
    	echo $method . "<br />";
    	Zend_Debug::dump($params);

        if ($this->_currentApiComponent === null) {
           // TODO:  Fix-> require_once 'Zend/Service/GitHub/Exception.php';
            throw new FarleyHills_Service_JoindIn_Exception('No JoindIn API component set');
        }
        
        $methodOriginal = $method;
        $method = sprintf("_%s", strtolower($method));
        
        if (!method_exists($this->_currentApiComponent, $method)) {
            // TODO Fix--> require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Nonexisting API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $method);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
        }
        
        if (!in_array($methodOriginal, $this->_currentApiComponent->_supportedMethods)) {
            // TODO Fix--> require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Unsupported API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $methodOriginal);
            throw new FarleyHills_Service_JoindIn_Exception($exceptionMessage);
        }
        
        return call_user_func_array(
        	array(
            	$this->_currentApiComponent, 
            	$method
            ), 
            $params
		);
    }
    
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
        	$this->_auth = array('auth' => array('user' => $this->getUsername(), 'pass' => md5($this->getPassword())));
        }
    }
   
    protected function _post($path, $action)
    {
        $this->_prepare($path);
        $query = $this->_setupQuery($action);
        $this->_localHttpClient->resetParameters();
        $response = $this->_localHttpClient->setRawData($query, 'text/json');
        return $this->_localHttpClient->request('POST');
    }
    
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