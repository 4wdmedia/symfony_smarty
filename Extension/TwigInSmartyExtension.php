<?php

namespace Vierwd\Symfony\Smarty\Extension;

use Psr\Container\ContainerInterface;
use Smarty;
use Smarty_Internal_Template;

class TwigInSmartyExtension {


	public function __construct(ContainerInterface $locator) {
		$this->locator = $locator;
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('block', 'twig', [$this, 'smarty_twig']);
	}

	public function smarty_twig($params, $content, Smarty_Internal_Template $smarty, &$repeat) {
		if (!isset($content)) {
			return;
		}

		$twig = $this->locator->get('twig');
		$template = $twig->createTemplate($content);
		return $twig->render($template, $smarty->getTemplateVars());
	}
}
