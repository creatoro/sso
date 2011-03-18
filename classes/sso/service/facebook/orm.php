<?php defined('SYSPATH') or die('No direct access allowed.');

class SSO_Service_Facebook_ORM extends SSO_Service_Facebook {

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
		$user = ORM::factory('user')
			->where($provider_field, '=', $data['id'])
			->or_where('email', '=', $data['email'])
			->find();

		// Signup if necessary
		ORM::factory('user_sso_orm')->signup_sso($user, $data, $provider_field);

		// Give the user a normal login session
		Auth::instance()->force_login_sso($user, $this->sso_service);

		return TRUE;
	}

} // End SSO_Service_Facebook_ORM