<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\TemplateRenderer;

/**
 * @template TRootProps of \PeServer\Core\Mvc\Template\Node\RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class IndexPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#region RootHtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		return new DefaultPage(
			new DefaultPageProps(
				parameter: $this->props->parameter,
				title: 'トップ',
				children: new Fragment([
					$this->context->builder->p(
						new HtmlGlobalAttributes(),
						new TextNode('Pe のサーバーが必要な処理。')
					),
					$this->context->builder->ul(
						new HtmlGlobalAttributes(),
						new Fragment([
							$this->context->builder->li(
								new HTMLLiAttributes(),
								$this->context->builder->a(
									new HTMLAnchorAttributes(href: '/plugin'),
									new TextNode('プラグイン')
								)
							),
							$this->context->builder->li(
								new HTMLLiAttributes(),
								$this->context->builder->a(
									new HTMLAnchorAttributes(href: '/api-doc'),
									new TextNode('API')
								)
							),
							$this->context->builder->li(
								new HTMLLiAttributes(),
								new Fragment([
									new TextNode('開発ドキュメント'),
									$this->context->builder->ul(
										new HtmlGlobalAttributes(),
										new Fragment([
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(href: '/public/api-doc/'),
													new TextNode('Doc: PeServer')
												)
											),
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(href: '/public/coverage/php/uit/'),
													new TextNode('Code Coverage: PeServer')
												)
											),
											$this->context->builder->li(
												new HTMLLiAttributes(),
												$this->context->builder->a(
													new HTMLAnchorAttributes(href: '/public/coverage/script/lcov-report/'),
													new TextNode('Code Coverage: Script')
												)
											)
										])
									)
								])
							),
							$this->context->builder->li(
								new HTMLLiAttributes(),
								new Fragment([
									new TextNode('ビルド状況'),
									$this->context->builder->table(
										new HtmlGlobalAttributes(),
										$this->context->builder->tbody(
											new HtmlGlobalAttributes(),
											new Fragment([
												$this->context->builder->tr(
													new HtmlGlobalAttributes(),
													new Fragment([
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(rowSpan: 2),
															new TextNode('Pe')
														),
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(),
															new TextNode('Current')
														),
														$this->context->builder->td(
															new HTMLTableCellAttributes(),
															$this->context->builder->a(
																new HTMLAnchorAttributes(href: 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml'),
																$this->context->builder->img(
																	new HTMLImgAttributes(
																		className: 'page-user-index-ci ' . $this->context->environment->get(),
																		src: $this->context->environment->isProduction()
																			? 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml/badge.svg'
																			: null
																	)
																)
															)
														)
													])
												),
												$this->context->builder->tr(
													new HtmlGlobalAttributes(),
													new Fragment([
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(),
															new TextNode('Master')
														),
														$this->context->builder->td(
															new HTMLTableCellAttributes(),
															$this->context->builder->a(
																new HTMLAnchorAttributes(href: 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml'),
																$this->context->builder->img(
																	new HTMLImgAttributes(
																		className: 'page-user-index-ci ' . $this->context->environment->get(),
																		src: $this->context->environment->isProduction()
																			? 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml/badge.svg?branch=master'
																			: null
																	)
																)
															)
														)
													])
												),
												$this->context->builder->tr(
													new HtmlGlobalAttributes(),
													new Fragment([
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(rowSpan: 2),
															new TextNode('Pe.Server')
														),
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(),
															new TextNode('Current')
														),
														$this->context->builder->td(
															new HTMLTableCellAttributes(),
															$this->context->builder->a(
																new HTMLAnchorAttributes(href: 'https://github.com/sk-0520/Pe.Server/actions'),
																$this->context->builder->img(
																	new HTMLImgAttributes(
																		className: 'page-user-index-ci ' . $this->context->environment->get(),
																		src: $this->context->environment->isProduction()
																			? 'https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg'
																			: null
																	)
																)
															)
														)
													])
												),
												$this->context->builder->tr(
													new HtmlGlobalAttributes(),
													new Fragment([
														$this->context->builder->th(
															new HTMLTableHeaderCellAttributes(),
															new TextNode('Master')
														),
														$this->context->builder->td(
															new HTMLTableCellAttributes(),
															$this->context->builder->a(
																new HTMLAnchorAttributes(href: 'https://github.com/sk-0520/Pe.Server/actions?query=branch%3Amaster'),
																$this->context->builder->img(
																	new HTMLImgAttributes(
																		className: 'page-user-index-ci ' . $this->context->environment->get(),
																		src: $this->context->environment->isProduction()
																			? 'https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg?branch=master'
																			: null
																	)
																)
															)
														)
													])
												)
											])
										)
									)
								])
							),
							$this->context->builder->li(
								new HTMLLiAttributes(),
								$this->context->builder->a(
									new HTMLAnchorAttributes(href: '/tool'),
									new TextNode('ツールとか')
								)
							)
						])
					),
					$this->context->environment->isDevelopment()
						? new Fragment([
							$this->context->builder->h2(
								new HtmlGlobalAttributes(),
								new TextNode('dev')
							),
							$this->context->builder->ul(
								new HtmlGlobalAttributes(),
								new Fragment([
									$this->context->builder->li(
										new HTMLLiAttributes(),
										new Fragment([
											$this->context->builder->a(
												new HTMLAnchorAttributes(href: '/dev/exception'),
												new TextNode('exception')
											),
											$this->context->builder->ul(
												new HtmlGlobalAttributes(),
												$this->context->builder->li(
													new HTMLLiAttributes(),
													$this->context->builder->a(
														new HTMLAnchorAttributes(href: '/ajax/dev/exception/json'),
														new TextNode('json')
													)
												)
											)
										])
									),
									$this->context->builder->li(
										new HTMLLiAttributes(),
										$this->context->builder->a(
											new HTMLAnchorAttributes(href: '/dev/streaming'),
											new TextNode('streaming')
										)
									)
								])
							)
						])
						: null
				])
			),
			$this->context
		);
	}

	#endregion
}
