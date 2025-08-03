import * as throws from "./throws";
import * as types from "./types";

/**
 * ID から要素取得を強制。
 *
 * @param elementId
 * @param elementType
 * @returns
 * @throws {throws.NotFoundDomSelectorError} セレクタから要素が見つからない
 * @throws {throws.ElementTypeError} 要素に指定された型が合わない
 */
export function requireElementById<THtmlElement extends HTMLElement>(
	elementId: string,
	elementType?: types.Constructor<THtmlElement>,
): THtmlElement {
	const result = document.getElementById(elementId);
	if (!result) {
		throw new throws.NotFoundDomSelectorError(elementId);
	}

	if (elementType) {
		if (!types.instanceOf(result, elementType)) {
			throw new throws.ElementTypeError(
				`${result.constructor.name} != ${elementType.prototype.constructor.name}`,
			);
		}
	}

	return result as THtmlElement;
}

/**
 * セレクタから要素取得を強制。
 *
 * @param element
 * @param selectors
 * @returns
 */
export function requireSelector<K extends keyof HTMLElementTagNameMap>(
	element: ParentNode,
	selectors: K,
): HTMLElementTagNameMap[K];
export function requireSelector<K extends keyof HTMLElementTagNameMap>(
	selectors: K,
): HTMLElementTagNameMap[K];
export function requireSelector<K extends keyof SVGElementTagNameMap>(
	element: ParentNode,
	selectors: K,
): SVGElementTagNameMap[K];
export function requireSelector<K extends keyof SVGElementTagNameMap>(
	selectors: K,
): SVGElementTagNameMap[K];
export function requireSelector<TElement extends Element = Element>(
	selectors: string,
	elementType?: types.Constructor<TElement>,
): TElement;
export function requireSelector<TElement extends Element = Element>(
	element: ParentNode,
	selectors: string,
	elementType?: types.Constructor<TElement>,
): TElement;
export function requireSelector<TElement extends Element = Element>(
	element: ParentNode | string | null,
	selectors?: string | types.Constructor<TElement>,
	elementType?: types.Constructor<TElement>,
): TElement {
	if (types.isString(element)) {
		if (selectors) {
			if (types.isString(selectors)) {
				throw new throws.MismatchArgumentError("selectors");
			}
			elementType = selectors;
		}
		selectors = element;
		element = null;
	} else {
		if (types.isUndefined(selectors)) {
			throw new throws.MismatchArgumentError("selectors");
		}
		if (!types.isString(selectors)) {
			throw new throws.MismatchArgumentError("selectors");
		}
	}

	const result = (element ?? document).querySelector(selectors);
	if (!result) {
		throw new throws.NotFoundDomSelectorError(selectors);
	}

	if (elementType) {
		if (!types.instanceOf(result, elementType)) {
			throw new throws.ElementTypeError(
				`${result.constructor.name} != ${elementType.prototype.constructor.name}`,
			);
		}
	}

	return result as TElement;
}

/**
 * セレクタに一致する要素リストの取得を強制。
 * @param element
 * @param selectors
 */
export function requireSelectorAll<K extends keyof HTMLElementTagNameMap>(
	element: ParentNode,
	selectors: K,
): NodeListOf<HTMLElementTagNameMap[K]>;
export function requireSelectorAll<K extends keyof HTMLElementTagNameMap>(
	selectors: K,
): NodeListOf<HTMLElementTagNameMap[K]>;
export function requireSelectorAll<K extends keyof SVGElementTagNameMap>(
	element: ParentNode,
	selectors: K,
): NodeListOf<SVGElementTagNameMap[K]>;
export function requireSelectorAll<K extends keyof SVGElementTagNameMap>(
	selectors: K,
): NodeListOf<SVGElementTagNameMap[K]>;
export function requireSelectorAll<TElement extends Element = Element>(
	selectors: string,
	elementType?: types.Constructor<TElement>,
): NodeListOf<TElement>;
export function requireSelectorAll<TElement extends Element = Element>(
	element: ParentNode,
	selectors: string,
	elementType?: types.Constructor<TElement>,
): NodeListOf<TElement>;
export function requireSelectorAll<TElement extends Element = Element>(
	element: ParentNode | string | null,
	selectors?: string | types.Constructor<TElement>,
	elementType?: types.Constructor<TElement>,
): NodeListOf<TElement> {
	if (types.isString(element)) {
		if (selectors) {
			if (types.isString(selectors)) {
				throw new throws.MismatchArgumentError("selectors");
			}
			elementType = selectors;
		}
		selectors = element;
		element = null;
	} else {
		if (types.isUndefined(selectors)) {
			throw new throws.MismatchArgumentError("selectors");
		}
		if (!types.isString(selectors)) {
			throw new throws.MismatchArgumentError("selectors");
		}
	}

	const result = (element ?? document).querySelectorAll<TElement>(selectors);
	if (!result) {
		throw new throws.NotFoundDomSelectorError(selectors);
	}

	if (elementType) {
		for (const elm of result) {
			if (!types.instanceOf(elm, elementType)) {
				throw new throws.ElementTypeError(
					`elm ${elm} != ${elementType.prototype.constructor.name}`,
				);
			}
		}
	}

	return result;
}

/**
 * セレクタから先祖要素を取得。
 *
 * @param selectors
 * @param element
 * @returns
 */
export function requireClosest<K extends keyof HTMLElementTagNameMap>(
	element: Element,
	selectors: K,
): HTMLElementTagNameMap[K];
export function requireClosest<K extends keyof SVGElementTagNameMap>(
	element: Element,
	selectors: K,
): SVGElementTagNameMap[K];
export function requireClosest<E extends Element = Element>(
	element: Element,
	selectors: string,
	elementType?: types.Constructor<E>,
): E;
export function requireClosest<TElement extends Element = Element>(
	element: Element,
	selectors: string,
	elementType?: types.Constructor<TElement>,
): Element {
	const result = element.closest(selectors);
	if (!result) {
		throw new throws.NotFoundDomSelectorError(selectors);
	}

	if (elementType) {
		if (!types.instanceOf(result, elementType)) {
			throw new throws.ElementTypeError(
				`${result.constructor.name} != ${elementType.prototype.constructor.name}`,
			);
		}
	}

	return result;
}

/**
 * 対象要素から所属する `Form` 要素を取得する。
 * @param element `Form` に所属する要素。
 * @returns
 */
export function getParentForm(element: Element): HTMLFormElement {
	return requireClosest(element, "form");
}

/**
 * テンプレートを実体化。
 * @param selectors
 */
export function cloneTemplate(selectors: string): DocumentFragment;
export function cloneTemplate(element: HTMLTemplateElement): DocumentFragment;
export function cloneTemplate(
	input: string | HTMLTemplateElement,
): DocumentFragment {
	const element =
		typeof input === "string"
			? requireSelector(input, HTMLTemplateElement)
			: input;

	const result = element.content.cloneNode(true);

	return result as DocumentFragment;
}

/**
 * 要素生成処理の構築。
 *
 * @param tagName
 * @param options
 */
export function createFactory<K extends keyof HTMLElementTagNameMap>(
	tagName: K,
	options?: ElementCreationOptions,
): TagFactory<HTMLElementTagNameMap[K]>;
/** @deprecated */
export function createFactory<K extends keyof HTMLElementDeprecatedTagNameMap>(
	tagName: K,
	options?: ElementCreationOptions,
): TagFactory<HTMLElementDeprecatedTagNameMap[K]>;
export function createFactory<THTMLElement extends HTMLElement>(
	tagName: string,
	options?: ElementCreationOptions,
): TagFactory<THTMLElement>;
export function createFactory(
	tagName: string,
	options?: ElementCreationOptions,
): TagFactory<HTMLElement> {
	const element = document.createElement(tagName, options);
	return new TagFactory(element);
}

/**
 * 要素の追加位置。
 */
export enum AttachPosition {
	/** 最後。 */
	Last = 0,
	/** 最初。 */
	First = 1,
	/** 直前。 */
	Previous = 2,
	/** 直後。 */
	Next = 3,
}

/**
 * 指定した要素から見た特定の位置に要素をくっつける
 * @param parent 指定要素。
 * @param position 位置。
 * @param factory 追加する要素。
 */
export function attach(
	parent: Element,
	position: AttachPosition,
	factory: NodeFactory,
): Node;
export function attach<TElement extends Element = Element>(
	parent: Element,
	position: AttachPosition,
	factory: TagFactory<TElement>,
): TElement;
export function attach(
	parent: Element,
	position: AttachPosition,
	node: Node,
): Node;
export function attach(
	parent: Element,
	position: AttachPosition,
	node: Node | NodeFactory,
): Node {
	const workNode = isNodeFactory(node) ? node.element : node;

	switch (position) {
		case AttachPosition.Last:
			return parent.appendChild(workNode);

		case AttachPosition.First:
			return parent.insertBefore(workNode, parent.firstChild);

		case AttachPosition.Previous:
			if (!parent.parentNode) {
				throw new TypeError("parent.parentNode");
			}
			return parent.parentNode.insertBefore(workNode, parent);

		case AttachPosition.Next:
			if (!parent.parentNode) {
				throw new TypeError("parent.parentNode");
			}
			return parent.parentNode.insertBefore(workNode, parent.nextSibling);

		default:
			throw new throws.NotImplementedError();
	}
}

function isNodeFactory(arg: unknown): arg is NodeFactory {
	return types.hasObject(arg, "element");
}

/**
 * ノード生成処理。
 */
export interface NodeFactory {
	//#region property

	readonly element: Node;

	//#endregion
}

/**
 * テキストノード生成処理。
 */
export class TextFactory implements NodeFactory {
	constructor(public readonly element: Text) {}
}

/**
 * 要素生成処理。
 */
export class TagFactory<TElement extends Element> implements NodeFactory {
	constructor(public readonly element: TElement) {}

	public createTag<K extends keyof HTMLElementTagNameMap>(
		tagName: K,
		options?: ElementCreationOptions,
	): TagFactory<HTMLElementTagNameMap[K]>;
	/** @deprecated */
	public createTag<K extends keyof HTMLElementDeprecatedTagNameMap>(
		tagName: K,
		options?: ElementCreationOptions,
	): TagFactory<HTMLElementDeprecatedTagNameMap[K]>;
	public createTag<THTMLElement extends HTMLElement>(
		tagName: string,
		options?: ElementCreationOptions,
	): TagFactory<THTMLElement>;
	public createTag(
		tagName: string,
		options?: ElementCreationOptions,
	): TagFactory<HTMLElement> {
		const createdElement = document.createElement(tagName, options);
		this.element.appendChild(createdElement);

		const nodeFactory = new TagFactory(createdElement);
		return nodeFactory;
	}

	public createText(text: string): TextFactory {
		const createdNode = document.createTextNode(text);
		this.element.appendChild(createdNode);

		const nodeFactory = new TextFactory(createdNode);
		return nodeFactory;
	}
}

/**
 * カスタムデータ属性のケバブ名を dataset アクセス可能な名前に変更
 * @param kebab データ属性名。
 * @param removeDataAttributeBegin 先頭の `data-`* を破棄するか。
 */
export function toCustomKey(
	kebab: string,
	removeDataAttributeBegin = true,
): string {
	const dataHead = "data-";
	const workKebab =
		removeDataAttributeBegin && kebab.startsWith(dataHead)
			? kebab.substring(dataHead.length)
			: kebab;

	return workKebab
		.split("-")
		.map((item, index) =>
			index
				? item.charAt(0).toUpperCase() + item.slice(1).toLowerCase()
				: item.toLowerCase(),
		)
		.join("");
}

/**
 * データ属性から値を取得。
 *
 * @param element 要素。
 * @param dataKey データ属性名。
 * @param removeDataAttributeBegin 先頭の `data-` を破棄するか。
 * @returns
 */
export function getDataset(
	element: HTMLOrSVGElement,
	dataKey: string,
	removeDataAttributeBegin = true,
): string {
	const key = toCustomKey(dataKey, removeDataAttributeBegin);
	const value = element.dataset[key];
	if (types.isUndefined(value)) {
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
export function getDatasetOr(
	element: HTMLOrSVGElement,
	dataKey: string,
	fallback: string,
	removeDataAttributeBegin = true,
): string {
	const key = toCustomKey(dataKey, removeDataAttributeBegin);
	const value = element.dataset[key];
	if (types.isUndefined(value)) {
		return fallback;
	}

	return value;
}

type HtmlTagName =
	| Uppercase<
			| keyof HTMLElementTagNameMap
			| keyof HTMLElementDeprecatedTagNameMap
			| keyof SVGElementTagNameMap
	  >
	| Lowercase<
			| keyof HTMLElementTagNameMap
			| keyof HTMLElementDeprecatedTagNameMap
			| keyof SVGElementTagNameMap
	  >;
/**
 * 要素のタグ名の一致判定。
 *
 * @param element 対象要素
 * @param value タグ名
 * @returns
 */
export function equalTagName(element: Element, value: HtmlTagName): boolean;
export function equalTagName(element: Element, value: string): boolean;
export function equalTagName(element: Element, value: Element): boolean;
export function equalTagName(
	element: Element,
	value: string | Element,
): boolean {
	const workValue = !types.isString(value) ? value.tagName : value;

	if (element.tagName === workValue) {
		return true;
	}

	return element.tagName.toUpperCase() === workValue.toUpperCase();
}

/**
 * 指定要素を兄弟間で上下させる。
 * @param current 対象要素。
 * @param isUp 上に移動させるか(偽の場合下に移動)。
 */
export function moveElement(current: HTMLElement, isUp: boolean): void {
	const refElement = isUp
		? current.previousElementSibling
		: current.nextElementSibling;

	if (refElement) {
		const newItem = isUp ? current : refElement;
		const oldItem = isUp ? refElement : current;
		current.parentElement?.insertBefore(newItem, oldItem);
	}
}
