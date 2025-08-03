//import * as dom from '../core/dom'
import * as ajax from "./ajax";

async function generatePluginIdCoreAsync() {
	const json = await ajax.communicateJsonAsync<{ plugin_id: string }>(
		"/api/plugin/generate-plugin-id",
		"GET",
	);
	if (json.error) {
		alert(json);
		return;
	}

	const pluginIdElement = document.getElementById(
		"pg-plugin-id",
	) as HTMLInputElement;
	pluginIdElement.value = json.data.plugin_id;
}

async function generatePluginIdAsync(ev: Event) {
	const element = ev.target as HTMLButtonElement;
	try {
		element.disabled = true;

		await generatePluginIdCoreAsync();
	} catch (ex) {
		console.error(ex);
		alert(ex);
	} finally {
		element.disabled = false;
	}
}

function register() {
	const autoGenElement = document.getElementById(
		"pg-plugin-id-auto-generate",
	);
	if (autoGenElement) {
		autoGenElement.addEventListener("click", generatePluginIdAsync, false);
	}
}

export function boot() {
	register();
}
