<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

/**
 * CSRFトークン埋め込み。
 */
class CsrfFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region TemplateFunctionBase

	public function getFunctionName(): string
	{
		return 'csrf';
	}

	protected function functionBodyImpl(): string
	{
		// このタイミングではセッション処理完了を期待している

		if (!$this->argument->stores->session->tryGet($this->argument->webSecurity->getCsrfKind(WebSecurity::CSRF_KIND_SESSION_KEY), $csrfToken)) {
			return Text::EMPTY;
		}
		/** @var string $csrfToken */

		/** @var string $type */
		$type = $this->params['type'] ?? 'name';

		$dom = new HtmlDocument();

		switch ($type) {
			case 'id':
				$element = $dom->addTagElement('meta');

				$element->setAttribute('id', $this->argument->webSecurity->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_ID));
				$element->setAttribute('name', $this->argument->webSecurity->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_ID));
				$element->setAttribute('content', $csrfToken);
				break;

			case 'name':
				$element = $dom->addTagElement('input');

				$element->setAttribute('type', 'hidden');
				$element->setAttribute('name', $this->argument->webSecurity->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME));
				$element->setAttribute('value', $csrfToken);
				break;

			default:
				throw new NotImplementedException();
		}

		return $dom->build();
	}

	#endregion
}
