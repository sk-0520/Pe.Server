
export function download(name: string, content: any) {
	const linkUrl = window.URL.createObjectURL(content);
	const anchorElement = document.createElement('a');
	anchorElement.download = name;
	anchorElement.href = linkUrl;
	anchorElement.style.display = 'none';
	anchorElement.click();
	anchorElement.remove();
}
