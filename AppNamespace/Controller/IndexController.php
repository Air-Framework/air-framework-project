<?php

namespace AppNamespace\Controller;

use Air\Controller\BaseController;
use Air\Bootstrap\Bootstrap;

class IndexController extends BaseController
{
	/**
	 * route = /lang/{locale}
	 * @param $route
	 */
    public function langAction($route)
	{
		$_SESSION['locale'] = $route[2];

		if($_SERVER["HTTP_REFERER"]) {
			$this->redirectTo($_SERVER["HTTP_REFERER"]);
		}

		$this->redirectTo('/result');
	}

	/**
	 * This is the first showed screen
	 * @param $route
	 */
	public function indexAction($route)
	{
		$this->render( 'index.html.twig', array('hello' => 'world'));
	}

	/**
	 * This is the first showed screen
	 * @param string $hello
	 * @param string $world
	 */
	public function paramAction($hello = '', $world = '', $hi)
	{
		$this->render( 'index.html.twig', array('hello' => $hello, 'world' => $world));
	}

}