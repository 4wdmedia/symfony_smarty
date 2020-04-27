<?php

function smarty_prefilter_strip(string $template): string {
	$replacements = [
		'{pre}' => '{/strip}',
		'{/pre}' => '{strip}',
	];

	// Smarty v3.1.32 changed handling of strip and comments.
	// Whitespace after comments is not stripped.
	// @see https://github.com/smarty-php/smarty/issues/436
	// We do not want this behaviour. That's why we strip all comments
	$template = preg_replace('-\{\*.*?\*\}-s', '', $template);

	$search = array_keys($replacements);
	$replace = array_values($replacements);

	$template = str_replace($search, $replace, $template);
	$template = '{strip}' . $template . '{/strip}';
	return $template;
}