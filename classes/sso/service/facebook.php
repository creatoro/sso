<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class SSO_Service_Facebook extends SSO_Core {

	// SSO parameters
	protected $sso_service = 'facebook';

	// OAuth parameters
	protected $oauth_version = '2';

	// Facebook
	protected $fb;

	public function __construct()
	{
		// Include Facebook SDK
		include Kohana::find_file('vendor', 'facebook/src/facebook');

		// Set config
		$config = Kohana::config('oauth.facebook');

		// Setup Facebook
		$this->fb = new Facebook(
			array(
				'appId'  => $config['key'],
				'secret' => $config['secret'],
				'cookie' => TRUE,
			)
		);

		parent::__construct();
	}

	/**
	 * Attempt to log in a user by using an OAuth provider
	 *
	 * @return  boolean
	 */
	public function login()
	{
		if (Arr::get($_GET, 'session'))
		{
			// Set session
			$session = json_decode(get_magic_quotes_gpc() ? stripslashes(Arr::get($_GET, 'session')) : Arr::get($_GET, 'session'), TRUE);

			// Load session
			$this->fb->setSession($session);

			// Complete login
			return $this->complete_login($this->sso_service);
		}
		elseif ($_GET AND ! Arr::get($_GET, 'session'))
		{
			// User denied the access to his / her account
			return FALSE;
		}

		// Redirect to provider's login page and ask for e-mail permission
		Request::instance()->redirect($this->fb->getLoginUrl(array('req_perms' => 'email')));
	}

} // End SSO_Service_Facebook