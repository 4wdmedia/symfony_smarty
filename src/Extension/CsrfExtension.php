<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfExtension implements SmartyExtension {

	/** @var CsrfTokenManagerInterface */
	protected $csrfTokenManager;

	public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
		$this->csrfTokenManager = $csrfTokenManager;
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('function', 'csrf_token', [$this, 'getCsrfToken']);
	}

	public function getCsrfToken(array $params, Smarty_Internal_Template $smarty): string {
		$params += [
			'tokenId' => null,
		];
		$tokenId = $params['tokenId'];
		return $this->csrfTokenManager->getToken($tokenId)->getValue();
	}
}
