<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Tool;

use Override;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\INode;

/**
 * @extends ToolPageBase<RootProps>
 */
class ToolBase64Page extends ToolPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		return $this->layout('ツール/base64', new Fragment([
			$this->warning(),
			$this->form('/tool/base64', 'tool_base64_input', 'tool_base64_kind', [
				'encode' => 'エンコード(あ => 44GC)',
				'decode' => 'デコード(44GC => あ)',
			]),
			$this->result('tool_base64_result'),
		]));
	}
}
