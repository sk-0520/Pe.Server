
/**
 * カスタムデータ属性のケバブ名を dataset アクセス可能な名前に変更
 * @param kebab データ属性名。
 * @param removeDataAttributeBegin 先頭の `data-`* を破棄するか。
 */
export function toCustomKey(kebab: string, removeDataAttributeBegin: boolean = true): string {

	const dataHead = 'data-';
	if(removeDataAttributeBegin && kebab.startsWith(dataHead)) {
		kebab = kebab.substring(dataHead.length);
	}

	return kebab
		.split('-')
		.map((item, index) => index
			? item.charAt(0).toUpperCase() + item.slice(1).toLowerCase()
			: item.toLowerCase()
		)
		.join('')
		;
}

/**
 * データ属性から値を取得。
 *
 * @param element 要素。
 * @param dataKey データ属性名。
 * @param removeDataAttributeBegin 先頭の `data-`* を破棄するか。
 * @returns
 */
export function getDataset(element: HTMLOrSVGElement, dataKey: string, removeDataAttributeBegin: boolean = true): string {
	const key = toCustomKey(dataKey, removeDataAttributeBegin);
	const value = element.dataset[key];
	if (value == undefined) {
		throw new Error(`${element}.${key}`);
	}

	return value;
}

/**
 * データ属性から値を取得。
 *
 * @param element 要素。
 * @param dataKey データ属性名。
 * @param fallback 取得失敗時の返却値。
 * @param removeDataAttributeBegin 先頭の `data-`* を破棄するか。
 * @returns
 */
export function getDatasetOr(element: HTMLOrSVGElement, dataKey: string, fallback: string, removeDataAttributeBegin: boolean = true): string {
	const key = toCustomKey(dataKey, removeDataAttributeBegin);
	const value = element.dataset[key];
	if (value == undefined) {
		return fallback;
	}

	return value;
}

/**
 * ID から要素取得を強制。
 *
 * @param elementId
 * @returns
 */
export function requireElementById<THtmlElement extends HTMLElement>(elementId: string): THtmlElement {
	const result = document.getElementById(elementId);
	if (!result) {
		throw new Error(elementId);
	}

	return result as THtmlElement;
}

/**
 * セレクタから要素取得を強制。
 *
 * @param selector
 * @param element
 * @returns
 */
export function requireSelector<THtmlElement extends HTMLElement>(selector: string, element: HTMLElement | null = null): THtmlElement {
	const result = (element ?? document).querySelector(selector);
	if (!result) {
		throw new Error(selector);
	}

	return result as THtmlElement;
}

/**
 * 対象要素から所属する Form 要素を取得する。
 * @param element Formに所属する要素。
 * @returns
 */
export function getParentForm(element: HTMLElement): HTMLFormElement {
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

