import * as default_loader from '../default_loader';
import * as management_plugin_category_edit from '../domain/management/plugin_category_edit';

window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	management_plugin_category_edit.boot();
});
