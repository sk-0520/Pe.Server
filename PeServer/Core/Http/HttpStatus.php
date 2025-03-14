<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Collections\Arr;

/**
 * HTTPステータスコード。
 */
enum HttpStatus: int
{
	/** プログラム内部で使用するのみ, HTTPの舞台には登場しない。 */
	case None = 0;

	case Continue = 100;
	case SwitchingProtocols = 101;
	case Processing = 102;
	case EarlyHints = 103;

	case OK = 200;
	case Created = 201;
	case Accepted = 202;
	case NonAuthoritativeInformation = 203;
	case NoContent = 204;
	case ResetContent = 205;
	case PartialContent = 206;
	case MultiStatus = 207;
	case AlreadyReported = 208;
	case IMUsed = 226;

	case MultipleChoices = 300;
	case MovedPermanently = 301;
	case Found = 302;
	case SeeOther = 303;
	case NotModified = 304;
	case UseProxy = 305;
	case Unused306 = 306;
	case TemporaryRedirect = 307;
	case PermanentRedirect = 308;

	case BadRequest = 400;
	case Unauthorized = 401;
	case PaymentRequired = 402;
	case Forbidden = 403;
	case NotFound = 404;
	case MethodNotAllowed = 405;
	case NotAcceptable = 406;
	case ProxyAuthenticationRequired = 407;
	case RequestTimeout = 408;
	case Conflict = 409;
	case Gone = 410;
	case LengthRequired = 411;
	case PreconditionFailed = 412;
	case PayloadTooLarge = 413;
	case UriTooLong = 414;
	case UnsupportedMediaType = 415;
	case RangeNotSatisfiable = 416;
	case ExpectationFailed = 417;
	/** I'm a teapot */
	case IamTeapot = 418;
	case MisdirectedRequest = 421;
	case UnprocessableEntity = 422;
	case Locked = 423;
	case FailedDependency = 424;
	case TooEarly = 425;
	case UpgradeRequired = 426;
	case PreconditionRequired = 428;
	case TooManyRequests = 429;
	case RequestHeaderFieldsTooLarge = 431;

	case InternalServerError = 500;
	case NotImplemented = 501;
	case BadGateway = 502;
	case ServiceUnavailable = 503;
	case GatewayTimeout = 504;
	case HttpVersionNotSupported = 505;
	case VariantAlsoNegotiates = 506;
	case InsufficientStorage = 507;
	case LoopDetected = 508;
	case NotExtended = 510;
	case NetworkAuthenticationRequired = 511;

	#region function

	/**
	 *
	 * @return bool
	 * @phpstan-pure
	 */
	public function isError(): bool
	{
		return 400 <= $this->value;
	}

	/**
	 *
	 * @return bool
	 * @phpstan-pure
	 */
	public function isRedirect(): bool
	{
		$codes = [
			300,
			301,
			302,
			303,
			304,
			307,
			308,
		];

		return Arr::in($codes, $this->value);
	}

	#endregion
}
