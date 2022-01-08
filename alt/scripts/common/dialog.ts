import * as common from './common';

export enum ButtonType {
	/**
	 * 閉じるボタンのみ。
	 * 背景押下でも閉じる。
	 * */
	Close,
	/**
	 * ボタン無し。
	 * 背景押下は無反応。
	 */
	None,
	/**
	 * 肯定・否定ボタン。
	 * 背景押下は否定ボタンと同じ。
	 */
	YesNo,
}

/**
 * ダイアログ操作。
 */
export enum DialogAction {
	/**
	 * 否定。
	 */
	Negative,
	/**
	 * 肯定。
	 */
	Positive,
}

/**
 * ダイアログ処理結果。
 */
export interface DialogResult<T> {
	/**
	 * 操作。
	 */
	action: DialogAction,
	/**
	 * データ。
	 */
	data: T | null,
}

export type DisplayFactory<T> = ((() => Node)) | ((closer: (dialogResult: DialogResult<T>) => void) => Node);

export interface DialogSetting<T> {
	/** ボタン種別 */
	button: ButtonType,
	/** 表示要素 */
	display: string | HTMLElement | DisplayFactory<T>,
}



export class Dialog<T> {
	readonly setting: DialogSetting<T>;

	private _callerFocusElement: HTMLElement | null = null;

	private _displayParentElement: HTMLElement | null = null;
	private _contentElement: Node | null = null;

	private _dialogElement: HTMLElement;

	private _prevStyle?: {
		body: {
			overflow: string,
		},
	};

	public texts = {
		autoBreak: true,
		button: {
			close: '閉じる',
			yes: 'はい',
			no: 'いいえ',
		}
	}

	private readonly _dialogEventName = 'dialog-submit';

	constructor(setting: DialogSetting<T>) {
		this.setting = setting;

		this._dialogElement = document.createElement('div');
		this._dialogElement.classList.add('pg-dialog');
		this._dialogElement.classList.add('dialog');
	}

	private isHtmlElement(arg: any): arg is HTMLElement {
		return arg && arg instanceof HTMLElement;
	}

	private isDisplayFactory(arg: any): arg is DisplayFactory<T> {
		return arg && arg.apply !== undefined;
	}

	private isString(arg: any): arg is String {
		return typeof arg == 'string';
	}

	/** タブ移動抑制 */
	private focusTrap(parentElement: HTMLElement) {
		this._callerFocusElement = document.activeElement as HTMLElement
		if (this._callerFocusElement) {
			this._callerFocusElement.blur();
		}

		// TAB移動可能要素
		const selector = [
			'a[href]:not([tabindex="-1"])',
			'area[href]:not([tabindex="-1"])',
			'input:not([disabled]):not([tabindex="-1"])',
			'select:not([disabled]):not([tabindex="-1"])',
			'textarea:not([disabled]):not([tabindex="-1"])',
			'button:not([disabled]):not([tabindex="-1"])',
			'iframe:not([tabindex="-1"])',
			'[tabindex]:not([tabindex="-1"])',
			'[contentEditable=true]:not([tabindex="-1"])',
		].join(',');
		const focusElements = Array.from(parentElement.querySelectorAll<HTMLElement>(selector))
			.sort(i => i.tabIndex)
			;

		if (focusElements.length) {
			const headElement = focusElements[0];
			const tailElement = focusElements[focusElements.length - 1];

			function focusTrapCore(ev: KeyboardEvent) {
				if (ev.key != 'Tab') {
					console.debug('タブ以外はブラウザに任せる');
					return;
				}

				if (ev.shiftKey) {
					// 戻る
					if (document.activeElement == headElement) {
						ev.preventDefault();
						tailElement.focus({ preventScroll: false, });
						return
					}
				} else {
					// 進む
					console.debug(document.activeElement);
					if (document.activeElement == tailElement) {
						ev.preventDefault();
						headElement.focus({ preventScroll: false, });
						return
					}
				}
				if (!focusElements.includes(document.activeElement as HTMLElement)) {
					console.debug('先頭強制選択: ' + ev.currentTarget);
					ev.preventDefault();
					headElement.focus({ preventScroll: false, });
					return;
				}
			}
			window.addEventListener('keydown', focusTrapCore, false);
		} else {
			parentElement.focus({ preventScroll: true, });
			window.addEventListener('keydown', ev => {
				if (ev.key == 'Tab') {
					ev.preventDefault();
				}
			}, false);
		}
	}

	private showCore(contentElement: Node) {
		// 背景
		const dialogBackgroundElement = document.createElement('div');
		dialogBackgroundElement.classList.add('background');

		if (this.setting.button != ButtonType.None) {
			dialogBackgroundElement.addEventListener('click', ev => this.executeClose({ action: DialogAction.Negative, data: null }, ev), false);
		} else {
			dialogBackgroundElement.classList.add('ignore-click');
		}

		// 前景
		const dialogForegroundElement = document.createElement('div');
		dialogForegroundElement.classList.add('foreground');
		dialogForegroundElement.addEventListener('click', ev => ev.stopPropagation(), false);

		dialogBackgroundElement.appendChild(dialogForegroundElement);

		// 表示要素設定箇所
		const dialogContentElement = document.createElement('div');
		dialogContentElement.classList.add('content');
		dialogContentElement.appendChild(contentElement);

		dialogForegroundElement.appendChild(dialogContentElement);

		// ボタン配置箇所
		if (this.setting.button != ButtonType.None) {
			const dialogButtonsElement = document.createElement('div');
			dialogButtonsElement.classList.add('buttons');

			switch (this.setting.button) {
				case ButtonType.Close: {
					const closeButtonElement = document.createElement('button');
					closeButtonElement.textContent = this.texts.button.close;
					closeButtonElement.classList.add('close');
					closeButtonElement.addEventListener('click', ev => this.executeClose({ action: DialogAction.Negative, data: null }, ev), false);

					dialogButtonsElement.appendChild(closeButtonElement);

					break;
				}

				case ButtonType.YesNo: {
					const yesButtonElement = document.createElement('button');
					yesButtonElement.textContent = this.texts.button.yes;
					yesButtonElement.classList.add('yes');
					yesButtonElement.addEventListener('click', ev => this.executeClose({ action: DialogAction.Positive, data: null }, ev), false);

					const noButtonElement = document.createElement('button');
					noButtonElement.textContent = this.texts.button.no;
					noButtonElement.classList.add('no');
					noButtonElement.addEventListener('click', ev => this.executeClose({ action: DialogAction.Negative, data: null }, ev), false);

					dialogButtonsElement.appendChild(yesButtonElement);
					dialogButtonsElement.appendChild(noButtonElement);
					break;
				}

				default:
					throw 'assert';
			}

			dialogForegroundElement.appendChild(dialogButtonsElement);
		}

		this.focusTrap(dialogForegroundElement);

		this._dialogElement.appendChild(dialogBackgroundElement);
		document.body.appendChild(this._dialogElement);
		this._prevStyle = {
			body: {
				overflow: document.body.style.overflow,
			}
		};
		document.body.style.overflow = 'hidden';
	}

	private executeClose(dialogResult: DialogResult<T>, ev: Event) {
		ev.preventDefault();
		this.close(dialogResult);
	}

	public showAsync(): Promise<DialogResult<T>> {
		if (this.isHtmlElement(this.setting.display)) {
			this._displayParentElement = this.setting.display.parentElement;
			this._contentElement = this.setting.display;
			this.setting.display.remove();
		} else if (this.isDisplayFactory(this.setting.display)) {
			this._contentElement = this.setting.display(this.close.bind(this));
		} else if (this.isString(this.setting.display)) {
			const messageElement = document.createElement('p');
			if (this.texts.autoBreak) {
				var lines = this.setting.display.split(/\r?\n/);
				for (let i = 0; i < lines.length; i++) {
					const line = lines[i];
					if (i) {
						const breakElement = document.createElement('br');
						messageElement.appendChild(breakElement);
					}
					const lineNode = document.createTextNode(line);
					messageElement.appendChild(lineNode);
				}
			} else {
				messageElement.textContent = this.setting.display;
			}
			this._contentElement = messageElement;
		} else {
			throw 'error' + JSON.stringify(this.setting);
		}

		this.showCore(this._contentElement);

		return new Promise<DialogResult<T>>((resolve, reject) => {
			this._dialogElement.addEventListener(this._dialogEventName, ev => {
				const ce = ev as CustomEvent<DialogResult<T>>;
				resolve(ce.detail);
			});
		}).finally(() => {

			if (this._displayParentElement) {
				const contentElement = this._contentElement as HTMLElement;
				contentElement.remove();
				this._displayParentElement.appendChild(contentElement!);
			}

			if (this._prevStyle) {
				document.body.style.overflow = this._prevStyle.body.overflow;
			}

			if (this._callerFocusElement) {
				this._callerFocusElement.focus({ preventScroll: true, });
			}

			this._dialogElement.remove();
			this._dialogElement.textContent = '';

		});
	}

	public close(dialogResult: DialogResult<T>) {
		var event = new CustomEvent<DialogResult<T>>(this._dialogEventName, { detail: dialogResult });
		this._dialogElement.dispatchEvent(event);
	}
}

/** 基本的にこれだけ使用しておけばOK */
export function showAsync<T>(setting: DialogSetting<T>): Promise<DialogResult<T>> {
	const dialog = new Dialog<T>(setting);
	return dialog.showAsync();
}

/** とりあえずダイアログが出ればいい程度の処理。 */
export function show(setting: DialogSetting<void>) {
	const dialog = new Dialog<void>(setting);
	dialog.showAsync();
}


/**
 * 待機中ダイアログを表示
 * @param busyTimeMs 表示するまでの時間(ミリ秒)
 */
export async function busyAsync(busyTimeMs: number): Promise<{ dialog: Dialog<void>, result: Promise<DialogResult<void>> }> {
	await common.sleepAsync(busyTimeMs);

	const dlg = new Dialog<void>({
		button: ButtonType.None,
		display: () => {
			const contentElement = document.createElement('div');
			contentElement.classList.add('busy');

			const iconElement = document.createElement('div');
			iconElement.classList.add('icon');

			const messageElement = document.createElement('p');
			messageElement.classList.add('message');

			contentElement.appendChild(iconElement);
			contentElement.appendChild(messageElement);

			return contentElement;
		},
	});

	const promise = dlg.showAsync();

	return Promise.resolve({
		dialog: dlg,
		result: promise,
	});
}
