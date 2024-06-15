import * as regex from "../../scripts/core/regex";

describe("regex", () => {
	test.each([
		["\\*", "*"],
		["\\.", "."],
		["\\*\\*", "**"],
	])("escape", (expected: string, pattern: string) => {
		expect(regex.escape(pattern)).toBe(expected);
	});
});
