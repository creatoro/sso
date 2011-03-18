<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class SSO_Service_Twitter extends SSO_Core {

	// SSO parameters
	protected $sso_service = 'twitter';

	// OAuth parameters
	protected $oauth_version = '1';

	/**
	 * Attempt to log in a user by using an OAuth provider
	 *
	 * @return  boolean
	 */
	public function login()
	{
		if (Arr::get($_GET, 'oauth_token') AND Arr::get($_GET, 'oauth_verifier'))
		{
			// Complete login if token and verifier are set
			return $this->complete_login();
		}
		elseif (Arr::get($_GET, 'denied'))
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

} // End SSO_Service_Twitter