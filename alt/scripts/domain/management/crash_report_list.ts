import * as dialog from "../../core/dialog";
import * as dom from "../../core/dom";
import * as ajax from "../ajax";
import * as url from "../../core/url";

function attachDelete(element: HTMLButtonElement): void {
	element.addEventListener("click", async (ev) => {
		const dialogResult = await dialog.showAsync({
			button: dialog.ButtonType.YesNo,
			display: () => {
				const template =
					dom.requireElementById<HTMLTemplateElement>("pg-delete-dialog");
				const work = dom.cloneTemplate(template);

				return work;
			},
		});

		if (dialogResult.action !== dialog.DialogAction.Positive) {
			return;
		}

		const sequence = dom.getDataset(element, "sequence");
		const json = await ajax.communicateJsonAsync(
			url.joinPath("/ajax/crash-report", sequence),
			"DELETE",
		);

		if (!json.error) {
			location.reload();
		} else {
			alert(JSON.stringify(json.error));
		}
	});
}

export function boot() {
	const deleteButtonElements =
		document.querySelectorAll<HTMLButtonElement>("button.pg-delete");
	for (const deleteButtonElement of deleteButtonElements) {
		attachDelete(deleteButtonElement);
	}
}
