<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Auth extends Kohana_Auth {

	/**
	 * Logs in a user via an OAuth provider
	 *
	 * @param   string   provider name (e.g. 'twitter', 'facebook')
	 * @return  boolean
	 */
	public function sso($provider)
	{
		// Set the type
		if ( ! $type = $this->_config->get('driver'))
		{
			$type = 'ORM';
		}

		return SSO::factory($provider, $type)->login();
	}

	/**
	 * Checks if a user logged in via an OAuth provider
	 *
	 * @param   string   provider name (e.g. 'twitter', 'facebook')
	 * @return  boolean
	 */
	public function logged_in_sso($provider = NULL)
	{
		// For starters, the user needs to be logged in
		if ( ! parent::logged_in())
		{
			return FALSE;
		}

		// Get the user from the session
		$user = $this->get_user();

		if ($provider !== NULL)
		{
			// Check for one specific OAuth provider
			$provider = $provider.'_id';
			return ! empty($user->$provider);
		}

		// Otherwise, just check the password field
		// We don't store passwords for OAuth users
		return empty($user->password);
	}

	/**
	 * Forces a user to be logged in when using SSO, without specifying a password.
	 *
	 * @param   mixed    username string, or user Jelly object
	 * @param   boolean  mark the session as forced
	 * @return  boolean
	 */
	public function force_login_sso($user, $provider, $mark_session_as_forced = FALSE)
	{
		// Set the type
		if ( ! $type = $this->_config->get('driver'))
		{
			$type = 'ORM';
		}

		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			if ($type == 'ORM')
			{
				$user = ORM::factory('user');
				$user->where($this->unique_key($username), '=', $username)->find();
			}
			elseif ($type == 'Jelly')
			{
				$user = Jelly::query('user')->where($this->unique_key($username, $provider), '=', $username)->limit(1)->select();
			}
		}

		if ($mark_session_as_forced === TRUE)
		{
			// Mark the session as forced, to prevent users from changing account information
			$this->_session->set('auth_forced', TRUE);
		}

		// Create a new autologin token
		if ($type == 'ORM')
		{
			$token = ORM::factory('user_token');
			$token->user_id = $user->id;
		}
		elseif ($type == 'Jelly')
		{
			$token = Jelly::factory('user_token');
			$token->user = $user->id;
		}

		// Set token data

		$token->expires = time() + $this->_config['lifetime'];
		$token->save();

		// Set the autologin cookie
		Cookie::set('authautologin', $token->token, $this->_config['lifetime']);

		// Run the standard completion
		$this->complete_login($user);
	}
	
	/**
	 * Allows a model use email, username and OAuth provider id as unique identifiers for login
	 *
	 * @param   string  unique value
	 * @param   string  OAuth provider name
	 * @return  string  field name
	 */
	public function unique_key($value, $oauth_provider = NULL)
	{
		if ($oauth_provider)
		{
			return $oauth_provider.'_id';
		}

		return Validate::email($value) ? 'email' : 'username';
	}
	
} // End Auth