<?php

namespace Flamix\Plugin\Queue;

/** Builds the raw SQL of a jobs table; most values go through the prepare closure (e.g. $wpdb->prepare). */
class SQL
{
    private string $table;
    private $prepareClosure;

    /**
     * @param  string  $table  Table name
     * @param  callable  $prepareClosure  Functions to clear SQL injections (->prepare($sql))
     */
    public function __construct(string $table, callable $prepareClosure)
    {
        $this->table = $table;
        $this->prepareClosure = $prepareClosure;
    }

    /**
     * Security prepared SQL.
     *
     * @param ...$args
     * @return string
     */
    private function prepare(...$args): string
    {
        return call_user_func($this->prepareClosure, ...$args);
    }

    /**
     * Table name; each job type needs its own table.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return $this->table;
    }

    /**
     * Create table for Queue.
     *
     * @return string
     */
    public function createTable(): string
    {
        return "CREATE TABLE {$this->getTableName()} (
                    id bigint(20) NOT NULL AUTO_INCREMENT,
                    order_id bigint(20) NOT NULL,
                    order_job_status varchar(20) NOT NULL,
                    data text DEFAULT '',
                    attempts tinyint(3) NOT NULL DEFAULT 0,
                    reserved_at datetime DEFAULT NULL,
                    updated_at datetime NOT NULL,
                    created_at datetime NOT NULL,
                PRIMARY KEY (id));";
    }

    /**
     * Use to check is table exist.
     *
     * @return string
     */
    public function describeTable(): string
    {
        return "DESCRIBE {$this->getTableName()}";
    }

    /**
     * Select Queue data.
     *
     * @param  array  $where
     * @param  array  $fields
     * @param  int|null  $limit
     * @return string
     */
    public function select(array $where = [], array $fields = [], ?int $limit = null): string
    {
        $where = $this->arrayToSQL($where, ' AND ');
        $fields = (empty($fields)) ? '*' : implode(',', $fields);
        $limit = ($limit > 0) ? "LIMIT {$limit}" : '';

        return "SELECT {$fields} FROM {$this->getTableName()} WHERE {$where} {$limit};";
    }

    /**
     * Jobs not in the $success status and not reserved in the last $block seconds.
     *
     * @param  string  $success
     * @param  int  $block  Seconds
     * @param  int  $limit
     * @return string
     */
    public function notSending(string $success, int $block = 60, int $limit = 20): string
    {
        $sql = "SELECT id, order_id, UNIX_TIMESTAMP(reserved_at) as reservet_at_timestamp
                    FROM {$this->getTableName()}
                        WHERE order_job_status != '%s' AND (reserved_at IS NULL OR reserved_at < NOW() - INTERVAL %d SECOND)
                            ORDER BY id DESC
                                LIMIT %d;";

        return $this->prepare($sql, $success, $block, $limit);
    }

    /**
     * Insert JOB to Queue.
     *
     * @param  int  $order_id
     * @param  string  $order_job_status
     * @return string
     */
    public function insert(int $order_id, string $order_job_status): string
    {
        $sql = "INSERT INTO {$this->getTableName()} (order_id, order_job_status, updated_at, created_at)
                    VALUES ('%d', '%s', NOW(), NOW());";

        return $this->prepare($sql, $order_id, $order_job_status);
    }

    /**
     * Update Queue JOB.
     *
     * @param  array  $fields
     * @param  array  $where
     * @return string
     */
    public function update(array $fields, array $where): string
    {
        $fields = $this->arrayToSQL($fields);
        $where = $this->arrayToSQL($where, ' AND ');
        return "UPDATE {$this->getTableName()} SET {$fields} WHERE {$where};";
    }

    /**
     * Massive update Queue JOB.
     *
     * @param  array  $fields  What to update, example: ['order_job_status' => 'NEW']
     * @param  string  $column  Column name to filter, example: 'order_id'
     * @param  array  $where_in  Values to filter, example: [1, 2, 3]
     * @return string
     */
    public function updateWhereIn(array $fields, string $column, array $where_in): string
    {
        $fields = $this->arrayToSQL($fields);
        $where = implode(',', array_map(fn($v) => is_numeric($v) ? $v : "'".addslashes($v)."'", $where_in));

        return "UPDATE {$this->getTableName()} SET {$fields} WHERE {$column} IN ({$where});";
    }

    /**
     * Delete JOB from DB.
     *
     * @param  int  $id
     * @return string
     */
    public function clear(int $id = 0): string
    {
        $where = $this->arrayToSQL($id ? ['id' => $id] : [], ' AND ');
        return "DELETE FROM {$this->getTableName()} WHERE {$where};";
    }

    /** Secure convert ['key' => 'value'] to key='value', joined by $glue: "," for SET, " AND " for WHERE. */
    private function arrayToSQL(array $where, string $glue = ','): string
    {
        if (empty($where)) {
            return '1=1';
        }

        foreach ($where as $key => &$value) {
            // Strict: a loose switch matched true (and 0 on PHP 7.4) to 'increment'.
            if ($value === 'increment') {
                $value = "{$key}={$key}+1";
            } elseif ($value === 'now') {
                $value = "{$key}=NOW()";
            } else {
                $value = $this->prepare("{$key}=%s", $value);
            }
        }

        return implode($glue, $where);
    }
}