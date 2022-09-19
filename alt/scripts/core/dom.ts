
/**
 * カスタムデータ属性のケバブ名を dataset アクセス可能な名前に変更
 * @param kebab
 */
export function toCustomKey(kebab: string): string {
	return kebab
		.split('-')
		.map((item, index) => index
			? item.charAt(0).toUpperCase() + item.slice(1).toLowerCase()
			: item.toLowerCase()
		)
		.join('')
		;
}

export function getDataset(element: HTMLOrSVGElement, dataKey: string): string {
	const key = toCustomKey(dataKey);
	const value = element.dataset[key];
	if (value == undefined) {
		throw new Error(`${element}.${key}`);
	}

	return value;
}

export function getDatasetOr(element: HTMLOrSVGElement, dataKey: string, fallback: string): string {
	const key = toCustomKey(dataKey);
	const value = element.dataset[key];
	if (value == undefined) {
		return fallback;
	}

	return value;
}

export function requireElementById<THtmlElement extends HTMLElement>(elementId: string): THtmlElement {
	const result = document.getElementById(elementId);
	if (!result) {
		throw new Error(elementId);
	}

	return result as THtmlElement;
}

export function requireSelector<THtmlElement extends HTMLElement>(selector: string, element: HTMLElement | null = null): THtmlElement {
	const result = (element ?? document).querySelector(selector);
	if (!result) {
		throw new Error(selector);
	}

	return result as THtmlElement;
}

export function getForm(element: HTMLElement): HTMLFormElement {
	const formElement = element.closest<HTMLFormElement>('form');
	if (formElement === null) {
		throw new Error(element.outerText);
	}

	return formElement;
}

export function cloneTemplate(element: HTMLTemplateElement): HTMLElement {
	return element.content.cloneNode(true) as HTMLElement;
}

/**
 * 指定要素を兄弟間で上下させる。
 * @param current 対象要素。
 * @param isUp 上に移動させるか(偽の場合下に移動)。
 */
export function moveElement(current: HTMLElement, isUp: boolean): void {
	const refElement = isUp
		? current.previousElementSibling
		: current.nextElementSibling
		;

	if (refElement) {
		const newItem = isUp ? current : refElement;
		const oldItem = isUp ? refElement : current;
		current.parentElement!.insertBefore(newItem, oldItem);
	}
}

