import * as default_loader from '../default_loader';
import * as management_version from '../domain/management/version'


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	management_version.boot();
});
