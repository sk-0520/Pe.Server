import * as dom from '../core/dom';
import * as path from '../core/path';
import * as ajax from '../core/ajax';

function attachDelete(deleteElement: HTMLButtonElement) {
	deleteElement.addEventListener('click', async ev => {
		ev.preventDefault();

		const logName = dom.getDataset(deleteElement, 'name');
		const json = await ajax.communicateJsonAsync(path.join('/ajax/log', logName), 'DELETE');

		if (!json.error) {
			location.reload();
		} else {
			alert(JSON.stringify(json.error));
		}
	})
}

export function boot() {
	for (const buttonElement of document.querySelectorAll<HTMLButtonElement>('button.pg-delete')) {
		attachDelete(buttonElement);
	}
}
