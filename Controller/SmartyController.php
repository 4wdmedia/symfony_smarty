<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Vierwd\Symfony\Smarty\Templating\SmartyEngine;

class SmartyController extends AbstractController {

	protected function render(string $view, array $parameters = [], Response $response = null): Response {
		if ($this->container->has('smarty')) {
			$content = $this->container->get('smarty')->render($view, $parameters);
		} else {
			return parent::render($view, $parameters, $response);
		}

		if (null === $response) {
			$response = new Response();
		}

		$response->setContent($content);

		return $response;
	}

	public static function getSubscribedServices() {
		return array_merge(parent::getSubscribedServices(), [
			'smarty' => SmartyEngine::class,
		]);
	}
}
