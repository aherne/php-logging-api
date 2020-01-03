# Logging API

This API is a very light weight logging system built on principles of simplicity and flexibility. Unlike Monolog, the industry standard in our days, it brings no tangible performance penalties and has near-zero learning curve just by keeping complexity to a minimum while offering you the ability to extend functionalities. In light of these ideas, the whole idea of logging is reduced to just three steps:

- **[configuration](#configuration)**: setting up an XML file where one or more loggers are set for each development environment
- **[initialization](#initialization)**: creating a [Lucinda\Logging\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/Wrapper.php) instance with above XML and current development environment then calling *getLogger()* method
- **[logging](#logging)**: use shared driver [Lucinda\Logging\Logger](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/Logger.php) returned by method above to log messages or exceptions/errors using methods named after SYSLOG priority

## Installation

This library is fully PSR-4 compliant and only requires PHP7.1+ interpreter. For installation run:

```console
composer require lucinda/logging
```

Then proceed with steps above and create a file (eg: index.php) in project root with following code:

```php
require_once("vendor/autoload.php");
$object = new Lucinda\Logging\Wrapper(simplexml_load_file(XML_FILE_NAME), DEVELOPMENT_ENVIRONMENT);
$logger = $object->getLogger();
// EXAMPLE: logs a "test" message with LOG_INFO priority
$logger->info("test");
```

### Unit Tests

API has 100% unit test coverage, but uses [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility. For tests and examples, check:

- [test.php](https://github.com/aherne/php-logging-api/blob/v3.0.0/test.php): runs unit tests in console
- [unit-tests.xml](https://github.com/aherne/php-logging-api/blob/v3.0.0/unit-tests.xml): sets up unit tests and mocks "loggers" tag
- [tests](https://github.com/aherne/php-logging-api/tree/v3.0.0/tests): unit tests for classes from [src](https://github.com/aherne/php-logging-api/tree/v3.0.0/src) folder
- [tests_drivers](https://github.com/aherne/php-logging-api/tree/v3.0.0/tests_drivers): unit tests for classes from [drivers](https://github.com/aherne/php-logging-api/tree/v3.0.0/drivers) folder

## Configuration

To configure this API you must have a XML with following tag:

```xml
<loggers path="PATH">
	<ENVIRONMENT>
		<logger class="CLASS" OPTIONS/>
		...
	</ENVIRONMENT>
	...
</loggers>
```

Where:

- **PATH**: (optional) folder of custom [Lucinda\Logging\AbstractLoggerWrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/AbstractLoggerWrapper.php) classes useful when developers desire to log using another mechanism than files/syslog already provided
- **ENVIRONMENT**: (mandatory) name of development environment (eg: local, dev or live). Note: a *loggers* tag can have multiple ENVIRONMENT subtags and latter can have multiple *logger* children!
- **CLASS**: (mandatory) full class name of [Lucinda\Logging\AbstractLoggerWrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/AbstractLoggerWrapper.php) implementation, encapsulating respective logger configuration. Available values:
    - [Lucinda\Logging\Driver\File\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/File/Wrapper.php): use this if you want to log to files
    - [Lucinda\Logging\Driver\SysLog\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/SysLog/Wrapper.php): use this if you want to log to syslog
    - *NAMESPACE\CLASS*: use this for your own custom logger identified by file found in PATH folder by same name as CLASS (see: [How to bind a new logger](#how-to-bind-a-new-logger))
- **OPTIONS**: (mandatory) attributes useful to configure respective logger:
    - If CLASS = [Lucinda\Logging\Driver\File\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/File/Wrapper.php), following tag attributes are avalable:
        - *path*: (mandatory) base name of file in which log is saved. Eg: "messages"
        - *rotation*: (optional) date algorithm to rotate log above. Eg: "Y-m-d"
        - *format*: (mandatory) controls what will be displayed in log line (see: [How log lines are formatted](#how-log-lines-are-formatted)). Eg: "%d %v %e %f %l %m %u %i %a"
    - If CLASS = [Lucinda\Logging\Driver\SysLog\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/SysLog/Wrapper.php), following attributes are available:
        - *application*: (mandatory) value that identifies your site against other syslog lines. Eg: "mySite"
        - *format*: (mandatory) controls what will be displayed in log line (see: [How log lines are formatted](#how-log-lines-are-formatted)). Eg: "%v %e %f %l %m %u %i %a"
    - Otherwise, tag attributes will depend on the logger you need to create. Their values are available from argument of **setLogger** method CLASS will need to implement. (see: [How to bind a new logger](#how-to-bind-a-new-logger))

Example:

```xml
<loggers>
    <local>
        <logger class="Lucinda\Logging\Driver\File\Wrapper" path="messages" format="%d %v %e %f %l %m %u %i %a" rotation="Y-m-d"/>
    </local>
    <live>
        <logger class="Lucinda\Logging\Driver\File\Wrapper" path="messages" format="%d %v %e %f %l %m %u %i %a" rotation="Y-m-d"/>
        <logger class="Lucinda\Logging\Driver\SysLog\Wrapper" application="unittest" format="%v %e %f %l %m %u %i %a"/>
    </live>
</loggers>
```

### How log lines are formatted

As one can see above, "logger" tags whose class is [Lucinda\Logging\Driver\File\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/File/Wrapper.php) and [Lucinda\Logging\Driver\SysLog\Wrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/drivers/SysLog/Wrapper.php) support a *format* attribute whose value can be a concatenation of:

- **%d**: current date using Y-m-d H:i:s format.
- **%v**: syslog priority level constant value matching to Logger method called.
- **%e**: name of thrown exception class ()
- **%f**: absolute location of file that logged message or threw a Throwable
- **%l**: line in file above where message was logged or Throwable/Exception was thrown
- **%m**: value of logged message or Throwable message
- **%e**: class name of Throwable, if log origin was a Throwable
- **%u**: value of URL when logging occurred, if available (value of $_SERVER["REQUEST_URI"])
- **%a**: value of USER AGENT header when logging occurred, if available (value of $_SERVER["HTTP_USER_AGENT"])
- **%i**: value of IP  when logging occurred, if available (value of $_SERVER["REMOTE_ADDR"])

### How to bind a new logger

Let us assume you want to bind a new SQL logger to this API. First you need to implement the logger itself, which must extend [Lucinda\Logging\Logger](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/Logger.php) and implement its required **log** method:

```php
class SQLLogger extends Lucinda\Logging\Logger
{
    private $schema;
    private $table;

    public function __construct(string $schema, string $table)
    {
        $this->schema = $schema;
        $this->table = $table;
    }

    protected function log($info, int $level): void
    {
        // log in sql database based on schema, table, info and level
    }
}
```

Now you need to bind logger above to XML configuration. To do so you must create another class extending [Lucinda\Logging\AbstractLoggerWrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/AbstractLoggerWrapper.php) and implement its required **setLogger** method:

```php
require_once("SQLLogger.php");

class SQLLoggerWrapper extends Lucinda\Logging\AbstractLoggerWrapper
{
    protected function setLogger(\SimpleXMLElement $xml): Logger
    {
        $schema = (string) $xml["schema"];
        $table = (string) $xml["table;
        return new SQLLogger($schema, $table);
    }
}
```

Assuming both classes above are found in *foo/bar* folder relative to project root you finally need to bind class above to XML:

```xml
<loggers path="foo/bar">
    <local>
        <logger class="SQLLoggerWrapper" table="logs" schema="logging_local"/>
    </local>
    <live>
        <logger class="SQLLoggerWrapper" table="logs" schema="logging_production"/>
    </live>
</loggers>
```
## Initialization

Now that XML is configured, you can get a logger to save and use later on whenever needed:

```php
$object = new Lucinda\Logging\Wrapper(simplexml_load_file(XML_FILE_NAME), DEVELOPMENT_ENVIRONMENT);
$logger = $object->getLogger();
```

Logger returned is a [Lucinda\Logging\Logger](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/Logger.php) that hides complexity of logger(s) underneath through a common interface centered on logging operations. 

**NOTE**: because XML parsing is somewhat costly, it is recommended to save $logger object and reuse it throughout application lifecycle.

## Logging

Once you saved and stored $logger object obtained above, you are able to perform logging via [Lucinda\Logging\Logger](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/Logger.php) methods:

- *emergency(\Throwable $exception)*: logs a \Throwable using **LOG_EMERG** priority
- *alert(\Throwable $exception)*:  logs a \Throwable using **LOG_ALERT** priority
- *critical(\Throwable $exception)*:  logs a \Throwable using **LOG_CRIT** priority
- *error(\Throwable $exception)*:  logs a \Throwable using **LOG_ERR** priority
- *warning(string $message)*: logs a message using **LOG_WARNING** priority
- *notice(string $message)*: logs a message using **LOG_NOTICE** priority
- *debug(string $message)*: logs a message using **LOG_DEBUG** priority
- *info(string $message)*: logs a message using **LOG_INFO** priority