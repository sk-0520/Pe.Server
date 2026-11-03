<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\App\Models\Data\Dto\CrashReportDetailDto;
use PeServer\App\Models\Data\Dto\FeedbackDetailDto;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\Code;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\CodeProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
abstract class ManagementReportDetailPageBase extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$v = $this->props->parameter->values;
		/** @var FeedbackDetailDto|CrashReportDetailDto $detail */
		$detail = $v['detail'];
		$isCrash = $detail instanceof CrashReportDetailDto;
		$kind = $isCrash ? 'crash-report' : 'feedback';
		$fields = [
			$this->field('シーケンス', (string)$detail->sequence),
			$this->field('タイムスタンプ', $this->timestamp($detail->timestamp)),
			$this->field('IPアドレス', $detail->ipAddress),
			$this->field('バージョン', $detail->version),
			$this->field('リビジョン', $detail->revision),
			$this->field('ビルド', $detail->build),
			$this->field('ユーザーID', $b->code(new HtmlGlobalAttributes(), new TextNode($detail->userId))),
		];
		if ($detail instanceof CrashReportDetailDto) {
			$fields[] = $this->field('例外', new Code(new CodeProps($detail->exception, 'plaintext'), $this->context));
			$fields[] = $this->field('メールアドレス', $v['email']
				? $b->code(new HtmlGlobalAttributes(), new TextNode($v['email']))
				: $b->span(new HtmlGlobalAttributes(className: 'mute'), new TextNode('なし')));
			$fields[] = $this->field('コメント', $detail->comment
				? new Code(new CodeProps($detail->comment, 'plaintext'), $this->context)
				: $b->span(new HtmlGlobalAttributes(className: 'mute'), new TextNode('なし')));
			$fields[] = $this->field('レポート', $b->a(new HTMLAnchorAttributes(
				download: 'crash-report-' . $detail->sequence . '.json',
				href: 'data:application/json;base64,' . $v['report'],
			), new TextNode('ダウンロード')));
		} else {
			foreach (
                [
				'初回実行日時' => $detail->firstExecuteTimestamp,
				'初回実行バージョン' => $detail->firstExecuteVersion,
				'プロセス(実行CPU)' => $detail->process,
				'プラットフォーム(OS CPU)' => $detail->platform,
				'OS' => $detail->os, 'CLR' => $detail->clr,
				'種別' => $detail->kind, '件名' => $detail->subject,
                ] as $label => $value
			) {
				$fields[] = $this->field($label, $value);
			}
			$fields[] = $this->field('コメント', new Markdown(new MarkdownProps($detail->content), $this->context));
		}
		$copies = [];
		foreach (['developer_title' => '件名', 'developer_body' => '本文'] as $key => $label) {
			$copies[] = $b->li(new HTMLLiAttributes(title: $v[$key]), $b->button(new HTMLButtonAttributes(
				data: new DataAttributes(['clipboard' => 'data', 'clipboard-value' => $v[$key]]),
			), new TextNode($label)));
		}
		/** @var list<ReportStatus> $statuses */
		$statuses = $v['report_status'];
		$radios = [];
		foreach ($statuses as $status) {
			$radios[] = $b->label(new HTMLLabelAttributes(), new Fragment([
				$b->inputRadio(new HTMLInputRadioAttributes(name: 'developer-status', value: $status->value, checked: $status === $detail->developerStatus)),
				new TextNode(' ' . ReportStatus::toString($status)),
			]));
			$radios[] = new TextNode(' ');
		}
		return $this->layout(($isCrash ? 'クラッシュレポート: ' : 'フィードバック: ') . $detail->sequence, new Fragment([
			$b->dl(new HtmlGlobalAttributes(), new Fragment($fields)),
			$b->section(new HtmlGlobalAttributes(), $this->form('/management/' . $kind . '/' . $detail->sequence, new Fragment([
				$b->h2(new HtmlGlobalAttributes(), new TextNode('開発用')),
				$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
					$this->field('コピー', $b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment($copies))),
					$this->field('状態', new Fragment($radios)),
					$this->input('メモ', 'developer-comment', InputHelperType::Textarea),
					$this->submit('保存', '実行'),
				])),
			]))),
		]));
	}
}
