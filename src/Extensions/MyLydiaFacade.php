<?php

namespace App\Extensions;

use Pythagus\Lydia\Lydia;

/**
 * Class bMyLydiaFacade
 *
 * @author: Damien MOLINA
 */
class MyLydiaFacade extends Lydia {

	/**
	 * Get the Lydia's configuration.
	 *
	 * @return array
	 */
	protected function setConfigArray() {
		return require __DIR__ . '/lydia.php' ;
	}

	/**
	 * Format the callback URL to be valid
	 * regarding the Lydia server.
	 *
	 * @param string $url
	 * @return string
	 */
	public function formatCallbackUrl(string $url) {
		return '' . $url ;
	}

}