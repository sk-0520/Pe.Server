<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementEnvironmentPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		// ManagementEnvironmentLogic が phpinfo の body を抽出した HTML。
		return $this->layout('環境情報', new TextNode($this->props->parameter->values['phpinfo'], TextNode::ESCAPE_RAW));
	}
}
