<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\INode;

class HomePrivacyPage extends HomePageBase
{
	#[Override]
	protected function getNode(): INode
	{
		/** @var string $privacyPolicy */
		$privacyPolicy = $this->props->parameter->values['privacy_policy'];

		return $this->layout(
			'プライバシーポリシー',
			new Markdown(new MarkdownProps($privacyPolicy, 'privacy'), $this->context)
		);
	}
}
