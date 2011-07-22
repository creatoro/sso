<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Single Sign On module for Kohana Auth
 *
 * @package     SSO
 * @author      creatoro
 * @copyright   (c) 2011 creatoro
 * @credits		Geert De Deckere
 * @license     http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
abstract class SSO_Core {

	/**
	 * @var  string  SSO service name
	 */
	protected $sso_service;

	/**
	 * @var  array  SSO configuration
	 */
	protected $sso_config;

	/**
	 * Loads the SSO configuration.
	 *
	 * @return  void
	 * @uses    Kohana::config
	 */
	public function __construct()
	{
		// Load SSO config
		$this->sso_config = Kohana::config('sso.'.$this->sso_service);
	}

	/**
	 * Returns a new SSO object.
	 *
	 * @param   string  $provider
	 * @param   string  $driver
	 * @return  SSO
	 */
	public static function factory($provider, $driver)
	{
		$class = 'SSO_Service_'.$provider.'_'.$driver;

		return new $class;
	}

	abstract public function login();

	abstract protected function complete_login();

} // End SSO_Core