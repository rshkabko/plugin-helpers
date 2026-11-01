# Queue

A table of order jobs. `SQL` builds the queries, your `JobCommands` subclass runs them.

## Job class

```php
use Flamix\Plugin\Queue\SQL;
use Flamix\Plugin\Queue\JobCommands;
use Flamix\Plugin\Queue\Interfaces\ShouldQueue;

class Order extends JobCommands implements ShouldQueue
{
    protected string $success = 'SENT';   // required: isSentSuccessfully() compares the status to it

    public function sqlClosure(): SQL
    {
        global $wpdb;
        return new SQL($wpdb->prefix . 'flamix_order_jobs', fn($query, ...$args) => $wpdb->prepare($query, ...$args));
    }

    public function query(string $query)
    {
        global $wpdb;
        return $wpdb->get_results($query); // rows as objects: JobCommands reads [0]->id, [0]->order_job_status
    }
}
```

## Usage

```php
$jobs = new Order();
$jobs->createQueueTableIfNotExist();        // CREATE TABLE when DESCRIBE returns nothing

$jobs->create($orderId);                    // status 'NEW'; returns query(INSERT)[0]->id ?? 0, so always 0 with $wpdb->get_results
$jobs->createOrUpdate($orderId, [           // updates the found job and returns its id; a new one is created as 'NEW' without $data and returns create()'s 0
    'order_job_status' => 'SENT',
    'attempts' => 'increment',              // attempts=attempts+1
    'updated_at' => 'now',                  // updated_at=NOW()
]);
$jobs->lock([$orderId, 15]);                // reserved_at = updated_at = NOW(); returns 2
$jobs->isSentSuccessfully($orderId);        // status === $success
```

## SQL

```php
$sql = (new Order())->sqlClosure();

$sql->select(['order_id' => 5], ['id'], 1); // SELECT id FROM <table> WHERE order_id='5' LIMIT 1;
$sql->notSending('SENT');                   // not 'SENT' and not reserved in the last 60 s, newest 20
$sql->notSending('SENT', 300, 50);          // columns: id, order_id, reservet_at_timestamp (sic)
$sql->update(['order_job_status' => 'NEW'], ['id' => 3]);
$sql->updateWhereIn(['order_job_status' => 'NEW'], 'order_id', [1, 2, 3]);
$sql->clear(3);                             // DELETE ... WHERE id='3'
$sql->clear();                              // no id: DELETE every row
```

Only one column per `$where`: several are joined with "," and give invalid SQL.
