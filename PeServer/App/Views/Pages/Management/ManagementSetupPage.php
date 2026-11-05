<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementSetupPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		return $this->layout('セットアップ', new Fragment([
			$b->p(new HtmlGlobalAttributes(), new Fragment([
				new TextNode('セットアップユーザーから管理者を生成するための情報を入力。'),
				$b->br(new HTMLBrAttributes()),
				new TextNode('管理者作成が完了した時点で元セットアップユーザーは無効化される。'),
			])),
			$b->p(new HtmlGlobalAttributes(), new Fragment([
				new TextNode('このセットアップ処理は管理者がセットアップユーザーとして処理している想定のため検証を甘くしている。'),
				$b->br(new HTMLBrAttributes()),
				new TextNode('そのため、ここで設定したユーザー情報でも設定画面で無編集保存ができない可能性あり。'),
			])),
			$this->form('/management/setup', $b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$this->input('login id', 'setting_setup_login_id', InputHelperType::Text),
				$this->input('password', 'setting_setup_password', InputHelperType::Text),
				$this->input('user name', 'setting_setup_user_name', InputHelperType::Text),
				$this->input('mail address', 'setting_setup_email', InputHelperType::Email),
				$this->input('web site', 'setting_setup_website', InputHelperType::Url),
				$this->submit('submit'),
			])), 'page-setting-setup', csrf: false),
		]));
	}
}
