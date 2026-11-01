<?php

namespace Flamix\Plugin\General;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use voku\helper\ASCII;
use Exception;

/** Laravel-style string helpers and the file logger behind flamix_log(). */
class Helpers
{
    /**
     * Host of a URL, with or without a scheme.
     *
     * @param string $url
     * @return string
     */
    public static function parseDomain(string $url): string
    {
        if (!str_contains($url, 'http'))
            $url = 'https://' . $url;

        $tmp = parse_url($url);
        return $tmp['host'] ?? '';
    }

    /**
     * @param string|bool|int $message Log message
     * @param array $context Data
     * @param string|null $chanel Folder
     * @return Logger
     * @throws Exception
     */
    public static function log($message, array $context = [], ?string $chanel = null): Logger
    {
        $date = date('Y-m-d');
        if (!defined('FLAMIX_LOGS_PATH'))
            throw new Exception('Logs path not defined, use setLogsPath() when init plugin!');

        $log = new Logger('commerceml');
        $log->pushHandler(new StreamHandler(FLAMIX_LOGS_PATH . '/logs/' . ($chanel ? $chanel . '/' : '') . $date . '-info-' . md5($date) . '.log', Logger::DEBUG));
        $log->info($message, $context);
        return $log;
    }

    /**
     * Laravel slug style.
     * 
     * @param string $title
     * @param string $separator - OR _
     * @return string
     */
    public static function slug(string $title, string $separator = '-'): string
    {
        $title = ASCII::to_ascii($title, 'en');

        $flip = $separator === '-' ? '_' : '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));

        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Laravel limit style.
     * 
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }
}