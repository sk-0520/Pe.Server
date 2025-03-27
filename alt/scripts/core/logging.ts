export interface Logger {
	trace(message?: any, ...optionalParams: any[]): void;
	debug(message?: any, ...optionalParams: any[]): void;
	info(message?: any, ...optionalParams: any[]): void;
	warn(message?: any, ...optionalParams: any[]): void;
	error(message?: any, ...optionalParams: any[]): void;
}

export function create(name: string): Logger {
	return new LoggerImpl(name);
}

class LoggerImpl implements Logger {
	private _name: string;

	constructor(name: string) {
		this._name = name;
	}

	private get name() {
		return `[${this._name}]`;
	}

	public trace(...args: any[]): void {
		console.trace(this.name, ...args);
	}
	public debug(...args: any[]): void {
		console.debug(this.name, ...args);
	}
	public info(...args: any[]): void {
		console.log(this.name, ...args);
	}
	public warn(...args: any[]): void {
		console.warn(this.name, ...args);
	}
	public error(...args: any[]): void {
		console.error(this.name, ...args);
	}
}
