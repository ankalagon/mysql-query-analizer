<?php

use Garden\Cli\Cli;
use Garden\Cli\LogFormatter;
use MysqlQueryAnalizer\Decorators\AvailableDecorators;
use MysqlQueryAnalizer\Decorators;

require_once 'vendor/autoload.php';

$cli = new Cli();
$cli->description('Mysql Query Profiling Tool')
    ->opt('host:h', 'Host of database (default localhost)')
    ->opt('port:P', 'Port number to use (default 3306)', false, 'integer')
    ->opt('user:u', 'User for login.', true)
    ->opt('password:p', 'Password to use when connecting to database.', true)
    ->opt('database:d', 'The name of the database to use.', true)
    ->opt('format:f', sprintf('Format to output (default: %s), available formats: %s', AvailableDecorators::getDecorators()[0], implode(',', AvailableDecorators::getDecorators())))
    ->opt('query:q', 'Query to analyze', true)
    ->opt('verbose:v', 'Set additional information about handlers', false, 'boolean', 'v');

$logFormatter = new LogFormatter();

$args = $cli->parse($argv, true);

$host = $args->getOpt('host');
$port = $args->getOpt('port');
$user = $args->getOpt('user');
$password = $args->getOpt('password');
$database = $args->getOpt('database');
$query = $args->getOpt('query');
$format = $args->getOpt('format');
$verbose = $args->getOpt('verbose');

try {
    if (!$format) {
        $format = AvailableDecorators::getDecorators()[0];
    } elseif (!in_array($format, AvailableDecorators::getDecorators())) {
        throw new \Exception(sprintf('Unknown decorator "%s", available decorators: %s', $format, implode(',', AvailableDecorators::getDecorators())));
    }

    $connection = new \MysqlQueryAnalizer\Connection($host, $port, $user, $password, $database);
    $connection->flushStatus();
    $profileData = $connection->showStatus($query);

    $decoratorEngine = Decorators\DecoratorFactory::create($format);
    $decorator = new \MysqlQueryAnalizer\Decorators\FromArrayDecorator($decoratorEngine);
    echo $decorator->toString($profileData);

    if ($verbose) {
        $handlerDecorator = new \MysqlQueryAnalizer\Decorators\HandlersDecorator(Decorators\DecoratorFactory::create('table'));
        echo $handlerDecorator->toString($profileData);
    }

} catch (\PDOException $e) {
    $logFormatter->error(sprintf('PDO error: %s', $e->getMessage()));
}