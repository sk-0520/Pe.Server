import * as default_loader from '../default_loader';
import * as plugin_edit from '../domain/plugin_edit';
import * as markdown_edit from '../domain/markdown_edit';

window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	plugin_edit.boot();
	markdown_edit.boot('.markdown-editor');
});
