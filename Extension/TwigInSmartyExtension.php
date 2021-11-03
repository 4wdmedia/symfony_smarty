<?php

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;
use Twig\Environment as TwigEnvironment;

class TwigInSmartyExtension implements SmartyExtension {

	/** @var \Twig\Environment */
	protected $twig = null;

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
		return $this->twig->render($template, $smarty->getTemplateVars());
	}
}
