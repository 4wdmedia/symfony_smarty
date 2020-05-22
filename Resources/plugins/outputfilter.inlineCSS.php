<?php

use Vierwd\Symfony\Smarty\CssInlineStyles;

function smarty_outputfilter_inlineCSS(string $template, Smarty_Internal_Template $smarty): string {
	if (!$smarty->getTemplateVars('inlineCSS')) {
		return $template;
	}

	return CssInlineStyles::inlineStyles($template);
}
