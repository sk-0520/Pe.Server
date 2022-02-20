import * as default_loader from '../default_loader';
import * as plugin_category_edit from '../domain/plugin_category_edit';

window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	plugin_category_edit.boot();
});
