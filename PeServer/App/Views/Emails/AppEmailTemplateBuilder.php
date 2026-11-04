<?php

declare(strict_types=1);

namespace PeServer\App\Views\Emails;

use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\CssSourceNode;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Text\TextBuilder;
use PeServer\Core\Mvc\Template\Node\Text\TextNodeOptions;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Throws\InvalidOperationException;

class AppEmailTemplateBuilder
{
	private const EMAIL_CSS = <<<'CSS'
h1 {
	font-size: 14pt;
	text-align: center;
}

*.header {
	list-style: none;
	text-align: center;
	padding: 0;
}

*.footer {
	font-size: 8pt;
	list-style: none;
	padding: 0;
	text-align: center;
}
*.footer li {
	display: inline;
}

*.content {
	margin: 3em auto 4em auto;
	width: 65%;
}

*.main {
	font-size: 14pt;
	border: 2px solid #cccccc;
	background: #eeeeee;
	padding: 8pt;
}
CSS;

	/**
	 * @param array<string,mixed> $values
	 */
	public function build(string $templateName, string $subject, array $values): string
	{
		return match ($templateName) {
			'mail_signup_step1' => $this->buildSignupStep1($subject, $values),
			'mail_signup_step2' => $this->buildSignupStep2($subject, $values),
			'password_reminder_token' => $this->buildPasswordReminder($subject, $values),
			'change_email_token' => $this->buildChangeEmailToken($subject, $values),
			'change_email_old' => $this->buildChangeEmailOld($subject, $values),
			'change_email_new' => $this->buildChangeEmailNew($subject, $values),
			'feedback_email' => $this->buildFeedback($values),
			'crash_report_email' => $this->buildCrashReport($values),
			default => throw new InvalidOperationException("Unsupported email template: $templateName"),
		};
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildSignupStep1(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();
		$url = $this->getNonEmptyString($values, 'url');

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(new HtmlGlobalAttributes(), new TextNode('ユーザー登録用URLを発行しました。')),
				$builder->div(
					new HtmlGlobalAttributes(className: 'main', style: 'text-align:center'),
					$builder->a(new HTMLAnchorAttributes(href: $url), new TextNode('本登録ページへ'))
				),
				new TextNode('URL: '),
				$builder->code(new HtmlGlobalAttributes(), new TextNode($url)),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildSignupStep2(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(
					new HtmlGlobalAttributes(),
					new TextNode($this->getString($values, 'name') . ' さんのユーザー登録が完了しました。')
				),
				$builder->div(
					new HtmlGlobalAttributes(className: 'main', style: 'text-align:center'),
					new Fragment([
						new TextNode('ログインID: '),
						$builder->code(new HtmlGlobalAttributes(), new TextNode($this->getString($values, 'login_id'))),
					])
				),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildPasswordReminder(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();
		$url = $this->getNonEmptyString($values, 'url');

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(
					new HtmlGlobalAttributes(),
					new Fragment([
						new TextNode('ログインID: '),
						$builder->code(new HtmlGlobalAttributes(), new TextNode($this->getString($values, 'login_id'))),
						new TextNode(' 宛のパスワード再発行通知です。'),
					])
				),
				$builder->div(
					new HtmlGlobalAttributes(className: 'main', style: 'text-align:center'),
					$builder->a(new HTMLAnchorAttributes(href: $url), new TextNode('再発行ページへ'))
				),
				new TextNode('URL: '),
				$builder->code(new HtmlGlobalAttributes(), new TextNode($url)),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildChangeEmailToken(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();
		$contactUrl = $this->getAppNonEmptyString($values, 'contact_url');

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(
					new HtmlGlobalAttributes(),
					new Fragment([
						new TextNode($this->getString($values, 'name') . ' さん宛のメールアドレス変更確認になります。'),
						$builder->br(new \PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes()),
						new TextNode('以下のトークンを使用してメールアドレス変更を完了してください。'),
					])
				),
				$builder->div(
					new HtmlGlobalAttributes(className: 'main', style: 'text-align:center'),
					$builder->code(new HtmlGlobalAttributes(), new TextNode($this->getString($values, 'token')))
				),
				$builder->p(new HtmlGlobalAttributes(), new TextNode('トークン期限が切れた後でもトークン発行は何度でも行えます。')),
				$builder->p(
					new HtmlGlobalAttributes(),
					new Fragment([
						new TextNode('本メールはユーザー起因で送信されています。'),
						$builder->br(new \PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes()),
						new TextNode('心当たりがない場合は問い合わせまでご連絡ください。'),
					])
				),
				$this->contactParagraph($builder, $contactUrl),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildChangeEmailOld(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();
		$contactUrl = $this->getAppNonEmptyString($values, 'contact_url');

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(
					new HtmlGlobalAttributes(),
					new Fragment([
						new TextNode($this->getString($values, 'name') . ' さん宛のメールアドレス変更完了通知になります。'),
						$builder->br(new \PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes()),
						new TextNode('本メールをもってこのメールアドレスにメール配信されることはありません。'),
					])
				),
				$builder->div(
					new HtmlGlobalAttributes(className: 'main', style: 'text-align:center'),
					new Fragment([
						new TextNode('変更先アドレス'),
						$builder->br(new \PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes()),
						$builder->code(new HtmlGlobalAttributes(), new TextNode($this->getString($values, 'new_email'))),
					])
				),
				$builder->p(
					new HtmlGlobalAttributes(style: 'color:#ff0000;font-weight:bold;'),
					new TextNode('このメールアドレス変更が自分の意志で行われていない場合問い合わせをお願いします。')
				),
				$this->contactParagraph($builder, $contactUrl),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildChangeEmailNew(string $subject, array $values): string
	{
		$builder = $this->createHtmlBuilder();
		$contactUrl = $this->getAppNonEmptyString($values, 'contact_url');

		return $this->buildHtml(
			$builder,
			$subject,
			new Fragment([
				$builder->p(
					new HtmlGlobalAttributes(),
					new Fragment([
						new TextNode($this->getString($values, 'name') . ' さん宛のメールアドレス変更完了通知になります。'),
						$builder->br(new \PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes()),
						new TextNode('今後はこのメールアドレスにメール配信が行われます。'),
					])
				),
				$builder->p(
					new HtmlGlobalAttributes(style: 'color:#ff0000;font-weight:bold;'),
					new TextNode('このメールアドレス変更が自分の意志で行われていない場合問い合わせをお願いします。')
				),
				$this->contactParagraph($builder, $contactUrl),
			]),
			$values
		);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildFeedback(array $values): string
	{
		$lines = [
			'バージョン: ' . $this->getString($values, 'version'),
			'リビジョン: ' . $this->getString($values, 'revision'),
			'ビルド: ' . $this->getString($values, 'build'),
			'----------------------',
			'件名:' . $this->getString($values, 'subject'),
			'件名:' . $this->getString($values, 'kind'),
			'内容:',
			$this->getString($values, 'content'),
		];

		return $this->buildPlainText(implode(PHP_EOL, $lines) . PHP_EOL);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildCrashReport(array $values): string
	{
		$lines = [];
		$comment = $values['comment'] ?? '';
		if (!is_string($comment)) {
			throw new InvalidOperationException('Invalid email template value: comment');
		}
		if (!empty($comment)) {
			$lines = [
				'コメント:',
				$comment,
				'----------------------',
			];
		}
		$lines = [
			...$lines,
			'バージョン: ' . $this->getString($values, 'version'),
			'リビジョン: ' . $this->getString($values, 'revision'),
			'ビルド: ' . $this->getString($values, 'build'),
			'----------------------',
			'例外:',
			$this->getString($values, 'exception'),
		];

		return $this->buildPlainText(implode(PHP_EOL, $lines) . PHP_EOL);
	}

	private function createHtmlBuilder(): HtmlBuilder
	{
		return new HtmlBuilder(HtmlBuilder::HTML_VERSION_EMAIL);
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function buildHtml(HtmlBuilder $builder, string $subject, INode $body, array $values): string
	{
		$app = $this->getArray($values, 'app');
		$footerLinks = [
			'Pe.Server' => $this->getNonEmptyString($app, 'server_url'),
			'問い合わせ' => $this->getNonEmptyString($app, 'contact_url'),
			'Pe.Server プロジェクト' => $this->getNonEmptyString($app, 'app_project_url'),
			'Pe プロジェクト' => $this->getNonEmptyString($app, 'server_project_url'),
			'フォーラム' => $this->getNonEmptyString($app, 'forum_url'),
			'Webサイト' => $this->getNonEmptyString($app, 'website_url'),
		];
		$footerItems = [];
		foreach ($footerLinks as $label => $href) {
			if (count($footerItems) > 0) {
				$footerItems[] = new TextNode(' ');
			}
			$footerItems[] = $builder->li(
				new HTMLLiAttributes(),
				$builder->a(new HTMLAnchorAttributes(href: $href), new TextNode($label))
			);
		}

		return $builder->build(
			$builder->html(
				new HTMLHtmlAttributes(lang: Bcp47::Japanese),
				new Fragment([
					$builder->head(
						new HtmlGlobalAttributes(),
						new Fragment([
							$builder->meta(new HTMLMetaAttributes(
								httpEquiv: 'Content-Type',
								content: 'text/html; charset=utf-8'
							)),
							$builder->style(new HTMLStyleAttributes(), new CssSourceNode(self::EMAIL_CSS)),
							$builder->title(new HtmlGlobalAttributes(), new TextNode($subject . ' - メール')),
						])
					),
					$builder->body(
						new HtmlGlobalAttributes(),
						new Fragment([
							$builder->h1(
								new HtmlGlobalAttributes(),
								new TextNode($subject)
							),
							$builder->p(
								new HtmlGlobalAttributes(className: 'header'),
								new TextNode('自動送信のため返信しないでください')
							),
							$builder->div(
								new HtmlGlobalAttributes(className: 'content'),
								$body
							),
							$builder->ul(
								new HtmlGlobalAttributes(className: 'footer'),
								new Fragment($footerItems)
							),
						])
					),
				])
			)
		);
	}

	/**
	 * @param non-empty-string $contactUrl
	 */
	private function contactParagraph(HtmlBuilder $builder, string $contactUrl): INode
	{
		return $builder->p(
			new HtmlGlobalAttributes(),
			new Fragment([
				new TextNode('問い合わせ: '),
				$builder->a(
					new HTMLAnchorAttributes(href: $contactUrl),
					new TextNode($contactUrl)
				),
			])
		);
	}

	private function buildPlainText(string $content): string
	{
		$builder = new TextBuilder(new TextNodeOptions(PHP_EOL));
		return $builder->build($builder->text($content));
	}

	/**
	 * @param array<string,mixed> $values
	 */
	private function getString(array $values, string $key): string
	{
		$value = $values[$key] ?? null;
		if (!is_string($value)) {
			throw new InvalidOperationException("Missing or invalid email template value: $key");
		}

		return $value;
	}

	/**
	 * @param array<string,mixed> $values
	 * @return non-empty-string
	 */
	private function getNonEmptyString(array $values, string $key): string
	{
		$value = $this->getString($values, $key);
		if ($value === '') {
			throw new InvalidOperationException("Missing or invalid email template value: $key");
		}

		return $value;
	}

	/**
	 * @param array<string,mixed> $values
	 * @return non-empty-string
	 */
	private function getAppNonEmptyString(array $values, string $key): string
	{
		return $this->getNonEmptyString($this->getArray($values, 'app'), $key);
	}

	/**
	 * @param array<string,mixed> $values
	 * @return array<string,mixed>
	 */
	private function getArray(array $values, string $key): array
	{
		$value = $values[$key] ?? null;
		if (!is_array($value)) {
			throw new InvalidOperationException("Missing or invalid email template value: $key");
		}

		return $value;
	}
}
