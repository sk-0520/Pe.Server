<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Pages\Default;

use Override;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Mvc\Template\Node\Html\Components\Asset\Asset;
use PeServer\Core\Mvc\Template\Node\Html\Components\Asset\AssetProps;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CharSet;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\Comment;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\Html\JavaScriptSourceNode;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\Pages\Default\DefaultPageProps>
 */
class DefaultPage extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\App\Views\Components\Pages\Default\DefaultPageProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		/** @var SessionAccount|null $account */
		$account = $this->context->stores->session->getOr('account', null);

		return $this->context->builder->html(
			new HTMLHtmlAttributes(
				lang: $this->props->lang
			),
			new Fragment([
				$this->context->builder->head(
					new HtmlGlobalAttributes(),
					new Fragment([
						$this->context->builder->meta(new HTMLMetaAttributes(
							charset: CharSet::Utf8,
						)),
						$this->context->builder->meta(new HTMLMetaAttributes(
							name: 'viewport',
							content: 'width=device-width, initial-scale=1.0',
						)),
						$this->context->builder->title(
							new HtmlGlobalAttributes(),
							new TextNode(
								Text::isNullOrEmpty($this->props->title)
									? 'Peサーバー'
									: $this->props->title . " - Peサーバー"
							)
						),
						$this->props->customHeadNode,
						new Asset(
							new AssetProps(
								file: '/favicon.svg',
								rel: Rel::Icon,
							),
							$this->context
						),
						new Asset(
							new AssetProps(
								file: $this->props->defaultStylePath ?? "/styles/style.css",
							),
							$this->context
						),
						$this->props->customStyleCode,
					]),
				),
				$this->context->builder->body(
					new HtmlGlobalAttributes(),
					new Fragment([
						$this->context->builder->span(
							new HtmlGlobalAttributes(
								id: 'node-template-indicator',
								title: 'NodeTemplate',
								style: 'position: fixed; top: 0; right: 0; z-index: 9999; padding: 0.1em 0.4em; background: #222; color: #fff; pointer-events: none;',
							),
							new TextNode('2')
						),
						$this->context->builder->main(
							new HtmlGlobalAttributes(
								id: "main"
							),
							new Fragment([
								$this->context->builder->h1(
									new HtmlGlobalAttributes(
										id: "title"
									),
									new Fragment([
										new TextNode(
											// ページのタイトル(!=title)は原則必要なのでフォールバックは行わない
											$this->props->title ?? Text::EMPTY
										),
										$this->context->environment->isDevelopment()
											? $this->context->builder->span(
												new HtmlGlobalAttributes(
													className: "mute",
													style: "margin-left: 1em;",
												),
												new TextNode('(dev)')
											)
											: null
									]),
								),
								// temp_messages
								isset($this->props->parameter->values["temp_messages"]) && !Arr::isNullOrEmpty($this->props->parameter->values["temp_messages"])
									? $this->context->builder->div(
										new HtmlGlobalAttributes(
											className: new StringValues(["common", "messages"])
										),
										$this->context->builder->ul(
											new HtmlGlobalAttributes(),
											new Fragment(
												Arr::map(
													$this->props->parameter->values["temp_messages"],
													fn($value, $key) => $this->context->builder->li(
														new HTMLLiAttributes(
															data: new DataAttributes([
																$key => $value
															])
														),
														new TextNode($value)
													)
												)
											)
										)
									)
									: null,
								// show_error_messages
								new ShowErrorMessages(
									new ShowErrorMessagesProps(
										errors: $this->props->parameter->errors,
										key: Validator::COMMON,
									),
									$this->context
								),
								// {block name='BODY'}
								$this->context->builder->section(
									new HtmlGlobalAttributes(
										id: "content"
									),
									$this->props->children ?? NoneNode::get()
								)
							])
						),
						// nav
						$this->context->builder->nav(
							new HtmlGlobalAttributes(),
							$this->context->builder->details(
								new HTMLDetailsAttributes(),
								new Fragment([
									$this->context->builder->summary(
										new HtmlGlobalAttributes(),
										NoneNode::get()
									),
									$this->context->builder->div(
										new HtmlGlobalAttributes(
											className: "menu"
										),
										NoneNode::get()
									)
								])
							)
						),
						// header
						$this->context->builder->header(
							new HtmlGlobalAttributes(),
							$this->context->builder->ul(
								new HtmlGlobalAttributes(),
								new Fragment([
									$this->context->builder->li(
										new HTMLLiAttributes(),
										$this->context->builder->a(
											new HTMLAnchorAttributes(
												href: "/"
											),
											new TextNode("トップ")
										),
									),
									$account !== null
										? new Fragment([
											$account->level === UserLevel::SETUP
												? $this->context->builder->li(
													new HTMLLiAttributes(),
													$this->context->builder->a(
														new HTMLAnchorAttributes(
															href: "/management/setup"
														),
														new TextNode("セットアップ")
													)
												)
												: $this->context->builder->li(
													new HTMLLiAttributes(),
													$this->context->builder->a(
														new HTMLAnchorAttributes(
															href: "/account/user2"
														),
														new TextNode("ユーザー情報")
													)
												),
											$account->level === UserLevel::ADMINISTRATOR
												? $this->context->builder->li(
													new HTMLLiAttributes(),
													$this->context->builder->a(
														new HTMLAnchorAttributes(
															href: "/management"
														),
														new TextNode("管理")
													)
												)
												: null,
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(
														href: "/account/logout2"
													),
													new TextNode("ログアウト")
												)
											)
										])
										: new Fragment([
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(
														href: "/account/login2"
													),
													new TextNode("ログイン")
												)
											),
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(
														href: "/account/signup2"
													),
													new TextNode("ユーザー登録")
												)
											),
										])
								])
							)
						),
						// footer
						$this->context->builder->footer(
							new HtmlGlobalAttributes(),
							$this->context->builder->ul(
								new HtmlGlobalAttributes(),
								new Fragment([
									$this->context->builder->li(
										new HTMLLiAttributes(),
										$this->context->builder->a(
											new HTMLAnchorAttributes(
												href: "/"
											),
											new TextNode("トップページ")
										)
									),
									$this->context->builder->li(
										new HTMLLiAttributes(),
										$this->context->builder->a(
											new HTMLAnchorAttributes(
												href: "/about"
											),
											new TextNode("このサイトについて")
										)
									)
								])
							)
						),
						//---
						new Asset(
							new AssetProps(
								file: $this->props->defaultScriptPath ?? "/scripts/script.js",
							),
							$this->context
						),
						$this->props->customScriptCode,
						$this->context->environment->isProduction()
							? $this->context->builder->script(
								new HTMLScriptAttributes(),
								new JavaScriptSourceNode(
									<<< JS
									var _paq = window._paq = window._paq || [];
									/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
									_paq.push(['trackPageView']);
									_paq.push(['enableLinkTracking']);
									(function() {
									var u="//analytics.content-type-text.net/";
									_paq.push(['setTrackerUrl', u+'matomo.php']);
									_paq.push(['setSiteId', '4']);
									var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
									g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
									})();
									JS
								)
							)
							: null,
					]),
				)
			])
		);
	}
}
