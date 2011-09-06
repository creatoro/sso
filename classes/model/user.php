<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Model_Auth_User {

	public static function initialize(Jelly_Meta $meta)
    {
        parent::initialize($meta);

        // Fields defined by the model
        $meta->fields(array(
			'twitter_id' => Jelly::field('integer', array(
				'unique' => TRUE,
			)),
			'facebook_id' => Jelly::field('integer', array(
				'unique' => TRUE,
			)),
        ));
    }

	/**
	 * Finds SSO user based on supplied data.
	 *
	 * @param   string  $provider_field
	 * @param   array   $data
	 * @return  Jelly_Model
	 * @uses    Jelly::query
	 */
	public function find_sso_user($provider_field, $data)
	{
		return Jelly::query('user')->find_sso_user($provider_field, $data);
	}

	/**
	 * Sign-up using data from OAuth provider.
	 *
	 * Override this method to add your own sign up process.
	 *
	 * @param   Jelly_Model  $user
	 * @param   array        $data
	 * @param   string       $provider_field
	 * @return  Jelly_Model
	 * @uses    Jelly::query
	 */
	public function sso_signup(Jelly_Model $user, array $data, $provider_field)
    {
		return Jelly::query('user')->sso_signup($user, $data, $provider_field);
	}

} // End User Model