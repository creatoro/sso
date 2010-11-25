<?php defined('SYSPATH') or die('No direct access allowed.');

class SSO_Service_Twitter_ORM extends SSO_Service_Twitter {

	/**
	 * Complete the login
	 *
	 * @return  boolean
	 */
	protected function complete_login()
	{
		if ($this->oauth_token AND $this->oauth_token->token !== Arr::get($_GET, 'oauth_token'))
		{
			// Delete the token, it is not valid
			Cookie::delete($this->oauth_cookie);

			// Send the user back to the beginning
			Request::instance()->redirect(URL::site($this->sso_config['login'], Request::$protocol));
		}

		// Get the verifier
		$verifier = Arr::get($_GET, 'oauth_verifier');

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
			error_log($e);
		    return FALSE;
		}

		// Set provider field
		$provider_field = $this->sso_service.'_id';

		// Check whether that id exists in our users table (provider id field)
		$user = ORM::factory('user')->where($provider_field, '=', $data->id)->find();

		// If not, store the new provider id as a new user
		if ( ! $user->loaded())
		{
			// Add user
			$user->$provider_field = $data->id;
			$user->save();
		}

		// If yes, log the user in and give him a normal auth session.
		Auth::instance()->force_login_sso($user, $this->sso_service);

		return TRUE;
	}

} // End SSO_Service_Twitter_ORM