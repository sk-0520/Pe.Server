<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\Security;
use PeServer\Core\InitialValue;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;

/**
 * CSRFトークン埋め込み。
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class CsrfFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'csrf';
	}

	protected function functionBodyImpl(): string
	{
		// このタイミングではセッション処理完了を期待している

		if(!$this->argument->session->tryGet(Security::CSRF_SESSION_KEY, $csrfToken)) {
			return InitialValue::EMPTY_STRING;
		}

		/** @var string $csrfToken */

		$dom = new HtmlDocument();

		$element = $dom->addElement('input');

		$element->setAttribute('type', 'hidden');
		$element->setAttribute('name', Security::CSRF_REQUEST_KEY);
		$element->setAttribute('value', $csrfToken);

		return $dom->build();
	}
}
