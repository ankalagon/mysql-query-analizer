<?php

namespace MysqlQueryAnalizer\Decorators;

class HandlersDecorator
{
    private $_engine = null;

    public function __construct(DecoratorInterface $engine)
    {
        $this->_engine = $engine;
    }

    public function toString($data)
    {
        $data = $this->_proceed($data);
        $this->_engine->setData($data);

        return $this->_engine->render();
    }

    private function _proceed($data)
    {
        $tmp = [];
        $handlers = $this->_handlersDescriptions();
        foreach ($data as $row) {
            if ($row['Value'] > 0 && isset($handlers[$row['Variable_name']])) {
                $tmp[] = [
                    'name' => $row['Variable_name'],
                    'description' => $handlers[$row['Variable_name']],
                    'count' => $row['Value']
                ];
            }
        }

        return $tmp;
    }

    private function _handlersDescriptions()
    {
        return [
            'Handler_commit' => 'The number of internal COMMIT statements.',
            'Handler_delete' => 'The number of times that rows have been deleted from tables.',
            'Handler_external_lock' => 'The server increments this variable for each call to its external_lock() function, which generally occurs at the beginning and end of access to a table instance. There might be differences among storage engines. This variable can be used, for example, to discover for a statement that accesses a partitioned table how many partitions were pruned before locking occurred: Check how much the counter increased for the statement, subtract 2 (2 calls for the table itself), then divide by 2 to get the number of partitions locked.',
            'Handler_mrr_init' => 'The number of times the server uses a storage engine\'s own Multi-Range Read implementation for table access.',
            'Handler_prepare' => 'A counter for the prepare phase of two-phase commit operations.',
            'Handler_read_first' => 'The number of times the first entry in an index was read. If this value is high, it suggests that the server is doing a lot of full index scans; for example, SELECT col1 FROM foo, assuming that col1 is indexed.',
            'Handler_read_key' => 'The number of requests to read a row based on a key. If this value is high, it is a good indication that your tables are properly indexed for your queries.',
            'Handler_read_last' => 'The number of requests to read the last key in an index. With ORDER BY, the server will issue a first-key request followed by several next-key requests, whereas with ORDER BY DESC, the server will issue a last-key request followed by several previous-key requests.',
            'Handler_read_next'=> 'The number of requests to read the next row in key order. This value is incremented if you are querying an index column with a range constraint or if you are doing an index scan.',
            'Handler_read_prev'=> 'The number of requests to read the previous row in key order. This read method is mainly used to optimize ORDER BY ... DESC.',
            'Handler_read_rnd'=> 'The number of requests to read a row based on a fixed position. This value is high if you are doing a lot of queries that require sorting of the result. You probably have a lot of queries that require MySQL to scan entire tables or you have joins that do not use keys properly.',
            'Handler_read_rnd_next' => 'The number of requests to read the next row in the data file. This value is high if you are doing a lot of table scans. Generally this suggests that your tables are not properly indexed or that your queries are not written to take advantage of the indexes you have.',
            'Handler_rollback'=> 'The number of requests for a storage engine to perform a rollback operation.',
            'Handler_savepoint' => 'The number of requests for a storage engine to place a savepoint.',
            'Handler_savepoint_rollback' => 'The number of requests for a storage engine to roll back to a savepoint.',
            'Handler_update' => 'The number of requests to update a row in a table.',
            'Handler_write' => 'The number of requests to insert a row in a table.'
        ];
    }

}