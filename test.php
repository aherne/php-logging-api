<?php
require __DIR__ . '/vendor/autoload.php';
try {
    if (!file_exists("/var/log/syslog")) {
        throw new Exception("Syslog not installed in: /var/log/syslog");
    }
    new Lucinda\UnitTest\ConsoleController("unit-tests.xml", "local", ($argv[1] ?? null));
} catch (\Throwable $e) {
    echo "ERROR: ".$e->getMessage().PHP_EOL;
    echo "TRACE: ".$e->getTraceAsString();
}
