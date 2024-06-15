import * as logging from "../core/logging";
import * as responsive from "../core/responsive";
const logger = logging.create("navigator");

function register() {
	const detailsElement = document.querySelector(
		"nav details",
	) as HTMLDetailsElement;

	window
		.matchMedia(responsive.mediaSet.phone)
		.addEventListener("change", (ev) => {
			resetMedia(ev.matches, detailsElement);
		});

	detailsElement.addEventListener(
		"toggle",
		(ev) => {
			toggle(detailsElement);
		},
		false,
	);
}

function resetMedia(
	isPhoneMatches: boolean,
	detailsElement: HTMLDetailsElement,
) {
	if (!isPhoneMatches) {
		detailsElement.open = false;
	}
}

function toggle(detailsElement: HTMLDetailsElement) {
	if (detailsElement.open) {
		const menuElement = document.querySelector(
			"nav details .menu",
		) as HTMLDetailsElement;
		if (menuElement.childElementCount) {
			logger.debug("メニュー複製済みのため何もしない");
			return;
		}

		const headerListElement = document.querySelector(
			"header ul",
		) as HTMLElement;

		const clonedElement = headerListElement.cloneNode(true);
		menuElement.appendChild(clonedElement);

		document.addEventListener(
			"click",
			(ev) => {
				const target = ev.target as Element;
				if (!target.closest("nav details")) {
					logger.debug("身内じゃないので閉じる");
					detailsElement.open = false;
				}
			},
			false,
		);
	}
}

export function boot() {
	register();
}
