<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use \Smarty_Internal_Template;
use \DOMDocument;
use PeServer\Core\Csrf;
use PeServer\Core\HtmlDocument;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;

class CsrfFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	protected function functionBodyImpl(): string
	{
		// このタイミングではセッション処理完了を期待している
		if (!isset($_SESSION[Csrf::SESSION_KEY])) {
			return '';
		}

		$csrfToken = $_SESSION[Csrf::SESSION_KEY];

		$dom = new HtmlDocument();

		$element = $dom->addElement('input');

		$element->setAttribute('type', 'hidden');
		$element->setAttribute('name', Csrf::REQUEST_KEY);
		$element->setAttribute('value', $csrfToken);

		return $dom->build();
	}
}
