<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Tool;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\INode;

class ToolJsonPage extends ToolPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		return $this->layout('ツール/Json', new Fragment([
			$this->warning(),
			$this->form('/tool/json', 'tool_json_input', 'tool_json_kind', [
				'format' => '整形',
				'minify' => 'ちっちゃくする',
			]),
			$this->result('tool_json_result'),
		]));
	}
}
