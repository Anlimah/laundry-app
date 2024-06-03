<?php

namespace Core;

class Status
{
    public static function isInformational($code)
    {
        return $code >= 100 && $code <= 199;
    }
    public static function isSuccess($code)
    {
        return $code >= 200 && $code <= 299;
    }

    public static function isRedirect($code)
    {
        return $code >= 300 && $code <= 399;
    }

    public static function isClientError($code)
    {
        return $code >= 400 && $code <= 499;
    }

    public static function isServerError($code)
    {
        return $code >= 500 && $code <= 599;
    }

    public static int $HTTP_100_CONTINUE = 100;
    public static int $HTTP_101_SWITCHING_PROTOCOLS = 101;
    public static int $HTTP_102_PROCESSING = 102;
    public static int $HTTP_103_EARLY_HINTS = 103;
    public static int $HTTP_200_OK = 200;
    public static int $HTTP_201_CREATED = 201;
    public static int $HTTP_202_ACCEPTED = 202;
    public static int $HTTP_203_NON_AUTHORITATIVE_INFORMATION = 203;
    public static int $HTTP_204_NO_CONTENT = 204;
    public static int $HTTP_205_RESET_CONTENT = 205;
    public static int $HTTP_206_PARTIAL_CONTENT = 206;
    public static int $HTTP_207_MULTI_STATUS = 207;
    public static int $HTTP_208_ALREADY_REPORTED = 208;
    public static int $HTTP_226_IM_USED = 226;
    public static int $HTTP_300_MULTIPLE_CHOICES = 300;
    public static int $HTTP_301_MOVED_PERMANENTLY = 301;
    public static int $HTTP_302_FOUND = 302;
    public static int $HTTP_303_SEE_OTHER = 303;
    public static int $HTTP_304_NOT_MODIFIED = 304;
    public static int $HTTP_305_USE_PROXY = 305;
    public static int $HTTP_306_RESERVED = 306;
    public static int $HTTP_307_TEMPORARY_REDIRECT = 307;
    public static int $HTTP_308_PERMANENT_REDIRECT = 308;
    public static int $HTTP_400_BAD_REQUEST = 400;
    public static int $HTTP_401_UNAUTHORIZED = 401;
    public static int $HTTP_402_PAYMENT_REQUIRED = 402;
    public static int $HTTP_403_FORBIDDEN = 403;
    public static int $HTTP_404_NOT_FOUND = 404;
    public static int $HTTP_405_METHOD_NOT_ALLOWED = 405;
    public static int $HTTP_406_NOT_ACCEPTABLE = 406;
    public static int $HTTP_407_PROXY_AUTHENTICATION_REQUIRED = 407;
    public static int $HTTP_408_REQUEST_TIMEOUT = 408;
    public static int $HTTP_409_CONFLICT = 409;
    public static int $HTTP_410_GONE = 410;
    public static int $HTTP_411_LENGTH_REQUIRED = 411;
    public static int $HTTP_412_PRECONDITION_FAILED = 412;
    public static int $HTTP_413_REQUEST_ENTITY_TOO_LARGE = 413;
    public static int $HTTP_414_REQUEST_URI_TOO_LONG = 414;
    public static int $HTTP_415_UNSUPPORTED_MEDIA_TYPE = 415;
    public static int $HTTP_416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public static int $HTTP_417_EXPECTATION_FAILED = 417;
    public static int $HTTP_418_IM_A_TEAPOT = 418;
    public static int $HTTP_421_MISDIRECTED_REQUEST = 421;
    public static int $HTTP_422_UNPROCESSABLE_ENTITY = 422;
    public static int $HTTP_423_LOCKED = 423;
    public static int $HTTP_424_FAILED_DEPENDENCY = 424;
    public static int $HTTP_425_TOO_EARLY = 425;
    public static int $HTTP_426_UPGRADE_REQUIRED = 426;
    public static int $HTTP_428_PRECONDITION_REQUIRED = 428;
    public static int $HTTP_429_TOO_MANY_REQUESTS = 429;
    public static int $HTTP_431_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public static int $HTTP_451_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public static int $HTTP_500_INTERNAL_SERVER_ERROR = 500;
    public static int $HTTP_501_NOT_IMPLEMENTED = 501;
    public static int $HTTP_502_BAD_GATEWAY = 502;
    public static int $HTTP_503_SERVICE_UNAVAILABLE = 503;
    public static int $HTTP_504_GATEWAY_TIMEOUT = 504;
    public static int $HTTP_505_HTTP_VERSION_NOT_SUPPORTED = 505;
    public static int $HTTP_506_VARIANT_ALSO_NEGOTIATES = 506;
    public static int $HTTP_507_INSUFFICIENT_STORAGE = 507;
    public static int $HTTP_508_LOOP_DETECTED = 508;
    public static int $HTTP_509_BANDWIDTH_LIMIT_EXCEEDED = 509;
    public static int $HTTP_510_NOT_EXTENDED = 510;
    public static int $HTTP_511_NETWORK_AUTHENTICATION_REQUIRED = 511;
}
