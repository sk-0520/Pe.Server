const cssSetting = {
	$_minWidthTablet: 768,
	$_minWidthComputer: 980,
};

export const mediaSet = {
	phone: `(max-width: ${cssSetting.$_minWidthTablet - 1}px)`,
	tablet: `(min-width: ${cssSetting.$_minWidthTablet}px) and (max-width: ${cssSetting.$_minWidthComputer - 1}px)`,
	computer: `(min-width: ${cssSetting.$_minWidthComputer}px)`,
} as const;
