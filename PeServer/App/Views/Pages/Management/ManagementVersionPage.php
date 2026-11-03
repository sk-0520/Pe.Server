<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTextAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementVersionPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		return $this->layout('バージョン設定', $this->form(
            '/management/version',
			$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$this->field('バージョン', new Fragment([
					$b->inputText(new HTMLInputTextAttributes(
						id: 'version',
                        name: 'version',
                        value: $p->values['version'],
						className: 'edit' . (empty($p->errors['version']) ? '' : ' error'),
						pattern: '[0-9]+\.[0-9]+\.[0-9]+',
					)),
					new ShowErrorMessages(new ShowErrorMessagesProps($p->errors, 'version'), $this->context),
				])),
				$b->dt(new HtmlGlobalAttributes(), $b->button(new HTMLButtonAttributes(
					id: 'command_get_versions',
                    type: ButtonType::Button,
					data: new DataAttributes(['release_url' => $p->values['release_url']]),
				), new TextNode('取得'))),
				$b->dd(new HtmlGlobalAttributes(), $b->pre(new HtmlGlobalAttributes(id: 'versions'), new TextNode('未取得'))),
				$this->submit('登録'),
			]))
		), '/scripts/management_version.js');
	}
}
