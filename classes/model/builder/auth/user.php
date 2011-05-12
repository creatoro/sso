<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Jelly auth user for adding own sign-up method.
 *
 * @package     Jelly/SSO
 * @author      creatoro
 * @copyright   (c) 2011 creatoro
 * @license     http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Model_Builder_Auth_User extends Jelly_Builder {

	/**
	 * Finds SSO user based on supplied data.
	 *
	 * @param   string       $provider_field
	 * @param   array        $data
	 * @return  Jelly_Model
	 */
	public function find_sso_user($provider_field, $data)
	{
		// Build query
		$user = $this->where($provider_field, '=', $data['id']);

		if (isset($data['email']))
		{
			// Add email to search if set
			$user->or_where('email', '=', $data['email']);
		}

		return $user->limit(1)->select();
	}

	/**
	 * Sign-up using data from OAuth provider.
	 *
	 * Override this method to add your own sign up process.
	 *
	 * @param   Jelly_Model  $user
	 * @param   array        $data
	 * @param   string       $provider
	 * @return  Jelly_Model
	 */
	public function sso_signup(Jelly_Model $user, array $data, $provider_field)
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

			// Save user
			$user->save(FALSE);
		}
		elseif ($user->loaded() AND empty($user->$provider_field))
		{
			// If user is found, but provider id is missing add it to details.
			// We can do this merge, because this means user is found by email address,
			// that is already confirmed by this OAuth provider, so it's considered trusted.
			$user->$provider_field = $data['id'];

			// Save user
			$user->save();
		}

		// Return user
		return $user;
    }

} // End Model_Builder_Auth_User