//import * as dom from '../core/dom'
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

function registerInline(element: HTMLElement) {
	element.addEventListener('mouseover', onMouseOverInlineElement, false);
	element.addEventListener('mouseleave', onMouseleaveInlineElement, false);
}

function registerBlock(element: HTMLElement) {

}

function register() {
	const inlineCopyElements = document.querySelectorAll<HTMLElement>('[data-clipboard="inline"]');
	for (const inlineCopyElement of inlineCopyElements) {
		registerInline(inlineCopyElement);
	}

	const blockCopyElements = document.querySelectorAll<HTMLElement>('.block-copy');
	for (const blockCopyElement of blockCopyElements) {
		registerBlock(blockCopyElement);
	}
}

export function boot() {
	register();
}
