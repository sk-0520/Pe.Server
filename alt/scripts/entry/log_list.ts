import * as default_loader from '../default_loader';
import * as application_log from '../domain/application_log';


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	application_log.boot();
});
