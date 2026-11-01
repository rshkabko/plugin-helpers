<?php

namespace Flamix\Plugin\Tests\Unit;

use Flamix\Plugin\Queue\Interfaces\ShouldQueue;
use Flamix\Plugin\Queue\JobCommands;
use Flamix\Plugin\Queue\SQL;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    public function test_sql_builds_prepared_queries()
    {
        $sql = $this->sql();

        $this->assertSame("SELECT id FROM jobs WHERE order_id='5' LIMIT 1;", $sql->select(['order_id' => 5], ['id'], 1));
        $this->assertSame("INSERT INTO jobs (order_id, order_job_status, updated_at, created_at)\n                    VALUES ('5', 'NEW', NOW(), NOW());", $sql->insert(5, 'NEW'));
        $this->assertSame("UPDATE jobs SET attempts=attempts+1,updated_at=NOW(),order_job_status='SENT' WHERE id='3';",
            $sql->update(['attempts' => 'increment', 'updated_at' => 'now', 'order_job_status' => 'SENT'], ['id' => 3]));
        $this->assertSame("UPDATE jobs SET reserved_at=NOW() WHERE order_id IN (1,'a\\'b');", $sql->updateWhereIn(['reserved_at' => 'now'], 'order_id', [1, "a'b"]));
        $this->assertSame('DELETE FROM jobs WHERE 1=1;', $sql->clear());
    }

    public function test_sql_sets_zero_and_true_as_values_not_as_increment()
    {
        $this->assertSame("UPDATE jobs SET attempts='0',sent='1' WHERE id='3';", $this->sql()->update(['attempts' => 0, 'sent' => true], ['id' => 3]));
    }

    public function test_sql_where_with_two_columns()
    {
        $this->markTestIncomplete('Bug: src/Queue/SQL.php:187 joins WHERE conditions with "," instead of AND, so select([a => 1, b => 2]) is invalid SQL.');
        $this->assertSame("SELECT * FROM jobs WHERE a='1' AND b='2' ;", $this->sql()->select(['a' => 1, 'b' => 2]));
    }

    public function test_job_commands_update_an_existing_job_and_read_its_status()
    {
        $jobs = new class($this->sql()) extends JobCommands implements ShouldQueue {
            protected string $success = 'SENT';
            public array $queries = [];
            private SQL $sql;

            public function __construct(SQL $sql)
            {
                $this->sql = $sql;
            }

            public function sqlClosure(): SQL
            {
                return $this->sql;
            }

            public function query(string $query)
            {
                $this->queries[] = $query;
                return strpos($query, 'SELECT') === 0 ? [(object) ['id' => 9, 'order_job_status' => 'SENT']] : [];
            }
        };

        $this->assertSame(9, $jobs->createOrUpdate(5, ['order_job_status' => 'SENT']));
        $this->assertSame("UPDATE jobs SET order_job_status='SENT' WHERE id='9';", $jobs->queries[1]);
        $this->assertTrue($jobs->isSentSuccessfully(5));
        $this->assertSame(2, $jobs->lock([5, 6]));
    }

    private function sql(): SQL
    {
        // Like $wpdb->prepare(): only %s is quoted (an already quoted '%s' too), %d stays as written.
        return new SQL('jobs', function (string $query, ...$args) {
            $query = str_replace(["'%s'", '%s'], ['%s', "'%s'"], $query);
            return vsprintf($query, array_map(fn($v) => is_string($v) ? addslashes($v) : $v, $args));
        });
    }
}
