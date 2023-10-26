import * as CodeMirror from 'codemirror';
import * as dom from '../core/dom';

function attachTextArea(element: HTMLTextAreaElement): void {
	CodeMirror.fromTextArea(element);
}

export function boot() {
	const elements = dom.requireSelectorAll<HTMLTextAreaElement>('textarea.editor');
	for (const element of elements) {
		attachTextArea(element);
	}
}
