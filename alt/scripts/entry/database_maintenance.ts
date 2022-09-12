import * as default_loader from '../default_loader';
import * as database_list from '../domain/database_list'


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	database_list.boot();
});
