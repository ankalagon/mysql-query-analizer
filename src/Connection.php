<?php

namespace MysqlQueryAnalizer;

class Connection
{
    /**
     * default port to use
     */
    const port = 3306;

    /**
     * default host to use
     */
    const host = 'localhost';

    /**
     * @var null| \PDO
     */
    private $_connection = null;

    function __construct($hostname, $port, $user, $password, $database)
    {
        if (!$hostname) {
            $hostname = self::host;
        }

        if (!$port) {
            $port = self::port;
        }

        $this->_connection = new \PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s', $hostname, $port, $database),
            $user,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    /**
     * switch on profiling
     * @return bool
     */
    public function switchOnProfiling()
    {
        return (bool) $this->_connection->query('SET profiling=1');
    }

    /**
     * Flush status from mysql stats
     * @return bool
     */
    public function flushStatus()
    {
        return (bool) $this->_connection->query('FLUSH STATUS');
    }

    public function showStatus($query)
    {
        $this->_connection->query($query);
        $result = $this->_connection->query('SHOW STATUS WHERE `variable_name` LIKE \'Handler%\' OR `variable_name` LIKE \'Created%\'');

        $tmpData = [];
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $tmpData[] = $row;
        }

        return $tmpData;

    }

    public function profileQuery($query)
    {
        $this->_connection->query($query);
        $result = $this->_connection->query($this->_profileQuery(1));

        $tmpData = [];
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $tmpData[] = $row;
        }

        return $tmpData;
    }

    private function _profileQuery($number = 1)
    {
        return sprintf('
          SELECT
              STATE as State, 
              SUM(DURATION) as TotalTime, 
              round(SUM(DURATION) / (
                  SELECT 
                    SUM(DURATION) 
                  FROM
                    information_schema.PROFILING AS TMP_PROFILING
                  WHERE 
                    TMP_PROFILING.QUERY_ID=PROFILING.QUERY_ID) * 100, 2) AS Percentage, 
              COUNT(*) AS Calls, 
              ROUND(SUM(DURATION)/COUNT(*), 6) AS AvarageTime, 
              ROUND(100 * STD(DURATION)/SUM(DURATION), 2) AS St_Dev  
          FROM 
              information_schema.PROFILING 
          WHERE
              QUERY_ID=%s 
          GROUP BY 
              STATE
          ORDER BY 
              TotalTime DESC', $number);
    }
}