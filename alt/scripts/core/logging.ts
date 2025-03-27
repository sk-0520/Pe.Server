export interface Logger {
	// biome-ignore lint/suspicious/noExplicitAny: console.trace
	trace(message?: any, ...optionalParams: any[]): void;
	// biome-ignore lint/suspicious/noExplicitAny: console.debug
	debug(message?: any, ...optionalParams: any[]): void;
	// biome-ignore lint/suspicious/noExplicitAny: console.info
	info(message?: any, ...optionalParams: any[]): void;
	// biome-ignore lint/suspicious/noExplicitAny: console.warn
	warn(message?: any, ...optionalParams: any[]): void;
	// biome-ignore lint/suspicious/noExplicitAny: console.error
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

	// biome-ignore lint/suspicious/noExplicitAny: console.trace
	public trace(...args: any[]): void {
		console.trace(this.name, ...args);
	}
	// biome-ignore lint/suspicious/noExplicitAny: console.debug
	public debug(...args: any[]): void {
		console.debug(this.name, ...args);
	}
	// biome-ignore lint/suspicious/noExplicitAny: console.info
	public info(...args: any[]): void {
		console.log(this.name, ...args);
	}
	// biome-ignore lint/suspicious/noExplicitAny: console.warn
	public warn(...args: any[]): void {
		console.warn(this.name, ...args);
	}
	// biome-ignore lint/suspicious/noExplicitAny: console.error
	public error(...args: any[]): void {
		console.error(this.name, ...args);
	}
}
