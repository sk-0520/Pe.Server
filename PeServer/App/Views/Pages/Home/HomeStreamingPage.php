<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOutputAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\JavaScriptSourceNode;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @extends HomePageBase<RootProps>
 */
class HomeStreamingPage extends HomePageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$children = new Fragment([
			$b->p(new HtmlGlobalAttributes(), new Fragment([
				$b->button(new HTMLButtonAttributes(id: 'chunk_start', type: ButtonType::Button), new TextNode('chunk')),
				$b->output(new HTMLOutputAttributes(id: 'chunk_output'), new TextNode('')),
			])),
			$b->p(new HtmlGlobalAttributes(), new Fragment([
				$b->button(new HTMLButtonAttributes(id: 'sse_text_start', type: ButtonType::Button), new TextNode('sse/text')),
				$b->output(new HTMLOutputAttributes(id: 'sse_text_output'), new TextNode('')),
			])),
			$b->p(new HtmlGlobalAttributes(), new Fragment([
				$b->button(new HTMLButtonAttributes(id: 'sse_json_start', type: ButtonType::Button), new TextNode('sse/json')),
				$b->output(new HTMLOutputAttributes(id: 'sse_json_output'), new TextNode('')),
			])),
		]);

		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: '開発用: streaming',
			children: $children,
			customScriptCode: new JavaScriptSourceNode(<<<'JS'
window.addEventListener('load', () => {
	const fireElement = document.getElementById('chunk_start');
	const outputElement = document.getElementById('chunk_output');

	fireElement.addEventListener('click', async () => {
		outputElement.textContent = '';
		let bytes = 0;
		var decoder = new TextDecoder("utf-8");
		const response = await fetch('/ajax/dev/streaming_chunk');
		for await (const chunk of response.body) {
			outputElement.textContent += decoder.decode(Uint8Array.from(chunk).buffer);
		}
	}, false);
});

window.addEventListener('load', () => {
	const fireElement = document.getElementById('sse_text_start');
	const outputElement = document.getElementById('sse_text_output');

	fireElement.addEventListener('click', async () => {
		outputElement.textContent = '';
		const eventSource = new EventSource("/ajax/dev/streaming_sse/text");
		eventSource.onmessage = (event) => {
			console.log(event);
			if(event.data === "<DONE>") {
				eventSource.close();
				return;
			}
			outputElement.textContent += event.data;
		};
	}, false);
});

window.addEventListener('load', () => {
	const fireElement = document.getElementById('sse_json_start');
	const outputElement = document.getElementById('sse_json_output');

	fireElement.addEventListener('click', async () => {
		outputElement.textContent = '';
		const eventSource = new EventSource("/ajax/dev/streaming_sse/json");
		eventSource.onmessage = (event) => {
			console.log(event);
			if(event.data === "<DONE>") {
				eventSource.close();
				return;
			}
			const value = JSON.parse(event.data)
			outputElement.textContent += value.content;
		};
	}, false);
});
JS),
		), $this->context);
	}
}
