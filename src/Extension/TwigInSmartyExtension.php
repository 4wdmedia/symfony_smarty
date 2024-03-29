<?php

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;
use Twig\Environment as TwigEnvironment;

class TwigInSmartyExtension implements SmartyExtension {

	protected TwigEnvironment $twig;

	public function __construct(TwigEnvironment $twig) {
		$this->twig = $twig;
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('block', 'twig', [$this, 'smarty_twig']);
	}

	public function smarty_twig(array $params, ?string $content, Smarty_Internal_Template $smarty, bool &$repeat): string {
		if (!isset($content)) {
			return '';
		}

		$template = $this->twig->createTemplate($content);

		$vars = $smarty->getTemplateVars();
		if (!is_array($vars)) {
			$vars = [];
		}

		return $this->twig->render($template, $vars);
	}
}
