<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Templating;

use Psr\Container\ContainerInterface;
use Smarty;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class SmartyEngine implements EngineInterface {

	/** @var Smarty */
	protected $smarty;

	protected $parser;
	protected $loader;
	protected $locator;

	public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, ContainerInterface $locator, array $templateDirectories, array $pluginDirectories = []) {
		$this->parser = $parser;
		$this->loader = $loader;
		$this->locator = $locator;

		$this->templateDirectories = $templateDirectories;

		$pluginDirectories = array_filter($pluginDirectories);
		array_unshift($pluginDirectories, realpath(__DIR__ . '/../Resources/plugins/'));
		$this->pluginDirectories = $pluginDirectories;
	}

	protected function initializeSmarty() {
		if ($this->smarty) {
			return;
		}

		$this->smarty = new Smarty();

		if ($this->locator->get('kernel')->getEnvironment() !== 'dev') {
			$this->smarty->setCacheLifetime(120);
			$this->smarty->setCompileCheck(false);
		}

		// $templateProcessor = new TemplatePreprocessor();
		// $this->Smarty->registerFilter('pre', $templateProcessor);
		// $this->Smarty->registerFilter('variable', 'Vierwd\\VierwdSmarty\\View\\clean');

		$cacheDir = $this->locator->get('kernel')->getCacheDir() . '/smarty';
		$this->smarty->compile_dir = $cacheDir . '/templates_c/';
		$this->smarty->cache_dir   = $cacheDir . '/cache/';

		if (!is_dir($this->smarty->cache_dir)) {
			mkdir($this->smarty->cache_dir, 0755, true);
		}
		if (!is_dir($this->smarty->compile_dir)) {
			mkdir($this->smarty->compile_dir, 0755, true);
		}

		$this->smarty->addPluginsDir($this->pluginDirectories);
		$this->smarty->loadFilter('pre', 'strip');
		$this->smarty->loadFilter('variable', 'clean');

		$this->smarty->setTemplateDir($this->templateDirectories);

		$this->smarty->assign('tagRenderer', $this->locator->get('tagRenderer'));

		$this->locator->get('extension.routing')->register($this->smarty);
		$this->locator->get('extension.twig')->register($this->smarty);
	}

	public function render($name, array $parameters = []) {
		$this->initializeSmarty();

		$this->smarty->assign($parameters);
		// $templateReference = $this->parser->parse($name);
		// $this->loader->load($templateReference);
		return $this->smarty->fetch($name);
	}

	public function exists($name) {
		$this->initializeSmarty();

		return $this->smarty->templateExists($name);
	}

	public function supports($name) {
		return substr($name, -4) === '.tpl';
	}
}