<?php

declare(strict_types=1);

namespace PeServerUT\App\Views\Emails;

use PeServer\App\Views\Emails\AppEmailTemplateBuilder;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\TestClass;

class AppEmailTemplateBuilderTest extends TestClass
{
	public function test_build_html_email_templates(): void
	{
		$builder = new AppEmailTemplateBuilder();
		$baseValues = [
			'app' => [
				'server_url' => 'https://example.com/',
				'contact_url' => 'https://example.com/contact?a=1&b=2',
				'app_project_url' => 'https://example.com/app',
				'server_project_url' => 'https://example.com/server',
				'forum_url' => 'https://example.com/forum',
				'website_url' => 'https://example.com/site',
			],
		];
		$templates = [
			[
				'mail_signup_step1',
				['url' => 'https://example.com/signup?a=1&b=2'],
				'本登録ページへ',
			],
			[
				'mail_signup_step2',
				['name' => '<User>', 'login_id' => 'user&1'],
				'&lt;User&gt; さんのユーザー登録が完了しました。',
			],
			[
				'password_reminder_token',
				['login_id' => 'user&1', 'url' => 'https://example.com/reset/token'],
				'再発行ページへ',
			],
			[
				'change_email_token',
				['name' => 'User', 'token' => 'token&1'],
				'token&amp;1',
			],
			[
				'change_email_old',
				['name' => 'User', 'new_email' => 'new@example.com'],
				'new@example.com',
			],
			[
				'change_email_new',
				['name' => 'User'],
				'今後はこのメールアドレスにメール配信が行われます。',
			],
		];

		foreach ($templates as [$templateName, $values, $expectedBody]) {
			$html = $builder->build($templateName, '件名', array_replace_recursive($baseValues, $values));

			$this->assertStringStartsWith('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">', $html, $templateName);
			$this->assertStringContainsString('<TITLE>件名 - メール</TITLE>', $html, $templateName);
			$this->assertStringContainsString('class="content"', $html, $templateName);
			$this->assertStringContainsString('<STYLE>', $html, $templateName);
			$this->assertDoesNotMatchRegularExpression('/<\/?[a-z]/', $html, $templateName);
			$this->assertStringContainsString($expectedBody, $html, $templateName);
			$this->assertStringContainsString('問い合わせ', $html, $templateName);
			$this->assertStringNotContainsString('{$values.', $html, $templateName);
		}
	}

	public function test_signup_step1_matches_original_email_structure(): void
	{
		$html = (new AppEmailTemplateBuilder())->build('mail_signup_step1', 'ユーザー登録用URL通知', [
			'url' => 'http://localhost/account/signup/example-token',
			'app' => [
				'server_url' => 'https://peserver.site/',
				'contact_url' => 'https://peserver.site/contact',
				'app_project_url' => 'https://github.com/sk-0520/Pe',
				'server_project_url' => 'https://github.com/sk-0520/Pe.Server',
				'forum_url' => 'https://groups.google.com/g/pe_development',
				'website_url' => 'https://content-type-text.net/',
			],
		]);

		$this->assertStringContainsString('<HTML lang="ja">', $html);
		$this->assertStringContainsString('<META content="text/html; charset=utf-8" http-equiv="Content-Type">', $html);
		$this->assertStringContainsString('<H1>ユーザー登録用URL通知</H1>', $html);
		$this->assertStringContainsString('<P class="header">自動送信のため返信しないでください</P>', $html);
		$this->assertStringNotContainsString('<UL class="header">自動送信のため返信しないでください</UL>', $html);
		$this->assertStringContainsString('<DIV class="content">', $html);
		$this->assertStringContainsString('<DIV class="main" style="text-align:center">', $html);
		$this->assertStringContainsString('http://localhost/account/signup/example-token', $html);
		$this->assertStringContainsString('<UL class="footer">', $html);
		$this->assertStringContainsString('https://groups.google.com/g/pe_development', $html);
		$this->assertStringNotContainsString('xmlns="http://www.w3.org/1999/xhtml"', $html);
	}

	public function test_build_feedback_plain_text_with_text_builder(): void
	{
		$html = (new AppEmailTemplateBuilder())->build('feedback_email', '件名', [
			'version' => '1.0',
			'revision' => 'abc',
			'build' => '42',
			'subject' => 'feedback subject',
			'kind' => 'question',
			'content' => "first\r\nsecond",
		]);

		$this->assertSame(
			implode(PHP_EOL, [
				'バージョン: 1.0',
				'リビジョン: abc',
				'ビルド: 42',
				'----------------------',
				'件名:feedback subject',
				'件名:question',
				'内容:',
				'first',
				'second',
				'',
			]),
			$html
		);
	}

	public function test_build_crash_report_plain_text_with_optional_comment(): void
	{
		$builder = new AppEmailTemplateBuilder();
		$values = [
			'version' => '1.0',
			'revision' => 'abc',
			'build' => '42',
			'exception' => "Error\r\nat example",
		];

		$withoutComment = $builder->build('crash_report_email', '件名', $values);
		$this->assertStringStartsWith('バージョン: 1.0' . PHP_EOL, $withoutComment);
		$this->assertStringContainsString('例外:' . PHP_EOL . 'Error' . PHP_EOL . 'at example', $withoutComment);
		$this->assertStringNotContainsString('コメント:', $withoutComment);

		$withComment = $builder->build('crash_report_email', '件名', [...$values, 'comment' => 'reported']);
		$this->assertStringStartsWith('コメント:' . PHP_EOL . 'reported' . PHP_EOL, $withComment);
	}

	public function test_build_throws_for_unknown_template(): void
	{
		$this->expectException(InvalidOperationException::class);
		(new AppEmailTemplateBuilder())->build('unknown', '件名', []);
	}
}
