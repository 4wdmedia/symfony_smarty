<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class CssInlineStyles {

	public static function inlineStyles(string $content): string {
		$metaCharset = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

		$cssToInlineStyles = new CssToInlineStyles();

		$html = $metaCharset . $content;

		// Remove Cache-Buster from Images. SwiftMailer cannot embed images, which do not end with a normal image-file-extension
		$html = (string)preg_replace('/\.(webp|jpe?g|png|gif)\?_=\d+/', '.$1', $html);

		$css = self::extractCSS($html);

		// inline all css-styles
		$html = $cssToInlineStyles->convert($html, $css);

		// strip DOCTYPE and html-tag, make sure all tags are closed; remove all class-attributes
		$document = new DOMDocument('1.0', 'utf-8');
		@$document->loadHTML($html);

		$XPath = new DOMXPath($document);
		$head = $document->getElementsByTagName('head')->item(0);

		// remove all external stylesheets
		$links = self::queryElements($XPath, '//link[@rel="stylesheet"]');
		foreach ($links as $link) {
			if ($link->parentNode) {
				$link->parentNode->removeChild($link);
			}
		}

		// use data-attribute, because Outlook had problems with multiple classes
		$intros = self::queryElements($XPath, '//*[contains(@class, "intro")]');
		foreach ($intros as $intro) {
			$intro->setAttribute('data-intro', 'intro');
		}

		// remove class attributes
		$elements = self::queryElements($XPath, '//*[@class]');
		foreach ($elements as $element) {
			$classes = explode(' ', $element->getAttribute('class'));
			$classes = array_filter($classes, function(string $class) {
				return $class === 'fallback-font' || substr($class, 0, 6) === 'column';
			});
			if (!$classes) {
				$element->removeAttribute('class');
			} else {
				$element->setAttribute('class', implode(' ', $classes));
			}
		}

		// Add fallback-font for Outlook to all paragraphs
		$paragraphs = self::queryElements($XPath, '//p');
		foreach ($paragraphs as $paragraph) {
			$class = $paragraph->getAttribute('class') ?? '';
			$class .= ' fallback-font';
			$paragraph->setAttribute('class', trim($class));
		}

		// our links will have inlined-styles. Some mail clients (iOS) detects phone numbers and events and links them
		if ($head && $head->lastChild) {
			$style = $document->createElement('style');
			$style->setAttribute('type', 'text/css');
			$style->appendChild($document->createTextNode('a {color: inherit; text-decoration: none;}'));
			$head->insertBefore($style, $head->lastChild);
		}

		// inline webfonts
		// if (preg_match_all('/@font-face\s*\{[^}]*\}/', $css, $matches)) {
		// 	$rules = $matches[0];

		// 	$cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
		// 	$staticFolder = $cObj->typolink_url([
		// 		'parameter' => 'static/',
		// 		'forceAbsoluteUrl' => true,
		// 	]);

		// 	$rules = array_map(function(string $rule) use ($staticFolder): string {
		// 		return str_replace('url(\'fonts/', 'url(\'' . $staticFolder . 'fonts/', $rule);
		// 	}, $rules);

		// 	$rules = implode("\n", $rules);

		// 	$style = $document->createElement('style');
		// 	$style->setAttribute('type', 'text/css');
		// 	$style->appendChild($document->createTextNode($rules));
		// 	$head->insertBefore($style, $head->lastChild);
		// }

		// inline media queries
		if ($head && $head->lastChild && preg_match_all('/@media [^{]*+{([^{}]++|{[^{}]*+})*+}/', $css, $matches)) {
			$rules = implode("\n", $matches[0]);

			$style = $document->createElement('style');
			$style->setAttribute('type', 'text/css');
			$style->appendChild($document->createTextNode($rules));
			$head->insertBefore($style, $head->lastChild);
		}

		$htmlElement = $document->getElementsByTagName('html')->item(0);
		return $document->saveHtml($htmlElement) ?: '';
	}

	protected static function extractCSS(string $html): string {
		// strip DOCTYPE and html-tag, make sure all tags are closed; remove all class-attributes
		$document = new DOMDocument('1.0', 'utf-8');
		@$document->loadHTML($html);

		$XPath = new DOMXPath($document);

		$css = '';
		$links = self::queryElements($XPath, '//link[@rel="stylesheet"][@href]');
		foreach ($links as $link) {
			$url = $link->getAttribute('href');
			if (self::isAbsoluteUrl($url)) {
				$css .= file_get_contents($url);
			} else {
				$css .= file_get_contents('public/' . ltrim($url, '/'));
			}
		}

		return $css;
	}

	/**
	 * @return DOMElement[]
	 */
	protected static function queryElements(DOMXPath $XPath, string $query): array {
		$elements = $XPath->query($query);
		$elements = $elements ? iterator_to_array($elements) : [];
		$elements = array_filter($elements, function(DOMNode $node): bool {
			return $node instanceof DOMElement;
		});
		return $elements;
	}

	protected static function isAbsoluteUrl(string $url): bool {
		return false !== strpos($url, '://') || '//' === substr($url, 0, 2);
	}
}
