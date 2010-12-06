<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Jelly auth user for adding own signup method
 *
 * @package     Jelly/SSO
 * @author      creatoro
 * @copyright   (c) 2010 creatoro
 * @license     http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Model_Builder_User_SSO_Jelly extends Jelly_Builder {

	/**
	 * Sign-up using data from OAuth provider
	 * Override this method to add your own sign up process
	 *
	 * @param   object  user
	 * @param   array   available data (provider id, email, etc.)
	 * @param   string  the field of the OAuth provider
	 * @return  object
	 */
	public function signup_sso($user, array & $data, $provider_field)
    {
		if ( ! $user->loaded())
		{
			// Add user
			$user->$provider_field = $data['id'];

			// Set email if it's available via OAuth provider
			if (isset($data['email']))
			{
				$user->email = $data['email'];
			}

			$user->save();
		}
		elseif ($user->loaded() AND empty($user->$provider_field))
		{
			// If user is found, but provider id is missing add it to details.
			// We can do this merge, because this means user is found by email address,
			// that is already confirmed by this OAuth provider, so it's considered trusted.
			$user->$provider_field = $data['id'];
			$user->save();
		}
    }

} // End Model_Builder_User_SSO_Jelly