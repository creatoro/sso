# SSO - single sign on module for OAuth providers

With this module you can login (and sign up) your users via OAuth providers while using the Auth module of Kohana.
The currently supported providers:

* Twitter
* Facebook

Thanks goes to Geert De Deckere for his work on OAuth login for [KohanaJobs](https://github.com/GeertDD/kohanajobs).

Read the following to get started.

1. step: Modify your users table
============================================

See the included `auth-schema-mysql.sql` file for the correct table structure.


2. step: Choose your ORM
============================================

By default the module supports [Jelly's](https://github.com/creatoro/kohana-jelly-for-Kohana-3.1) `3.1/develop` branch.
Drivers for Kohana's [ORM](https://github.com/kohana/orm) can be downloaded [here](https://github.com/creatoro/orm-sso).

3. step: Enable OAuth providers
============================================

Enable the `oauth` [module](http://github.com/kohana/oauth) in `bootstrap.php` and do the following with the needed providers:

* For Twitter: enable the [Twitter API](https://github.com/shadowhand/apis) in `bootstrap.php`
* For Facebook: download the [Facebook SDK](https://github.com/facebook/php-sdk) and uncompress it to `application/vendor/facebook` directory

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
============================================

Copy `sso.php` from the `config` directory to `application/config` directory and edit it.

You will have to set 2 URLs for each provider:

* callback URL: this will be the page where the user is returned after he / she confirmed the OAuth request
* login URL: if the login process is interrupted the user will be returned to the login page (this setting isn't used for every provider, though it is advisable to have it set)

IMPORTANT: in many cases you want the 2 URLs to be the same as the login completion is not called seperately from initiating the login process. This feature is for only the sake of flexibility.


5. step: Login the user
============================================

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

If the user wasn't found during the login process the current sign up method saves the user as a new user. It
also merges the OAuth account with a standard account if they share the same e-mail address.

You can define your own sign up method. Check out the following files depending on your ORM:

### Jelly

 1. Copy `module_directory/classes/model/builder/user.php` to `application_directory/classes/model/builder/user.php`.
 2. Create a `sso_signup()` method` in `application_directory/classes/model/builder/user.php` and customize it to your needs.

 To get an example on how to create the method check it out in `module_directory/classes/model/builder/auth/user.php`.

### Kohana ORM

 1. Copy `orm_driver_directory/classes/model/user.php` to `application_directory/classes/model/user.php`.
 2. Customize the `sso_signup()` method to your needs.