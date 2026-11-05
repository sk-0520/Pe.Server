<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

/**
 * BCP 47
 *
 * @see https://developer.mozilla.org/docs/Glossary/BCP_47_language_tag
 * @see https://datatracker.ietf.org/doc/html/rfc5646
 */
enum Bcp47: string
{
	case Afrikaans = 'af';
	case Arabic = 'ar';
	case Bengali = 'bn';
	case Bulgarian = 'bg';
	case Catalan = 'ca';
	case Chinese = 'zh';
	case Croatian = 'hr';
	case Czech = 'cs';
	case Danish = 'da';
	case Dutch = 'nl';
	case English = 'en';
	case Estonian = 'et';
	case Finnish = 'fi';
	case French = 'fr';
	case German = 'de';
	case Greek = 'el';
	case Gujarati = 'gu';
	case Hebrew = 'he';
	case Hindi = 'hi';
	case Hungarian = 'hu';
	case Icelandic = 'is';
	case Indonesian = 'id';
	case Irish = 'ga';
	case Italian = 'it';
	case Japanese = 'ja';
	case Kannada = 'kn';
	case Korean = 'ko';
	case Latvian = 'lv';
	case Lithuanian = 'lt';
	case Malay = 'ms';
	case Malayalam = 'ml';
	case Marathi = 'mr';
	case Norwegian = 'no';
	case Persian = 'fa';
	case Polish = 'pl';
	case Portuguese = 'pt';
	case Punjabi = 'pa';
	case Romanian = 'ro';
	case Russian = 'ru';
	case Serbian = 'sr';
	case Slovak = 'sk';
	case Slovenian = 'sl';
	case Spanish = 'es';
	case Swahili = 'sw';
	case Swedish = 'sv';
	case Tamil = 'ta';
	case Telugu = 'te';
	case Thai = 'th';
	case Turkish = 'tr';
	case Ukrainian = 'uk';
	case Urdu = 'ur';
	case Vietnamese = 'vi';

	case ArabicSaudiArabia = 'ar-SA';
	case ChineseChina = 'zh-CN';
	case ChineseHongKong = 'zh-HK';
	case ChineseSimplified = 'zh-Hans';
	case ChineseTaiwan = 'zh-TW';
	case ChineseTraditional = 'zh-Hant';
	case EnglishAustralia = 'en-AU';
	case EnglishCanada = 'en-CA';
	case EnglishUnitedKingdom = 'en-GB';
	case EnglishUnitedStates = 'en-US';
	case FrenchCanada = 'fr-CA';
	case FrenchFrance = 'fr-FR';
	case GermanAustria = 'de-AT';
	case GermanGermany = 'de-DE';
	case GermanSwitzerland = 'de-CH';
	case HindiIndia = 'hi-IN';
	case ItalianItaly = 'it-IT';
	case JapaneseJapan = 'ja-JP';
	case KoreanSouthKorea = 'ko-KR';
	case PortugueseBrazil = 'pt-BR';
	case PortuguesePortugal = 'pt-PT';
	case RussianRussia = 'ru-RU';
	case SerbianCyrillic = 'sr-Cyrl';
	case SerbianLatin = 'sr-Latn';
	case SpanishMexico = 'es-MX';
	case SpanishSpain = 'es-ES';

	case Undetermined = 'und';
	case MultipleLanguages = 'mul';
	case NoLinguisticContent = 'zxx';
}
