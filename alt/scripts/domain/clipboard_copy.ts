import * as dom from '../core/dom'
import * as clipboard from '../core/clipboard'

function createClipboardBaseElement(): HTMLSpanElement {
	const baseElement = document.createElement('span');

	baseElement.classList.add('pg-clipboard-base')

	return baseElement;
}

function existsClipboardBaseElement(element: HTMLElement): boolean {
	const baseElement = element.querySelector('.pg-clipboard-base');
	return baseElement != null;
}

function onMouseOverInlineElement(event: MouseEvent) {
	const element = <HTMLElement>event.currentTarget;
	if (existsClipboardBaseElement(element)) {
		return;
	}

	const baseElement = createClipboardBaseElement();

	const copyButtonElement = document.createElement('button');
	copyButtonElement.classList.add('pg-clipboard-copy')
	copyButtonElement.textContent = '📋';
	copyButtonElement.setAttribute('title', 'コピー');
	copyButtonElement.addEventListener('click', _ => {
		// 実装設計の問題だけど先に消しとかないとテキストが💩
		baseElement.remove();

		clipboard.copyText(element.textContent ?? '');

		const newBaseElement = createClipboardBaseElement();
		newBaseElement.textContent = '✔';
		newBaseElement.classList.add('pg-clipboard-ok');
		element.appendChild(newBaseElement);
	}, false);

	baseElement.appendChild(copyButtonElement);

	element.appendChild(baseElement);
}

function onMouseleaveInlineElement(event: MouseEvent) {
	const element = <HTMLElement>event.currentTarget;

	const baseElement = element.querySelector('.pg-clipboard-base')!;
	baseElement.remove();
}

function onMouseOverBlockElement(event: MouseEvent) {
	const element = <HTMLElement>event.currentTarget;
	if (existsClipboardBaseElement(element)) {
		return;
	}

	const baseElement = createClipboardBaseElement();

	const copyButtonElement = document.createElement('button');
	copyButtonElement.classList.add('pg-clipboard-copy')
	copyButtonElement.textContent = '📋';
	copyButtonElement.setAttribute('title', 'コピー');
	copyButtonElement.addEventListener('click', _ => {
		// 実装設計の問題だけど先に消しとかないとテキストが💩
		baseElement.remove();

		clipboard.copyText(element.textContent ?? '');

		const newBaseElement = createClipboardBaseElement();
		newBaseElement.textContent = '✔';
		newBaseElement.classList.add('pg-clipboard-ok');
		element.appendChild(newBaseElement);
	}, false);

	baseElement.appendChild(copyButtonElement);

	element.appendChild(baseElement);
}

function onMouseleaveBlockElement(event: MouseEvent) {
	const element = <HTMLElement>event.currentTarget;

	const baseElement = element.querySelector('.pg-clipboard-base')!;
	baseElement.remove();
}

function onClickDataElement(event: MouseEvent) {
	event.preventDefault();

	const element = <HTMLElement>event.currentTarget;
	if (existsClipboardBaseElement(element)) {
		return;
	}

	const value = dom.getDataset(element, 'clipboard-value');
	clipboard.copyText(value);
}

function registerInline(element: HTMLElement) {
	element.addEventListener('mouseover', onMouseOverInlineElement, false);
	element.addEventListener('mouseleave', onMouseleaveInlineElement, false);
}

function registerBlock(element: HTMLElement) {
	element.addEventListener('mouseover', onMouseOverBlockElement, false);
	element.addEventListener('mouseleave', onMouseleaveBlockElement, false);
}

function registerData(element: HTMLElement) {
	element.addEventListener('click', onClickDataElement, false);
}

function registerMarkdown(element: HTMLElement) {
	const inlineCopyElements = element.querySelectorAll<HTMLElement>(':not(pre) > code');
	const blockCopyElements = element.querySelectorAll<HTMLElement>('pre');

	for (const inlineCopyElement of inlineCopyElements) {
		inlineCopyElement.dataset['clipboard'] = 'inline';
		registerInline(inlineCopyElement);
	}
	for (const blockCopyElement of blockCopyElements) {
		blockCopyElement.dataset['clipboard'] = 'block';
		registerBlock(blockCopyElement);
	}
}

function register() {
	const inlineCopyElements = document.querySelectorAll<HTMLElement>('[data-clipboard="inline"]');
	for (const element of inlineCopyElements) {
		registerInline(element);
	}

	const blockCopyElements = document.querySelectorAll<HTMLElement>('[data-clipboard="block"]');
	for (const element of blockCopyElements) {
		registerBlock(element);
	}

	const dataCopyElements = document.querySelectorAll<HTMLElement>('[data-clipboard="data"][data-clipboard-value]');
	for (const element of dataCopyElements) {
		registerData(element);
	}

	const markdownElements = document.querySelectorAll<HTMLElement>('section.markdown');
	for (const markdownElement of markdownElements) {
		registerMarkdown(markdownElement);
	}

}

export function boot() {
	register();
}
