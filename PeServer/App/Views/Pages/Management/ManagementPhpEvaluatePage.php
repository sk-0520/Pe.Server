<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementPhpEvaluatePage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$v = $this->props->parameter->values;
		$nodes = [
			$b->h2(new HtmlGlobalAttributes(), new TextNode('入力')),
			$this->form('/management/php-evaluate', $b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$this->input('PHP', 'php_statement', InputHelperType::Textarea, true),
				$this->submit('submit', '実行'),
			])), 'page-setting-php-evaluate'),
		];
		if ($v['executed']) {
			$pre = fn(string $text) => $b->pre(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode($text));
			$nodes[] = $b->h2(new HtmlGlobalAttributes(), new TextNode('結果'));
			$nodes[] = $b->dl(new HtmlGlobalAttributes(), new Fragment([
				$this->field('出力', $pre($v['output_is_string'] ? Text::toString($v['output']) : Text::dump($v['output']))),
				$this->field('結果', $v['result_is_string']
					? $pre($v['result'])
					: ($v['result'] ? $pre(Text::dump($v['result'])) : new TextNode('なし'))),
				$this->field('実行PHPソース', $pre($v['execute_statement'])),
			]));
		}
		return $this->layout('PHP実行', new Fragment($nodes));
	}
}
