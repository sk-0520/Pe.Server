
/**
 * 指定文字列をコピー。
 * @param text 対象文字列。
 */
export function copyText(text: string): void {
	const copyElement = document.createElement('pre');
	copyElement.textContent = text;
	const bodyElement = document.body;
	bodyElement.appendChild(copyElement);

	const range = document.createRange();
	range.selectNodeContents(copyElement);
	const selection = window.getSelection()!;
	selection.removeAllRanges();
	selection.addRange(range);

	document.execCommand('copy');
	selection.removeAllRanges();
	bodyElement.removeChild(copyElement);
}
