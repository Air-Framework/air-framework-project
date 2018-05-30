<?php

namespace AppNamespace\Controller;

use Air\Controller\BaseController;

class IndexController extends BaseController
{
	/**
	 * By default called on base url "/" without parameters
     * To add parameters to this route url must be /index/index/param1/param2/...
	 */
	public function indexAction()
	{
		$this->render( 'index.html.twig', array('hello' => 'world'));
	}

	/**
	 * Route with params
     * Without router file (routes.yml) called like this :
     * http(s)://base_url/index/param/{hello}/{world}
     * Can be called like this too cause it's IndexController
     * http(s)://base_url/param/{hello}/{world}
     *
	 * @param string $hello
	 * @param string $world
	 */
	public function paramAction($hello = '', $world = '')
	{
		$this->render( 'index.html.twig', array('hello' => $hello, 'world' => $world));
	}

}