# SSO - single sign on module for OAuth providers

With this module you can login your users via OAuth providers while using the Auth module of Kohana.
The currently supported providers:
- Twitter
- Facebook

Thanks goes to Geert De Deckere for his work on OAuth login for [KohanaJobs][0].
[0]: https://github.com/GeertDD/kohanajobs

Read the following to get started.

1. step: Choose your ORM
========================

The module supports Kohana's ORM module and Jelly (other drivers can be added easily).

Kohana ORM
----------
Nothing needs to be done, it's ready for use.

Jelly
-----
1. Create a `classes/model/user.php` model and extend the `Model_Auth_User` class.
2. Define the additional fields for the OAuth providers in the model.


2. step: Enable OAuth providers
===============================
Enable the `oauth` module in `bootstrap.php` and do the following with the needed providers:

- For Twitter: enable the [Twitter API][1] in `bootstrap.php`
- For Facebook: download the [Facebook SDK][2] and uncompress it to `application/vendor/facebook` directory

[1]: https://github.com/shadowhand/apis
[2]: https://github.com/facebook/php-sdk


3. Edit the configuration
=========================

Copy `sso.php` from the `config` directory to `application/config` directory and edit it.

You will have to set 2 URLs for each provider:
- callback URL: this will be the page where the user is returned after he / she confirmed the OAuth request
- login URL: if the login process is interrupted the user will be returned to the login page (this setting isn't used for every provider)


4. Login the user
=================
In your controller all you have to do something like this (the example is for Twitter):

	// Load Auth instance
	$auth = Auth::instance();

	// Login the user via Twitter
	$auth->sso('twitter');


5. Check is a user is logged in trough a specific OAuth provider
================================================================

	$auth->logged_in_sso('twitter');