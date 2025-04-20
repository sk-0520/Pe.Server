/**
 * @jest-environment jsdom
 */
import * as dom from "../../scripts/core/dom";
import * as throws from "../../scripts/core/throws";

describe("dom", () => {
	test("requireElementById", () => {
		document.body.innerHTML = `
			<span id="id1">id1:1</span>
			<span id="id2">id2:1</span>
		`;

		expect(dom.requireElementById("id1").textContent).toBe("id1:1");
		expect(dom.requireElementById("id2").textContent).toBe("id2:1");
		expect(dom.requireElementById("id2", HTMLSpanElement).textContent).toBe(
			"id2:1",
		);

		expect(() => dom.requireElementById("id3")).toThrowError(
			throws.NotFoundDomSelectorError,
		);
		expect(() => dom.requireElementById("id2", HTMLDivElement)).toThrowError(
			throws.ElementTypeError,
		);
	});

	test("requireSelector", () => {
		document.body.innerHTML = `
			<div>
				<div data-name="x">X1</div>
				<div name="x">X2</div>
			</div>
		`;

		expect(dom.requireSelector('[data-name="x"]').textContent).toBe("X1");
		expect(dom.requireSelector('[name="x"]').textContent).toBe("X2");

		expect(() => dom.requireSelector(".x")).toThrow(
			throws.NotFoundDomSelectorError,
		);
		expect(() =>
			dom.requireSelector('[data-name="x"]', HTMLSpanElement),
		).toThrow(throws.ElementTypeError);

		const rootDiv = dom.requireSelector("div");
		expect(dom.requireSelector(rootDiv, "div").textContent).toBe("X1");
		expect(() => dom.requireSelector(rootDiv, "div", HTMLSpanElement)).toThrow(
			throws.ElementTypeError,
		);
	});

	test("requireSelectorAll", () => {
		document.body.innerHTML = `
			<div>
				<div data-name="x">X1</div>
				<div name="x">X2</div>
				<span>X3</span>
			</div>
			<ul>
				<li>A</li>
				<li>B</li>
				<li>C</li>
			</ul>
		`;

		expect(dom.requireSelectorAll("*").length).toBe(
			8 /* html + head + body = 3 */ + 3,
		);
		expect(dom.requireSelectorAll(document.body, "*").length).toBe(8);

		expect(dom.requireSelectorAll("div").length).toBe(3);

		expect(dom.requireSelectorAll("div *").length).toBe(3);
		expect(dom.requireSelectorAll("div div").length).toBe(2);

		expect(() => dom.requireSelectorAll("div *", HTMLDivElement)).toThrowError(
			throws.ElementTypeError,
		);
		expect(dom.requireSelectorAll("div *", HTMLElement).length).toBe(3);
		expect(dom.requireSelectorAll("div div", HTMLDivElement).length).toBe(2);

		expect(dom.requireSelectorAll("ul *").length).toBe(3);
		expect(dom.requireSelectorAll("ul li").length).toBe(3);
	});

	test("requireClosest", () => {
		document.body.innerHTML = `
			<div id="a">
				<div id="b">
					<div id="c">
						<div id="d">
						</div>
					</div>
				</div>
			</div>
		`;

		const a = document.getElementById("a") as HTMLElement;
		const b = document.getElementById("b") as HTMLElement;
		const c = document.getElementById("c") as HTMLElement;
		const d = document.getElementById("d") as HTMLElement;

		expect(dom.requireClosest(a, "*").id).toBe("a");
		expect(dom.requireClosest(b, "#a").id).toBe("a");
		expect(dom.requireClosest(c, "#b").id).toBe("b");
		expect(dom.requireClosest(d, "#b").id).toBe("b");
		expect(dom.requireClosest(d, "#a > div").id).toBe("b");
		expect(dom.requireClosest(d, "#a > div > div").id).toBe("c");

		expect(() => dom.requireClosest(d, "#a span")).toThrowError(
			throws.NotFoundDomSelectorError,
		);

		expect(dom.requireClosest(a, "*", HTMLDivElement).id).toBe("a");
		expect(() => dom.requireClosest(a, "*", HTMLSpanElement)).toThrowError(
			throws.ElementTypeError,
		);
	});

	test("getParentForm", () => {
		document.body.innerHTML = `
			<form data-key="a">
				<div id="a"></div>
			</form>
			<form data-key="b">
				<div id="b"></div>
			</form>
			<div>
				<div id="c"></div>
			</div>
		`;

		const a = document.getElementById("a") as HTMLElement;
		const b = document.getElementById("b") as HTMLElement;
		const c = document.getElementById("c") as HTMLElement;

		expect(dom.getParentForm(a).dataset.key).toBe("a");
		expect(dom.getParentForm(b).dataset.key).toBe("b");

		expect(() => dom.getParentForm(c)).toThrowError(Error);
	});

	test("cloneTemplate", () => {
		document.body.innerHTML = `
			<template id="a">
				<div name="a"></div>
			</template>
			<div id="error">xxx</div>
		`;

		const a1 = document.getElementById("a") as HTMLTemplateElement;
		const a2 = dom.cloneTemplate(a1);
		expect(a2.querySelector("[name]")?.getAttribute("name")).toBe("a");

		const b2 = dom.cloneTemplate("#a");
		expect(b2.querySelector("[name]")?.getAttribute("name")).toBe("a");

		expect(() => dom.cloneTemplate("#error")).toThrowError(
			throws.ElementTypeError,
		);
	});

	test("toCustomKey", () => {
		expect(dom.toCustomKey("")).toBe("");
		expect(dom.toCustomKey("a")).toBe("a");
		expect(dom.toCustomKey("aB")).toBe("ab");
		expect(dom.toCustomKey("aBc")).toBe("abc");
		expect(dom.toCustomKey("a-b")).toBe("aB");
		expect(dom.toCustomKey("a-b-c")).toBe("aBC");

		expect(dom.toCustomKey("dataset-key-data")).toBe("datasetKeyData");

		expect(dom.toCustomKey("data-key-data")).toBe("keyData");
		expect(dom.toCustomKey("data-key-data", true)).toBe("keyData");
		expect(dom.toCustomKey("data-key-data", false)).toBe("dataKeyData");
	});

	test("attach:Last", () => {
		document.body.innerHTML = `
			<div id="root">
				<div id="A">A</div>
				<div id="B">B</div>
				<div id="C">C</div>
			</div>
		`;
		const root = dom.requireElementById("root");

		const div = document.createElement("div");
		div.textContent = "*";

		dom.attach(root, dom.AttachPosition.Last, div);
		expect(root.children[0].textContent).toBe("A");
		expect(root.children[1].textContent).toBe("B");
		expect(root.children[2].textContent).toBe("C");
		expect(root.children[3].textContent).toBe("*");
	});

	test("attach:First", () => {
		document.body.innerHTML = `
			<div id="root">
				<div id="A">A</div>
				<div id="B">B</div>
				<div id="C">C</div>
			</div>
		`;
		const root = dom.requireElementById("root");

		const div = document.createElement("div");
		div.textContent = "*";

		dom.attach(root, dom.AttachPosition.First, div);
		expect(root.children[0].textContent).toBe("*");
		expect(root.children[1].textContent).toBe("A");
		expect(root.children[2].textContent).toBe("B");
		expect(root.children[3].textContent).toBe("C");
	});

	test("attach:Previous", () => {
		document.body.innerHTML = `
			<div id="root">
				<div id="A">A</div>
				<div id="B">B</div>
				<div id="C">C</div>
			</div>
		`;
		const root = dom.requireElementById("root");
		const center = dom.requireElementById("B");

		const div = document.createElement("div");
		div.textContent = "*";

		dom.attach(center, dom.AttachPosition.Previous, div);
		expect(root.children[0].textContent).toBe("A");
		expect(root.children[1].textContent).toBe("*");
		expect(root.children[2].textContent).toBe("B");
		expect(root.children[3].textContent).toBe("C");
	});

	test("attach:Next", () => {
		document.body.innerHTML = `
			<div id="root">
				<div id="A">A</div>
				<div id="B">B</div>
				<div id="C">C</div>
			</div>
		`;
		const root = dom.requireElementById("root");
		const center = dom.requireElementById("B");

		const div = document.createElement("div");
		div.textContent = "*";

		dom.attach(center, dom.AttachPosition.Next, div);
		expect(root.children[0].textContent).toBe("A");
		expect(root.children[1].textContent).toBe("B");
		expect(root.children[2].textContent).toBe("*");
		expect(root.children[3].textContent).toBe("C");
	});

	test("createFactory", () => {
		document.body.innerHTML = `
			<div id="root"></div>
		`;
		const root = dom.requireElementById("root");

		const p = dom.createFactory("p");
		p.createText("HEAD");
		const span = p.createTag("span");
		span.element.textContent = "CENTER";
		p.createText("TAIL");

		const pe = dom.attach(root, dom.AttachPosition.Last, p);

		expect(pe).toBe(p.element);
		expect(pe.childNodes[0].textContent).toBe("HEAD");
		expect(pe.childNodes[1].textContent).toBe("CENTER");
		expect(pe.childNodes[1].nodeName).toBe("SPAN");
		expect(pe.childNodes[2].textContent).toBe("TAIL");
	});

	test("getDataset", () => {
		document.body.innerHTML = `
			<div id="id" data-a="A" data-a-sub="SUB" data-empty=""></div>
		`;

		const element = dom.requireElementById("id");

		expect(dom.getDataset(element, "a")).toBe("A");
		expect(dom.getDataset(element, "a-sub")).toBe("SUB");

		expect(dom.getDataset(element, "data-a")).toBe("A");
		expect(dom.getDataset(element, "data-a-sub")).toBe("SUB");

		expect(dom.getDataset(element, "empty")).toBe("");
		expect(() => dom.getDataset(element, "data-a", false)).toThrowError(Error);
		expect(() => dom.getDataset(element, "b")).toThrowError(Error);
	});

	test("getDatasetOr", () => {
		document.body.innerHTML = `
			<div id="id" data-a="A" data-a-sub="SUB" data-empty=""></div>
		`;

		const element = dom.requireElementById("id");

		expect(dom.getDatasetOr(element, "a", "X")).toBe("A");
		expect(dom.getDatasetOr(element, "a-sub", "X")).toBe("SUB");

		expect(dom.getDatasetOr(element, "data-a", "X")).toBe("A");
		expect(dom.getDatasetOr(element, "data-a-sub", "X")).toBe("SUB");

		expect(dom.getDatasetOr(element, "empty", "X")).toBe("");
		expect(dom.getDatasetOr(element, "data-a", "X", false)).toBe("X");
		expect(dom.getDatasetOr(element, "b", "X")).toBe("X");
	});

	test("equalTagName", () => {
		document.body.innerHTML = `
			<div id="id"></div>
		`;

		const element = dom.requireElementById("id");

		expect(dom.equalTagName(element, "div")).toBeTruthy();
		expect(dom.equalTagName(element, "DIV")).toBeTruthy();
		expect(dom.equalTagName(element, "span")).toBeFalsy();
		expect(dom.equalTagName(element, "pppp")).toBeFalsy();

		expect(
			dom.equalTagName(element, document.createElement("div")),
		).toBeTruthy();
		expect(
			dom.equalTagName(element, document.createElement("Div")),
		).toBeTruthy();
		expect(
			dom.equalTagName(element, document.createElement("DIV")),
		).toBeTruthy();

		expect(
			dom.equalTagName(element, document.createElement("span")),
		).toBeFalsy();
	});
});
