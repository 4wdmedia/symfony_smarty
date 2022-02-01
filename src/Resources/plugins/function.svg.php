<?php
declare(strict_types = 1);

use Vierwd\SvgInliner\SvgInliner;

function smarty_function_svg(array $params, Smarty_Internal_Template $smarty): string {
	static $svgInliner;

	if (!$svgInliner) {
		$svgInliner = new SvgInliner(['excludeFromConcatenation' => true]);
	}
	$params = $params + [
		'output' => false,
		'width' => false,
		'height' => false,
		'src' => false,
		'value' => false,
		'class' => false,
	];

	if ($params['output']) {
		return $svgInliner->renderFullSVG();
	}

	if ($params['value']) {
		return $svgInliner->renderSVG($params['value'], $params);
	}

	if ($params['src']) {
		$svgClass = 'svg-' . basename($params['src'], '.svg');
		if ($params['class']) {
			$params['class'] .= ' ' . $svgClass;
		} else {
			$params['class'] = $svgClass;
		}
		return $svgInliner->renderSVGFile($params['src'], $params);
	}

	throw new Exception('Unknown SVG. Missing src or value attribute', 1588074195);
}
