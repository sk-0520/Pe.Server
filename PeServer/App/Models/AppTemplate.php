<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Views\Emails\AppEmailTemplateBuilder;

/**
 * アプリ用汎用テンプレート処理。
 */
final class AppTemplate
{
	public function __construct(
		private AppConfiguration $config,
	) {
		//NOP
	}

	#region function

	/**
	 * NodeTemplate によるメール用テンプレートの作成。
	 *
	 * HTMLメールとプレーンテキストメールの送信本文を生成する。
	 *
	 * @param string $templateName
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return string
	 */
	public function createNodeMailTemplate(string $templateName, string $subject, array $params): string
	{
		$params = $this->addMailAppValues($subject, $params);

		return (new AppEmailTemplateBuilder())->build($templateName, $subject, $params);
	}

	/**
	 * メールテンプレート共通のアプリ情報を設定。
	 *
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return array<string,mixed>
	 */
	private function addMailAppValues(string $subject, array $params): array
	{
		$families = $this->config->setting->config->address->families;

		$params['app'] = [
			'subject' => $subject,
			'server_url' => $families->serverUrl,
			'contact_url' => $families->contactUrl,
			'app_project_url' => $families->appProjectUrl,
			'server_project_url' => $families->serverProjectUrl,
			'forum_url' => $families->forumUrl,
			'website_url' => $families->websiteUrl,
		];

		return $params;
	}

	#endregion
}
