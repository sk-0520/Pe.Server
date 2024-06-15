import * as dom from "../../core/dom";

export function boot() {
	const versionCommandElement = dom.requireElementById(
		"command_get_versions",
		HTMLButtonElement,
	);

	const releaseUrl = dom.getDataset(versionCommandElement, "release_url");

	versionCommandElement.addEventListener("click", async (ev) => {
		ev.preventDefault();

		const response = await fetch(releaseUrl, {
			headers: {
				Accept: "application/vnd.github+json",
				"X-GitHub-Api-Version": "2022-11-28",
			},
		});
		const json = await response.json();
		console.debug(json);
		const versions = json.map((a: { name: any; tag_name: any }) => ({
			name: a.name,
			tag_name: a.tag_name,
		}));
		const result = JSON.stringify(versions, undefined, "\t");

		const versionsElement = dom.requireElementById("versions");
		versionsElement.textContent = result;
	});
}
