<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Templating;

use Psr\Container\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class TwigEngine implements EngineInterface {

	protected $parser;
	protected $loader;
	protected $locator;

	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, ContainerInterface $locator) {
		$this->parser = $parser;
		$this->loader = $loader;
		$this->locator = $locator;
	}

	public function render($name, array $parameters = []) {
		return $this->locator->get('twig')->render($name, $parameters);
	}

	public function exists($name) {
		try {
			$this->locator->get('twig')->load($name);
		} catch (\Throwable $e) {
			return false;
		}

		return true;
	}

	public function supports($name) {
		return substr($name, -5) === '.twig';
	}
}