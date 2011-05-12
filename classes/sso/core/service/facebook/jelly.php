<?php defined('SYSPATH') or die('No direct access allowed.');

class SSO_Core_Service_Facebook_Jelly extends SSO_Service_Facebook {

	/**
	 * Completes the login and signs up a user if necessary.
	 *
	 * @return  boolean
	 * @uses    Kohana::$log
	 * @uses    Log::ERROR
	 * @uses    Kohana_Exception::text
	 * @uses    Jelly::factory
	 * @uses    Auth::instance
	 */
	protected function complete_login()
	{
		try
		{
			// Get user details
			$data = $this->fb->api('/me');
		}
		catch (FacebookApiException $e)
		{
			// Log the error and return FALSE
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		    return FALSE;
		}

		// Set provider field
		$provider_field = $this->sso_service.'_id';

		// Check whether that id exists in our users table (provider id field)
		$user = Jelly::factory('user')->find_sso_user($provider_field, $data);

		// Signup if necessary
		$signup = Jelly::factory('user')->sso_signup($user, $data, $provider_field);

		// Give the user a normal login session
		Auth::instance()->force_sso_login($signup);

		return TRUE;
	}

} // End SSO_Core_Service_Facebook_Jelly