<?php

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingExtension {

	private $generator;

	public function __construct(UrlGeneratorInterface $generator) {
		$this->generator = $generator;
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('function', 'url', [$this, 'getUrl']);
		$smarty->registerPlugin('function', 'path', [$this, 'getPath']);
	}

	public function getPath(array $params, Smarty_Internal_Template $smarty): string {
		// string $name, array $parameters = [], bool $relative = false
		$params = $params + [
			'name' => null,
			'parameters' => [],
			'relative' => false,
		];
		$name = $params['name'];
		$parameters = $params['parameters'];
		$relative = $params['relative'];
		return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	public function getUrl(array $params, Smarty_Internal_Template $smarty): string {
		// string $name, array $parameters = [], bool $schemeRelative = false
		$params = $params + [
			'name' => null,
			'parameters' => [],
			'schemeRelative' => false,
		];
		$name = $params['name'];
		$parameters = $params['parameters'];
		$schemeRelative = $params['schemeRelative'];
		return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
	}
}