<?php defined('SYSPATH') or die('No direct access allowed.');

class SSO_Core_Service_Twitter_Jelly extends SSO_Service_Twitter {

	/**
	 * Completes the login and signs up a user if necessary.
	 *
	 * @return  boolean
	 * @uses    Request::current()
	 * @uses    Session::instance()
	 * @uses    URL::site
	 * @uses    Twitter::factory
	 * @uses    Kohana::$log
	 * @uses    Log::ERROR
	 * @uses    Kohana_Exception::text
	 * @uses    Jelly::factory
	 * @uses    Auth::instance
	 */
	protected function complete_login()
	{
		if ($this->oauth_token AND $this->oauth_token->token !== Request::current()->query('oauth_token'))
		{
			// Delete the token, it is not valid
			Session::instance()->delete($this->oauth_cookie);

			// Send the user back to the beginning
			Request::current()->redirect(URL::site($this->sso_config['login'], Request::current()));
		}

		// Get the verifier
		$verifier = Request::current()->query('oauth_verifier');

		// Store the verifier in the token
		$this->oauth_token->verifier($verifier);

		// Exchange the request token for an access token
		$this->oauth_token = $this->oauth_provider->access_token($this->oauth_consumer, $this->oauth_token);

		try
		{
			// Get user details
			$data = Twitter::factory('account')->verify_credentials($this->oauth_consumer, $this->oauth_token);
		}
		catch (Kohana_OAuth_Exception $e)
		{
			// Log the error and return false
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		    return FALSE;
		}

		// Set provider field
		$provider_field = $this->sso_service.'_id';

		// Data to array
		$data = (array) $data;

		// Check whether that id exists in our users table (provider id field)
		$user = Jelly::factory('user')->find_sso_user($provider_field, $data);

		// Signup if necessary
		$signup = Jelly::factory('user')->sso_signup($user, $data, $provider_field);

		// Give the user a normal login session
		Auth::instance()->force_sso_login($signup);

		// Login complete
		return TRUE;
	}

} // End SSO_Core_Service_Twitter_Jelly