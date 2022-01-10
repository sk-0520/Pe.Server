// まぁまぁ適当さ加減がやばい
import * as ajax from '../common/ajax'
import * as dom from '../common/dom'
import * as logging from '../common/logging'
const logger = logging.create('markdown');

function register(selector: string) {
	const elements = document.querySelectorAll<HTMLTextAreaElement>(selector);
	for (const element of elements) {
		const resultSelector = dom.getDataset(element, 'markdown-result');
		if (!resultSelector) {
			logger.warn('markdown-result');
			continue;
		}
		const resultElement = document.querySelector(resultSelector) as HTMLElement;
		if (!resultElement) {
			logger.warn('element: ' + resultSelector);
			continue;
		}

		element.addEventListener('change', $ev => {
			applyAsync(element, resultElement);
		});
	}
}

async function applyAsync(sourceElement: HTMLTextAreaElement, targetElement: HTMLElement) {
	const response = await fetch('/ajax/markdown', {
		'method': 'POST',
		'headers': {
			'Content-Type': 'application/json',
		},
		'body': JSON.stringify({
			'level': 'user',
			'source': sourceElement.value,
		}),
	});

	const json = await response.json();
	const result = json as ajax.AjaxResult<{
		markdown: string
	}>;

	logger.debug(result);
	if (!result.error) {
		const markdownHtml = result.data['markdown'];
		targetElement.innerHTML = markdownHtml;
	}
}

export function boot(selector: string) {
	register(selector);
}
