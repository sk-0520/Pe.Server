import * as monaco from '../../../node_modules/monaco-editor/monaco';

function apply(element: HTMLElement) {
}

export function boot() {
	const editors = document.querySelectorAll<HTMLElement>('.editor');
	for(const editor of editors) {
		apply(editor);
	}
}
