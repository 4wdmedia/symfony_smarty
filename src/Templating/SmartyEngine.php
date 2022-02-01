<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Templating;

use Psr\Container\ContainerInterface;
use Smarty;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Vierwd\Symfony\Smarty\Event\SmartyInitializeEvent;
use Vierwd\Symfony\Smarty\Extension\SmartyExtension;

class SmartyEngine implements EngineInterface {

	/** @var Smarty */
	protected $smarty = null;

	/** @var EntrypointLookupCollectionInterface */
	protected $entrypointCollection;
	/** @var TemplateNameParserInterface */
	protected $parser;
	/** @var EventDispatcherInterface */
	protected $dispatcher;
	/** @var AuthorizationCheckerInterface */
	protected $authChecker;
	/** @var LoaderInterface */
	protected $loader;
	/** @var KernelInterface */
	protected $kernel;
	/** @var ContainerInterface */
	protected $locator;
	/** @var array */
	protected $templateDirectories;
	/** @var array */
	protected $pluginDirectories;

	public function __construct(
		EntrypointLookupCollectionInterface $entrypointCollection,
		TemplateNameParserInterface $parser,
		EventDispatcherInterface $dispatcher,
		AuthorizationCheckerInterface $authChecker,
		LoaderInterface $loader,
		KernelInterface $kernel,
		ContainerInterface $locator,
		array $templateDirectories,
		array $pluginDirectories = []
	) {
		$this->entrypointCollection = $entrypointCollection;
		$this->parser = $parser;
		$this->dispatcher = $dispatcher;
		$this->authChecker = $authChecker;
		$this->loader = $loader;
		$this->kernel = $kernel;
		$this->locator = $locator;

		$this->templateDirectories = $templateDirectories;

		$pluginDirectories = array_filter($pluginDirectories);
		array_unshift($pluginDirectories, realpath(__DIR__ . '/../Resources/plugins/'));
		$this->pluginDirectories = $pluginDirectories;
	}

	protected function initializeSmarty(): void {
		if ($this->smarty !== null) {
			return;
		}

		$this->smarty = new Smarty();

		if ($this->kernel->getEnvironment() !== 'dev') {
			$this->smarty->setCacheLifetime(120);
			$this->smarty->setCompileCheck(0);
		}

		// $templateProcessor = new TemplatePreprocessor();
		// $this->Smarty->registerFilter('pre', $templateProcessor);
		// $this->Smarty->registerFilter('variable', 'Vierwd\\VierwdSmarty\\View\\clean');

		$cacheDir = $this->kernel->getCacheDir() . '/smarty';
		$this->smarty->setCompileDir($cacheDir . '/templates_c/');
		$this->smarty->setCacheDir($cacheDir . '/cache/');

		if (!is_dir($this->smarty->getCacheDir())) {
			mkdir($this->smarty->getCacheDir(), 0755, true);
		}
		if (!is_dir($this->smarty->getCompileDir())) {
			mkdir($this->smarty->getCompileDir(), 0755, true);
		}

		$this->smarty->addPluginsDir($this->pluginDirectories);
		$this->smarty->loadFilter('pre', 'strip');
		$this->smarty->loadFilter('variable', 'clean');
		$this->smarty->loadFilter('output', 'inlineCSS');

		$this->smarty->setTemplateDir($this->templateDirectories);

		$this->smarty->assign('app', $this->locator->get('twig.app_variable'));
		$this->smarty->assign('tagRenderer', $this->locator->get('tagRenderer'));
		$this->smarty->assign('imageService', $this->locator->get('imageService'));
		$this->smarty->assign('authChecker', $this->authChecker);

		$this->registerExtension('extension.routing');
		$this->registerExtension('extension.twig');
		$this->registerExtension('extension.csrf');
		$this->registerExtension('extension.modifier');
		$this->registerExtension('extension.widget');

		$event = new SmartyInitializeEvent($this);
		$this->dispatcher->dispatch($event, SmartyInitializeEvent::NAME);
	}

	protected function registerExtension(string $name): void {
		$extension = $this->locator->get($name);

		if (! $extension instanceof SmartyExtension) {
			throw new \Exception('Extension ' . $name . ' must implement SmartyExtension interface', 1635929196);
		}

		$extension->register($this->smarty);
	}

	public function getSmarty(): Smarty {
		$this->initializeSmarty();

		return $this->smarty;
	}

	public function render($name, array $parameters = []) {
		$this->initializeSmarty();

		$this->entrypointCollection->getEntrypointLookup('_default')->reset();

		$this->smarty->assign($parameters);
		// $templateReference = $this->parser->parse($name);
		// $this->loader->load($templateReference);
		return $this->smarty->fetch((string)$name);
	}

	public function exists($name) {
		$this->initializeSmarty();

		return $this->smarty->templateExists((string)$name);
	}

	public function supports($name) {
		return substr((string)$name, -4) === '.tpl';
	}
}
