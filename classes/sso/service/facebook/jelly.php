<?php defined('SYSPATH') or die('No direct access allowed.');

class SSO_Service_Facebook_Jelly extends SSO_Service_Facebook {

	/**
	 * Complete the login
	 *
	 * @return  boolean
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
			// Log the error and return false
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		    return FALSE;
		}

		// Set provider field
		$provider_field = $this->sso_service.'_id';

		// Check whether that id exists in our users table (provider id field)
		$user = Jelly::query('user_sso_jelly')
			->where($provider_field, '=', $data['id'])
			->or_where('email', '=', $data['email'])
			->limit(1)
			->select();

		// Signup if necessary
		Jelly::query('user_sso_jelly')->signup_sso($user, $data, $provider_field);

		// Give the user a normal login session
		Auth::instance()->force_login_sso($user->$provider_field, $this->sso_service);

		return TRUE;
	}

} // End SSO_Service_Facebook_Jelly