<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Environment as TwigEnvironment;

class TwigEngine implements EngineInterface {

	protected TemplateNameParserInterface $parser;
	protected LoaderInterface $loader;
	protected TwigEnvironment $twig;

	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, TwigEnvironment $twig) {
		$this->parser = $parser;
		$this->loader = $loader;
		$this->twig = $twig;
	}

	public function render($name, array $parameters = []) {
		return $this->twig->render((string)$name, $parameters);
	}

	public function exists($name) {
		try {
			$this->twig->load((string)$name);
		} catch (\Throwable $e) {
			return false;
		}

		return true;
	}

	public function supports($name) {
		return substr((string)$name, -5) === '.twig';
	}
}
