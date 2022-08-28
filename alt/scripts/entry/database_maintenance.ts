import * as default_loader from '../default_loader';

function attachTableButtons() {
	const tableButtonElements = document.querySelectorAll('#tables .pg-table');
	for (const tableButtonElement of tableButtonElements) {
		tableButtonElement.addEventListener('click', ev => {
			const tableButtonElement = ev.target as HTMLElement;
			const tableName = tableButtonElement.dataset['table'] ?? '';
			const columnsJson = tableButtonElement.dataset['columns'] ?? '';
			const columns = JSON.parse(columnsJson) as [];
			let sqlLines = [
				'select',
				'	' + columns.map(i => i['name']).join(', '),
				'from',
				'	' + tableName
			];
			if (columns.filter(i => i['pk']).length) {
				sqlLines.push('order by');
				sqlLines.push('	' + columns.filter(i => i['pk']).map(i => i['name']).join(', '));
			}

			const textAreaElement = document.getElementById('database_maintenance_statement') as HTMLTextAreaElement;
			textAreaElement.textContent = sqlLines.join("\r\n");
		});
	}
}

window.addEventListener('DOMContentLoaded', ev => {
	attachTableButtons();
	default_loader.boot();
});
