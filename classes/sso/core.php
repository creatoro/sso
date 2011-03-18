<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Single Sign On module for Kohana Auth
 *
 * @package     SSO
 * @author      creatoro
 * @copyright   (c) 2010 creatoro
 * @credits		Geert De Deckere
 * @license     http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
abstract class SSO_Core {

	// SSO parameters
	protected $sso_service;
	protected $sso_config;

	// OAuth parameters
	protected $oauth_provider;
	protected $oauth_consumer;
	protected $oauth_token;
	protected $oauth_cookie;
	protected $oauth_version;

	public function __construct()
	{
		// Set SSO config
		$this->sso_config = Kohana::config('sso.'.$this->sso_service);

		// Set OAuth cookie
		$this->oauth_cookie = 'oauth_token_'.$this->sso_service;

		// Set OAuth provider
		$this->oauth_provider = $this->sso_service;

		// Load the configuration for this OAuth provider
		$config = Kohana::config('oauth.'.$this->oauth_provider);

		// Do the setup for OAuth 1.0
		if ($this->oauth_version == '1')
		{
			// Create a consumer from the config
			$this->oauth_consumer = OAuth_Consumer::factory($config);

			// Load the provider
			$this->oauth_provider = OAuth_Provider::factory($this->oauth_provider);
		}

		// Retrieve token if available
		if ($token = Session::instance()->get($this->oauth_cookie))
		{
			// Get the token from storage
			$this->oauth_token = unserialize($token);
		}
	}

	/**
	 * Return a new SSO object
	 *
	 * @param   string  name of the OAuth provider
	 * @param   string  name of driver
	 * @return  object  SSO object
	 */
	public static function factory($provider, $driver)
	{
		$class = 'SSO_Service_'.$provider.'_'.$driver;

		return new $class;
	}

	abstract public function login();

	abstract protected function complete_login();

} // End SSO_Core