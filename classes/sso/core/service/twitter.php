<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class SSO_Core_Service_Twitter extends SSO_OAuth {

	/**
	 * @var  string  sso service name
	 */
	protected $sso_service = 'twitter';

	/**
	 * Attempt to log in a user by using an OAuth provider.
	 *
	 * @return  boolean
	 * @uses    Request::current()
	 * @uses    URL::site
	 * @uses    Session::instance()
	 */
	public function login()
	{
		if (Request::current()->query('oauth_token') AND Request::current()->query('oauth_verifier'))
		{
			// Complete login if token and verifier are set
			return $this->complete_login();
		}
		elseif (Request::current()->query('denied'))
		{
			// User denied the access to his / her account
			return FALSE;
		}

		// Set the callback URL where the user will be returned to
		$callback = URL::site($this->sso_config['callback'], Request::current());

		// Add the callback URL to the consumer
		$this->oauth_consumer->callback($callback);

		// Get a request token for the consumer
		$token = $this->oauth_provider->request_token($this->oauth_consumer);

		// Store the request token
		Session::instance()->set($this->oauth_cookie, serialize($token));

		// Redirect to the provider's login page
		Request::current()->redirect($this->oauth_provider->authorize_url($token));
	}

} // End SSO_Core_Service_Twitter