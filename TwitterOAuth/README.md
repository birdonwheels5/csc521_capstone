## TwitterOAuth ##

PHP library to communicate with Twitter OAuth API version 1.1.

[![Latest Stable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/stable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Total Downloads](https://poser.pugx.org/ricardoper/twitteroauth/downloads.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c/mini.png)](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c) ![License](https://poser.pugx.org/ricardoper/twitteroauth/license.svg)

- Namespaced
- PHP 5.3
- [PSR-0](http://www.php-fig.org/psr/psr-0/ "PHP Framework Interop Group")
- [PSR-2](http://www.php-fig.org/psr/psr-2/ "PHP Framework Interop Group")
- OOP

## Requirements ##

- PHP Version >= 5.3
- PHP cURL extension

## Installation ##

The recommended way to install TwitterOAuth is through [Composer](http://getcomposer.org/):

```json
{
    "require": {
        "ricardoper/twitteroauth": "1.*"
    }
}
```

## Example ##

```php
<?php

	use TwitterOAuth\TwitterOAuth;

	date_default_timezone_set('UTC');

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Array with the OAuth tokens provided by Twitter when you create application
	 *
	 * output_format - Optional - Values: text|json|array|object - Default: object
	 */
    $config = array(
        'consumer_key' => '01b307acba4f54f55aafc33bb06bbbf6ca803e9a',
        'consumer_secret' => '926ca39b94e44e5bdd4a8705604b994ca64e1f72',
        'oauth_token' => 'e98c603b55646a6d22249d9b0096e9af29bafcc2',
        'oauth_token_secret' => '07cfdf42835998375e71b46d96b4488a5c659c2f',
        'output_format' => 'object'
    );

	/**
	 * Instantiate TwitterOAuth class with set tokens
	 */
	$tw = new TwitterOAuth($config);


	/**
	 * Returns a collection of the most recent Tweets posted by the user
	 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	 */
	$params = array(
	    'screen_name' => 'ricard0per',
	    'count' => 5,
	    'exclude_replies' => true
	);

	/**
	 * Send a GET call with set parameters
	 */
	$response = $tw->get('statuses/user_timeline', $params);

	var_dump($response);


	/**
	 * Creates a new list for the authenticated user
	 * https://dev.twitter.com/docs/api/1.1/post/lists/create
	 */
	$params = array(
	    'name' => 'TwOAuth',
	    'mode' => 'private',
	    'description' => 'Test List',
	);

	/**
	 * Send a POST call with set parameters
	 */
	$response = $tw->post('lists/create', $params);

	var_dump($response);
```

## License ##

Released under the MIT License.
