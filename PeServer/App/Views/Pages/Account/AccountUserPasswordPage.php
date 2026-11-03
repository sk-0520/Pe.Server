<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserPasswordPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		return $this->layout('パスワード変更', $this->form(
            '/account/user/password2',
            'page-account-password',
			$this->context->builder->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$this->input('現在パスワード', 'account_password_current', InputHelperType::Password, true),
				$this->input('新しいパスワード', 'account_password_new', InputHelperType::Password, true),
				$this->input('新しいパスワード(確認用)', 'account_password_confirm', InputHelperType::Password, true),
				$this->submit('登録'),
			]))
		));
	}
}
