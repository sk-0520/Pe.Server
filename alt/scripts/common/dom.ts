
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

export function forceSelector<THtmlElement extends Element>(element: HTMLElement, selector: string): THtmlElement {
	const result = element.querySelector(selector);
	if (!result) {
		throw new Error(selector);
	}

	return result as THtmlElement;
}

export function getDataset(element: HTMLOrSVGElement, dataKey: string): string {
	const key = toCustomKey(dataKey);
	const value = element.dataset[key];
	if (value == undefined) {
		throw new Error(`${element}.${key}`);
	}

	return value;
}
