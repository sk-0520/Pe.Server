import * as default_loader from '../default_loader';
import * as log_list from '../domain/log_list';


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	log_list.boot();
});
