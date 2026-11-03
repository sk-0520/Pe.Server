<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class AccountUserEditPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$parameter = $this->props->parameter;

		return new DefaultPage(
			new DefaultPageProps(
				parameter: $parameter,
				title: 'ユーザー情報 編集',
				defaultScriptPath: '/scripts/user_edit.js',
				children: $this->context->builder->form(
					new HTMLFormAttributes(
						action: '/account/user/edit2',
						method: FormMethod::Post,
						className: 'page-account-user'
					),
					new Fragment([
						new Csrf(new CsrfProps(), $this->context),
						$this->context->builder->dl(
							new HtmlGlobalAttributes(className: 'input'),
							new Fragment([
								$this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode('名前')),
								$this->context->builder->dd(
									new HtmlGlobalAttributes(),
									new InputHelper(new InputHelperProps(
										parameter: $parameter,
										key: 'account_edit_name',
										type: InputHelperType::Text,
										className: 'edit',
										required: true,
									), $this->context)
								),
								$this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode('Webサイト')),
								$this->context->builder->dd(
									new HtmlGlobalAttributes(),
									new InputHelper(new InputHelperProps(
										parameter: $parameter,
										key: 'account_edit_website',
										type: InputHelperType::Url,
										className: 'edit',
									), $this->context)
								),
								$this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode('説明')),
								$this->context->builder->dd(
									new HtmlGlobalAttributes(),
									$this->context->builder->div(
										new HtmlGlobalAttributes(className: 'tab'),
										new Fragment([
											$this->context->builder->inputRadio(new HTMLInputRadioAttributes(
												id: 'description_markdown_source',
												name: 'tab_markdown',
												className: 'tab_check',
												checked: true,
											)),
											$this->context->builder->label(
												new HTMLLabelAttributes(htmlFor: 'description_markdown_source', className: 'tab_header'),
												new TextNode('Markdown')
											),
											$this->context->builder->div(
												new HtmlGlobalAttributes(className: 'tab_content'),
												new InputHelper(new InputHelperProps(
													parameter: $parameter,
													key: 'account_edit_description',
													type: InputHelperType::Textarea,
													className: 'edit markdown-editor',
													data: new DataAttributes(['markdown-result' => '.markdown-browser']),
												), $this->context)
											),
											$this->context->builder->inputRadio(new HTMLInputRadioAttributes(
												id: 'description_markdown_preview',
												name: 'tab_markdown',
												className: 'tab_check',
											)),
											$this->context->builder->label(
												new HTMLLabelAttributes(htmlFor: 'description_markdown_preview', className: 'tab_header'),
												new TextNode('プレビュー')
											),
											$this->context->builder->div(
												new HtmlGlobalAttributes(className: 'tab_content'),
												new Markdown(new MarkdownProps(
													content: Text::toString($parameter->values['account_edit_description'] ?? Text::EMPTY),
													className: 'markdown markdown-browser',
												), $this->context)
											),
										])
									)
								),
								$this->context->builder->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get()),
								$this->context->builder->dd(
									new HtmlGlobalAttributes(className: 'action'),
									$this->context->builder->button(new HTMLButtonAttributes(), new TextNode('登録'))
								),
							])
						),
					])
				)
			),
			$this->context
		);
	}
}
