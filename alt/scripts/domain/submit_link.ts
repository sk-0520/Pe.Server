import * as dom from "../core/dom";
import * as dialog from "../core/dialog";

function attachSubmit(element: HTMLButtonElement): void {
	if (element.dataset.dialog === "disabled") {
		return;
	}

	element.addEventListener("click", async (ev) => {
		ev.preventDefault();

		const message = dom.getDatasetOr(
			element,
			"dialog-message",
			"実行しますか？",
		);

		const dialogResult = await dialog.showAsync({
			button: dialog.ButtonType.YesNo,
			display: message,
		});

		if (dialogResult.action !== dialog.DialogAction.Positive) {
			return;
		}

		const form = dom.getParentForm(element);
		form.submit();
	});
}

export function boot() {
	const elements =
		document.querySelectorAll<HTMLButtonElement>("form button.link");
	for (const element of elements) {
		attachSubmit(element);
	}
}
