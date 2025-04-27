/**
 * 指定文字列をコピー。
 * @param text 対象文字列。
 */
export async function copyText(text: string): Promise<void> {
	if (navigator.clipboard) {
		return await navigator.clipboard.writeText(text);
	}

	const copyElement = document.createElement("pre");
	copyElement.textContent = text;
	const bodyElement = document.body;
	bodyElement.appendChild(copyElement);

	const range = document.createRange();
	range.selectNodeContents(copyElement);
	const selection = window.getSelection();
	if (!selection) {
		throw new Error("window.getSelection");
	}

	selection.removeAllRanges();
	selection.addRange(range);

	document.execCommand("copy");
	selection.removeAllRanges();
	bodyElement.removeChild(copyElement);
}
