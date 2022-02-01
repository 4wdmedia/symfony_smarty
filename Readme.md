# Symfony Smarty Extension

> Use [Smarty](http://www.smarty.net/) in your templates.

## Installation

Install using [composer](https://getcomposer.org/):
```
composer require 'vierwd/symfony-smarty'
```

### Usage in controllers

To use smarty templates for your controller just extend the `Vierwd\Symfony\Smarty\Controller\SmartyController`.
You can then use `$this->render('error/error.tpl')` to render a Smarty template.

#### Example

```php
// src/Controller/IndexController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Vierwd\Symfony\Smarty\Controller\SmartyController;

class IndexController extends SmartyController {

	/**
	 * @Route("/", name="index")
	 */
	public function index(Request $request): Response {
		return $this->render('index/index.tpl', ['message' => 'Hello from Smarty']);
	}
}
```

```Smarty
{* templates/index/index.tpl *}

{$message}
```

### Pre-defined variables

There are some variables, that are always available to your templates:

Variable Name | Contents
--------------|---------
app | `Symfony\Bridge\Twig\AppVariable`
tagRenderer | `Symfony\WebpackEncoreBundle\Asset\TagRenderer`
imageService | An image service which can be used to scale images using imagemagick
authChecker | `AuthorizationCheckerInterface`

### Pre-defined smarty functions, blocks and modifiers

- csrf_token
- integer
- url
- path
- svg
- twig
- widget
- inlineCSS

### Power-Block: twig

If you still need some twig logic, you can embed twig template code within your Smarty templates:

#### Twig in Smarty template
```smarty
{$message}
{twig}
	{literal}
		{{ form_start(createForm) }}
		{{ form_rest(createForm) }}
	{/literal}
{/twig}
```
