import {EditorView, basicSetup} from "codemirror"
import {javascript} from "@codemirror/lang-javascript";
import * as dom from '../core/dom';

function attachTextArea(element: HTMLTextAreaElement): void {
	let view = new EditorView({
		extensions: [basicSetup, javascript()],
		parent: document.body
	  });
	console.assert(view);
}

export function boot() {
	const elements = dom.requireSelectorAll<HTMLTextAreaElement>('textarea.editor');
	for (const element of elements) {
		attachTextArea(element);
	}
}
