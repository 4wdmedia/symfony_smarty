<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\EngineInterface;

class SmartyController extends AbstractController {

	protected function render(string $view, array $parameters = [], Response $response = null): Response {
		if (!$this->container->has('templating')) {
			return parent::render($view, $parameters, $response);
		}

		if (null === $response) {
			$response = new Response();
		}

		$engine = $this->container->get('templating');
		if ($engine instanceof EngineInterface) {
			$content = $engine->render($view, $parameters);

			$response->setContent($content);
		}

		return $response;
	}

	public static function getSubscribedServices(): array {
		return array_merge(parent::getSubscribedServices(), [
			'templating' => DelegatingEngine::class,
		]);
	}
}
