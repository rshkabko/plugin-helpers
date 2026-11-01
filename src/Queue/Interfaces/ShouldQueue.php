<?php

namespace Flamix\Plugin\Queue\Interfaces;

use Flamix\Plugin\Queue\SQL;

/** What a JobCommands subclass must provide: the SQL builder and a way to run a query. */
interface ShouldQueue
{
    public function sqlClosure(): SQL;
    /** Rows as objects; JobCommands reads ['0']->id and ['0']->order_job_status. */
    public function query(string $query);
}