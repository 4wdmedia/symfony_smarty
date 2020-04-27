<?php

function smarty_variablefilter_clean($str) {
	if (is_scalar($str)) {
		$str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
		// replace html-characters
		$str = str_replace(['<', '>', '"'], ['&lt;', '&gt;', '&quot;'], $str);

		return $str;
	} else if ($str === null) {
		return '';
	} else {
		throw new Exception('$str needs to be scalar value');
	}
}