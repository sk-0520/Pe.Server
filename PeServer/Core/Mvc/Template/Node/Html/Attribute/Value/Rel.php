<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Rel: string
{
	case Alternate = "alternate";
	case Author = "author";
	case Bookmark = "bookmark";
	case Canonical = "canonical";
	case CompressionDictionary = "compression-dictionary";
	case DnsPrefetch = "dns-prefetch";
	case External = "external";
	case Expect = "expect";
	case Help = "help";
	case Icon = "icon";
	case License = "license";
	case Manifest = "manifest";
	case Me = "me";
	case Modulepreload = "modulepreload";
	case Next = "next";
	case Nofollow = "nofollow";
	case Noopener = "noopener";
	case Noreferrer = "noreferrer";
	case Opener = "opener";
	case Pingback = "pingback";
	case Preconnect = "preconnect";
	case Prefetch = "prefetch";
	case Preload = "preload";
	case Prerender = "prerender";
	case Prev = "prev";
	case PrivacyPolicy = "privacy-policy";
	case Search = "search";
	case Stylesheet = "stylesheet";
	case Tag = "tag";
	case TermsOfService = "terms-of-service";
}
