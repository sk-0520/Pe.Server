<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Sandbox: string
{
	case Default = '';
	case AllowForms = 'allow-forms';
	case AllowModals = 'allow-modals';
	case AllowOrientationLock = 'allow-orientation-lock';
	case AllowPointerLock = 'allow-pointer-lock';
	case AllowPopups = 'allow-popups';
	case AllowPopupsToEscapeSandbox = 'allow-popups-to-escape-sandbox';
	case AllowPresentation = 'allow-presentation';
	case AllowSameOrigin = 'allow-same-origin';
	case AllowScripts = 'allow-scripts';
	case AllowStorageAccessByUserActivation = 'allow-storage-access-by-user-activation';
	case AllowTopNavigation = 'allow-top-navigation';
	case AllowTopNavigationByUserActivation = 'allow-top-navigation-by-user-activation';
}
