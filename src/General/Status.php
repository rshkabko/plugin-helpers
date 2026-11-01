<?php

namespace Flamix\Plugin\General;

/** Status codes as upper snake case keys. */
class Status
{
    /** "Sent to CRM" -> "SENT_TO_CRM", cut to 60 characters. */
    public static function key(string $key): string
    {
        $key = Helpers::slug($key, '_');
        return strtoupper(Helpers::limit($key, 60, ''));
    }
}
