<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Error;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class ErrorPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$status = $this->props->parameter->httpStatus;
		$errorNumber = Text::toString($this->props->parameter->values['error_number'] ?? '');
		$message = Text::toString($this->props->parameter->values['message'] ?? 'なし');

		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: 'エラー: ' . Text::toString($status->value),
			children: new Fragment([
				$b->p(new HtmlGlobalAttributes(), new TextNode('問題が発生しました。')),
				$b->dl(new HtmlGlobalAttributes(), new Fragment([
					$b->dt(new HtmlGlobalAttributes(), new TextNode('ステータスコード')),
					$b->dd(new HtmlGlobalAttributes(), $b->code(
						new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
						new TextNode(Text::toString($status->value))
					)),
					$b->dt(new HtmlGlobalAttributes(), new TextNode('エラーコード')),
					$b->dd(new HtmlGlobalAttributes(), $b->code(
						new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
						new TextNode($errorNumber)
					)),
					$b->dt(new HtmlGlobalAttributes(), new TextNode('メッセージ')),
					$b->dd(new HtmlGlobalAttributes(), new TextNode(Text::isNullOrEmpty($message) ? 'なし' : $message)),
				])),
			])
		), $this->context);
	}
}
