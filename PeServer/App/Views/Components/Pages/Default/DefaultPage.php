<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Pages\Default;

use Override;
use PeServer\App\Views\Components\Asset\Asset;
use PeServer\App\Views\Components\Asset\AssetProps;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Mvc\Template\Node\Fragment;
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
		return $this->context->builder->html(
			new HTMLHtmlAttributes(),
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
						$this->context->builder->main(
							new HtmlGlobalAttributes(
								id: "main"
							),
							new Fragment([
								$this->context->builder->h1(
									new HtmlGlobalAttributes(),
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
													fn($a) => $this->context->builder->li(
														new HTMLLiAttributes(),
														new TextNode($a)
													)
												)
											)
										)
									)
									: null,

							])
						),
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
