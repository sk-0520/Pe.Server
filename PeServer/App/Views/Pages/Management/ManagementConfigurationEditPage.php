<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementConfigurationEditPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		return $this->layout('現在設定', $this->form(
            '/management/configuration/edit',
			$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$b->dt(new HtmlGlobalAttributes(), new TextNode('setting.' . $p->values['env_name'] . '.json')),
				$b->dt(new HtmlGlobalAttributes(), $b->code(new HtmlGlobalAttributes(), new TextNode($p->values['path']))),
				$b->dd(new HtmlGlobalAttributes(), new Fragment([
					$b->textarea(new HTMLTextAreaAttributes(
						id: 'json',
                        name: 'json',
                        className: 'edit json' . (empty($p->errors['json']) ? '' : ' error'),
						autoFocus: true,
                        required: true,
					), new TextNode(Text::toString($p->values['json']))),
					new ShowErrorMessages(new ShowErrorMessagesProps($p->errors, 'json'), $this->context),
				])),
				$this->submit('更新'),
			])),
            'page-management-config-edit'
		));
	}
}
