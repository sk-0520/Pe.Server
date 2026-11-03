<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Csrf;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Text;
use PeServer\Core\Web\WebSecurity;

/**
 * @extends HtmlComponentBase<CsrfProps>
 */
class Csrf extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<CsrfProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$security = $this->context->webSecurity;
		if (!$this->context->stores->session->tryGet($security->getCsrfKind(WebSecurity::CSRF_KIND_SESSION_KEY), $token)) {
			return NoneNode::get();
		}

		return match ($this->props->type) {
			'name' => $this->context->builder->inputHidden(new HTMLInputHiddenAttributes(
				name: $security->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME),
				value: Text::toString($token)
			)),
			'id' => $this->context->builder->meta(new HTMLMetaAttributes(
				id: $security->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_ID),
				name: $security->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_ID),
				content: Text::toString($token)
			)),
		};
	}
}
