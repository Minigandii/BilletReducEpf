<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Lydia main properties
	|--------------------------------------------------------------------------
	|
	| This array lists the properties that won't change
	| from a request to another. This properties are added
	| in the LydiaRequest.executeRequest() method.
	*/
	'properties' => [
		// Currency: only EUR and GBP are supported.
		'currency' => 'EUR',
	],

	/*
	|--------------------------------------------------------------------------
	| Lydia debugging mode
	|--------------------------------------------------------------------------
	|
	| This value determines whether the website is in a debugging mode.
	| The transactions won't be move money.
	*/
	'debug' => true,

	/*
	|--------------------------------------------------------------------------
	| Lydia credentials
	|--------------------------------------------------------------------------
	|
	| This array contains the different PaymentLydia's credentials according
	| to the previous debug value.
	*/
	'credentials' => [
		/*
		 * Debugging mode.
		 */
		'debug' => [
			'url_server'    => 'https://homologation.lydia-app.com',
			'vendor_token'  => "64ef479303811732396550",
			'private_token' => "64ef479307bf5180963969",
		],

		/*
		 * Production mode.
		 */
		'prod'  => [
			'url_server'    => 'https://lydia-app.com',
			'vendor_token'  => "64ef479303811732396550",
			'private_token' => "64ef479307bf5180963969",
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Lydia's URL
	|--------------------------------------------------------------------------
	|
	| This array contains the available and used Lydia's url
	*/
	'url' => [
		/*
		 * URL used to make transactions.
		 */
		'do' => '/api/request/do.json',

		/*
		 * URL used to check the payment state.
		 */
		'state' => '/api/request/state.json',

		/*
		 * URL used to refund transaction. This
		 * route is sometimes comment to be unusable.
		 */
		'refund' => '/api/transaction/refund.json',
	],

] ;