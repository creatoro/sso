# SSO - single sign on module for OAuth providers

With this module you can login (and sign up) your users via OAuth providers while using the Auth module of Kohana.
The currently supported providers:

* Twitter
* Facebook

Thanks goes to Geert De Deckere for his work on OAuth login for [KohanaJobs][0].
[0]: https://github.com/GeertDD/kohanajobs

Read the following to get started.

1. step: Modify your users table
================================

See the included `mysql.sql` file for the correct table structure.


2. step: Choose your ORM
========================

The module supports Kohana's ORM module and Jelly (other drivers can be added easily).

IMPORTANT: The module supports Jelly's latest, unstable branch.

### Kohana ORM

Nothing needs to be done, it's ready for use.

### Jelly (only latest, unstable branch is supported)

1. Create a `classes/model/user.php` model and extend the `Model_Auth_User` class.
2. Define additional fields for the OAuth providers in the model, for example:

		public static function initialize(Jelly_Meta $meta)
		{
			parent::initialize($meta);

			// Additional fields
			$meta->fields(array(
				'twitter_id' => new Jelly_Field_Integer(array(
					'unique' => TRUE,
				)),
				'facebook_id' => new Jelly_Field_Integer(array(
					'unique' => TRUE,
				)),
			));
		}


3. step: Enable OAuth providers
===============================

Enable the `oauth` module in `bootstrap.php` and do the following with the needed providers:

* For Twitter: enable the [Twitter API][1] in `bootstrap.php`
* For Facebook: download the [Facebook SDK][2] and uncompress it to `application/vendor/facebook` directory

[1]: https://github.com/shadowhand/apis
[2]: https://github.com/facebook/php-sdk

### Set options for OAuth providers

Copy `oauth.php` from the oauth module's `config` directory to `application/config` directory and edit it.

Set the key and secret for the providers like this (for Facebook use your App Id as key):

	return array(
		'twitter' => array(
			'key' => 'xxx',
			'secret' => 'xxx'
		),
	);


4. step: Edit the configuration
===============================

Copy `sso.php` from the `config` directory to `application/config` directory and edit it.

You will have to set 2 URLs for each provider:

* callback URL: this will be the page where the user is returned after he / she confirmed the OAuth request
* login URL: if the login process is interrupted the user will be returned to the login page (this setting isn't used for every provider, though it is advisable to have it set)

IMPORTANT: in many cases you want the 2 URLs to be the same as the login completion is not called seperately from initiating the login process. This feature is for only the sake of flexibility.


5. step: Login the user
=======================

In your controller all you have to do is something like this (the example is for Twitter):

	// Load Auth instance
	$auth = Auth::instance();

	// Login the user via Twitter
	if ($auth->sso('twitter'))
	{
		// The SSO module returns TRUE if user is logged in, we can issue a redirect in this case
		$this->request->redirect('');
	}


+1: Customize the sign up process
============================================

If the user wasn't found during the login provess the current sign up method saves the user in the users as a new
user. It also merges standard and OAuth accounts if previous account was found by e-mail address.

You can define your own sign up method. Check out the following files depending on your ORM:

### Kohana ORM

Find the `classes/model/user/sso/orm.php` in the module directory.

### Jelly (only latest, unstable branch is supported)

Find the `classes/model/builder/user/sso/jelly.php` in the module directory.