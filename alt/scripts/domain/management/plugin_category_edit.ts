import * as dom from '../../core/dom'
import * as ajax from '../../core/ajax'
import * as path from '../../core/path'

function registerRow(trElement: HTMLTableRowElement) {
	const pluginCategoryId = dom.getDataset(trElement, 'plugin-category-id');
	const updateElement = trElement.querySelector('[name="update"]') as HTMLButtonElement;
	const deleteElement = trElement.querySelector('[name="delete"]') as HTMLButtonElement;

	updateElement.addEventListener('click', async ev => {
		const displayNameElement = trElement.querySelector('[name="display-name"]') as HTMLInputElement;
		if (!displayNameElement.value.trim().length) {
			displayNameElement.setCustomValidity('未入力');
			return;
		}

		const descriptionElement = trElement.querySelector('[name="description"]') as HTMLInputElement;

		const json = await ajax.communicateJsonAsync(path.join('/ajax/plugin-category', pluginCategoryId), 'PATCH', {
			'category_display_name': displayNameElement.value.trim(),
			'category_description': descriptionElement.value.trim(),
		});
		if(json.error) {
			alert(json);
		}
	}, false);

	deleteElement.addEventListener('click', async ev => {
		if (confirm(pluginCategoryId + ' を削除?')) {
			const json = await ajax.communicateJsonAsync(path.join('/ajax/plugin-category', pluginCategoryId), 'DELETE');
			if(json.error) {
				alert(json);
			} else {
				trElement.remove();
			}
		}
	}, false);
}

function registerAdd() {
	document.getElementById('category_add_submit')!.addEventListener('click', async ev => {
		const categoryAddIdElement = document.getElementById('category_add_id') as HTMLInputElement;
		const categoryAddDisplayNameElement = document.getElementById('category_add_display') as HTMLInputElement;
		const categoryAddDescriptionElement = document.getElementById('category_add_description') as HTMLInputElement;

		const categoryId = categoryAddIdElement.value.trim();
		const categoryDisplayName = categoryAddDisplayNameElement.value.trim();
		const categoryAddDescription = categoryAddDescriptionElement.value.trim();

		if (!categoryId.length) {
			return;
		}
		if (!categoryDisplayName.length) {
			return;
		}

		const json = await ajax.communicateJsonAsync('/ajax/plugin-category', 'POST', {
			'category_id': categoryId,
			'category_display_name': categoryDisplayName,
			'category_description': categoryAddDescription,
		});
		if(json.error) {
			alert(json);
		} else {
			location.reload();
		}
	})
}

function register() {
	const categoryItemsElement = document.getElementById('category_items')!;
	const trElements = categoryItemsElement.querySelectorAll<HTMLTableRowElement>('tr');

	for (const trElement of trElements) {
		registerRow(trElement);
	}

	registerAdd();
}

export function boot() {
	register();
}
