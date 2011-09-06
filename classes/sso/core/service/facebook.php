<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class SSO_Core_Service_Facebook extends SSO_OAuth2 {

	/**
	 * @var  string  sso service name
	 */
	protected $sso_service = 'facebook';

	/**
	 * @var  object  Facebook SDK
	 */
	protected $fb;

	/**
	 * Loads the Facebook SDK.
	 *
	 * @return  void
	 * @uses    Kohana::find_file
	 * @uses    Kohana::config
	 * @uses    Facebook
	 */
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
	 * Attempts to log in a user by using an OAuth provider.
	 *
	 * @return  boolean
	 * @uses    Request::current()
	 */
	public function login()
	{
		if ($session = Request::current()->query('session'))
		{
			// Set session
			$session = json_decode(get_magic_quotes_gpc() ? stripslashes($session) : $session, TRUE);

			// Load session
			$this->fb->setSession($session);

			// Complete login
			return $this->complete_login();
		}
		elseif ($_GET AND ! Request::current()->query('session'))
		{
			// User denied the access to his / her account
			return FALSE;
		}

		// Redirect to provider's login page and ask for e-mail permission
		Request::current()->redirect($this->fb->getLoginUrl(array('display' => 'popup', 'req_perms' => 'email')));
	}

} // End SSO_Core_Service_Facebook