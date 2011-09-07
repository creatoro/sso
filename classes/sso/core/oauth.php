<?php defined('SYSPATH') or die('No direct script access.');

abstract class SSO_Core_OAuth extends SSO {

	/**
	 * @var  object  OAuth consumer
	 */
	protected $oauth_consumer;

	/**
	 * @var  mixed  OAuth provider
	 */
	protected $oauth_provider;

	/**
	 * @var  object  OAuth token
	 */
	protected $oauth_token;

	/**
	 * @var  object  OAuth cookie
	 */
	protected $oauth_cookie;

	/**
	 * Sets up everything needed for OAuth.
	 *
	 * @return  void
	 * @uses    Kohana::$config
	 * @uses    OAuth_Consumer::factory
	 * @uses    OAuth_Provider::factory
	 * @uses    Session::instance
	 */
	public function __construct()
	{
		parent::__construct();

		// Set OAuth cookie
		$this->oauth_cookie = 'oauth_token_'.$this->sso_service;

		// Set OAuth provider
		$this->oauth_provider = $this->sso_service;

		// Load the OAuth configuration for this OAuth provider
		$oauth_config = Kohana::$config->load('oauth.'.$this->oauth_provider);

		// Create a consumer from the OAuth config
		$this->oauth_consumer = OAuth_Consumer::factory($oauth_config);

		// Load the provider
		$this->oauth_provider = OAuth_Provider::factory($this->oauth_provider);

		// Retrieve token if available
		if ($token = Session::instance()->get($this->oauth_cookie))
		{
			// Get the token from storage
			$this->oauth_token = unserialize($token);
		}
	}

} // End SSO_Core_OAuth