# Logging API

Small logging package for PHP applications that want a simple XML-configured logger facade with pluggable drivers.

The package gives you:

- one common `Lucinda\Logging\Logger` API
- XML wiring for one or more loggers per environment
- a `MultiLogger` that forwards each log call to all configured drivers
- built-in drivers for files, JSON files, PHP error log, stdout/stderr, syslog, and no-op logging

## Supported Drivers

| Driver | Wrapper Class | Output |
| --- | --- | --- |
| File | `Lucinda\Logging\Driver\File\Wrapper` | formatted text lines in rotating `.log` files |
| Json | `Lucinda\Logging\Driver\Json\Wrapper` | newline-delimited JSON in rotating `.json` files |
| ErrorLog | `Lucinda\Logging\Driver\ErrorLog\Wrapper` | PHP native `error_log()` |
| Stream | `Lucinda\Logging\Driver\Stream\Wrapper` | `php://stdout` or `php://stderr` |
| SysLog | `Lucinda\Logging\Driver\SysLog\Wrapper` | syslog |
| Null | `Lucinda\Logging\Driver\Null\Wrapper` | discards all log calls |

## Installation

```console
composer require lucinda/logging
```

Requirements:

- PHP 8.1+
- `ext-SimpleXML`

## Runtime Flow

Create a `RequestInformation` object, load XML, build the wrapper, then keep and reuse the returned logger:

```php
require __DIR__."/vendor/autoload.php";

$requestInformation = new Lucinda\Logging\RequestInformation();
$requestInformation->setUri($_SERVER["REQUEST_URI"] ?? "");
$requestInformation->setIpAddress($_SERVER["REMOTE_ADDR"] ?? "");
$requestInformation->setUserAgent($_SERVER["HTTP_USER_AGENT"] ?? "");

$xml = simplexml_load_file("configuration.xml");
$wrapper = new Lucinda\Logging\Wrapper($xml, $requestInformation, "local");
$logger = $wrapper->getLogger();

$logger->info("application started");
```

The logger exposes RFC5424/syslog-style levels:

| Method | Argument | Level |
| --- | --- | --- |
| `emergency` | `Throwable $exception` | `LOG_EMERG` |
| `alert` | `Throwable $exception` | `LOG_ALERT` |
| `critical` | `Throwable $exception` | `LOG_CRIT` |
| `error` | `Throwable $exception` | `LOG_ERR` |
| `warning` | `string $message` | `LOG_WARNING` |
| `notice` | `string $message` | `LOG_NOTICE` |
| `debug` | `string $message` | `LOG_DEBUG` |
| `info` | `string $message` | `LOG_INFO` |

Severe methods receive `Throwable` instances. Message-oriented methods receive strings.

## Configuration

Configuration lives under a root containing `loggers`, then one child tag per environment:

```xml
<xml>
    <loggers>
        <local>
            <logger class="Lucinda\Logging\Driver\File\Wrapper"
                    path="/var/log/my-app/messages"
                    format="%d %v %e %f %l %m %u %i %a"
                    rotation="Y-m-d"/>
        </local>
        <live>
            <logger class="Lucinda\Logging\Driver\Stream\Wrapper"
                    stream="php://stdout"
                    format="%d %v %m"/>
            <logger class="Lucinda\Logging\Driver\Json\Wrapper"
                    path="/var/log/my-app/events"
                    rotation="Y-m-d"/>
        </live>
    </loggers>
</xml>
```

`Wrapper` reads only the requested environment:

```php
$wrapper = new Lucinda\Logging\Wrapper($xml, $requestInformation, "live");
```

Each `logger` tag must provide a `class` attribute containing an `AbstractLoggerWrapper` implementation. Driver-specific attributes are documented below.

## Built-In Drivers

### File

Writes formatted text lines to disk.

```xml
<logger class="Lucinda\Logging\Driver\File\Wrapper"
        path="/var/log/my-app/messages"
        format="%d %v %m"
        rotation="Y-m-d"/>
```

Attributes:

- `path`: required; file path without extension
- `format`: required; text format pattern
- `rotation`: optional; PHP `date()` pattern appended as `__{date}`

Output file names end in `.log`. With `path="/var/log/app"` and `rotation="Y-m-d"`, the output is `/var/log/app__2026-06-05.log`.

### Json

Writes newline-delimited JSON objects to disk.

```xml
<logger class="Lucinda\Logging\Driver\Json\Wrapper"
        path="/var/log/my-app/events"
        rotation="Y-m-d"/>
```

Attributes:

- `path`: required; file path without extension
- `rotation`: optional; PHP `date()` pattern appended as `__{date}`

Output file names end in `.json`. Each line contains `timestamp`, `level`, and `message`. Exception logs also include an `exception` object with `class`, `file`, `line`, and `trace`. Available request metadata is added as `uri`, `ip_address`, and `user_agent`.

### ErrorLog

Writes formatted text lines through PHP's native `error_log()`.

```xml
<logger class="Lucinda\Logging\Driver\ErrorLog\Wrapper"
        format="%d %v %m"/>
```

Attributes:

- `format`: required; text format pattern

### Stream

Writes formatted text lines to process streams. This driver intentionally accepts only `php://stdout` and `php://stderr`.

```xml
<logger class="Lucinda\Logging\Driver\Stream\Wrapper"
        stream="php://stdout"
        format="%d %v %m"/>
```

Attributes:

- `stream`: required; either `php://stdout` or `php://stderr`
- `format`: required; text format pattern

This is the best fit for containerized applications where the platform collects stdout/stderr.

### SysLog

Writes formatted text lines to syslog.

```xml
<logger class="Lucinda\Logging\Driver\SysLog\Wrapper"
        application="my-app"
        format="%v %m"/>
```

Attributes:

- `application`: required; name passed to `openlog()`
- `format`: required; text format pattern

### Null

Discards all log calls.

```xml
<logger class="Lucinda\Logging\Driver\Null\Wrapper"/>
```

This is useful for disabled logging or tests that need a real logger object without output.

## Text Formatting

Text-based drivers use `Lucinda\Logging\LogFormatter`. The `format` attribute can contain:

| Placeholder | Value |
| --- | --- |
| `%d` | current date as `Y-m-d H:i:s` |
| `%v` | numeric log level |
| `%e` | exception class, for `Throwable` logs |
| `%f` | exception file, or caller file when available for string logs |
| `%l` | exception line, or caller line when available for string logs |
| `%m` | message text or exception message |
| `%u` | request URI from `RequestInformation` |
| `%i` | IP address from `RequestInformation` |
| `%a` | user agent from `RequestInformation` |

Example:

```xml
<logger class="Lucinda\Logging\Driver\File\Wrapper"
        path="/var/log/my-app/messages"
        format="%d %v %m %u %i"/>
```

## Request Information

`RequestInformation` is optional metadata used by formatters and JSON logs:

```php
$requestInformation = new Lucinda\Logging\RequestInformation();
$requestInformation->setUri("/account");
$requestInformation->setIpAddress("127.0.0.1");
$requestInformation->setUserAgent("Mozilla/5.0");
```

Unset fields remain `null` and are skipped by JSON output. In text formats, placeholders are replaced only when matching metadata exists.

## Custom Drivers

A custom driver needs two classes:

- a logger extending `Lucinda\Logging\Logger`
- a wrapper extending `Lucinda\Logging\AbstractLoggerWrapper`

Example logger:

```php
namespace App\Logging;

class DatabaseLogger extends \Lucinda\Logging\Logger
{
    protected function log(string|\Throwable $info, int $level): void
    {
        $message = $info instanceof \Throwable ? $info->getMessage() : $info;

        // Persist $level and $message wherever your application needs.
    }
}
```

Example XML wrapper:

```php
namespace App\Logging;

class DatabaseLoggerWrapper extends \Lucinda\Logging\AbstractLoggerWrapper
{
    protected function setLogger(\SimpleXMLElement $xml): \Lucinda\Logging\Logger
    {
        return new DatabaseLogger();
    }
}
```

Configuration:

```xml
<logger class="App\Logging\DatabaseLoggerWrapper"/>
```

## Testing

This repository uses `lucinda/unit-testing`.

```console
php test.php
```

Tests for `src/` live in `tests/`. Tests for built-in drivers live in `tests_drivers/`.
