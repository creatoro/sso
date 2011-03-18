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
		// Set Driver
		if ( ! $driver = $this->_config->get('driver'))
		{
			$driver = 'ORM';
		}

		return SSO::factory($provider, $driver)->login();
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
		// Set the driver
		if ( ! $driver = $this->_config->get('driver'))
		{
			$driver = 'ORM';
		}

		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			if ($driver == 'ORM')
			{
				$user = ORM::factory('user');
				$user->where($this->unique_key($username), '=', $username)->find();
			}
			elseif ($driver == 'Jelly')
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
		if ($driver == 'ORM')
		{
			$token = ORM::factory('user_token');
			$token->user_id = $user->id;
		}
		elseif ($driver == 'Jelly')
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

		return Valid::email($value) ? 'email' : 'username';
	}
	
} // End Auth