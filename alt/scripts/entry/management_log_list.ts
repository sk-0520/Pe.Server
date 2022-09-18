import * as default_loader from '../default_loader';
import * as management_log_list from '../domain/management/log_list';


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	management_log_list.boot();
});
