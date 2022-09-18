import * as default_loader from '../default_loader';
import * as management_crash_report_list from '../domain/management/crash_report_list'


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	management_crash_report_list.boot();
});
