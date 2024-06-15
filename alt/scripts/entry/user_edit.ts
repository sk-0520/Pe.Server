import * as default_loader from "../default_loader";
import * as markdown_edit from "../domain/markdown_edit";

window.addEventListener("DOMContentLoaded", (ev) => {
	default_loader.boot();
	markdown_edit.boot(".markdown-editor");
});
