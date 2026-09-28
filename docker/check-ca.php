<?php

$caPath = getenv('MYSQL_ATTR_SSL_CA');
if (!empty($caPath)) {
    if (!file_exists($caPath) || !is_readable($caPath)) {
        fwrite(STDERR, "Error: MySQL CA certificate at {$caPath} is not readable.\n");
        exit(1);
    }
    $content = file_get_contents($caPath);
    if (!str_contains($content, 'BEGIN CERTIFICATE')) {
        fwrite(STDERR, "Error: MySQL CA certificate at {$caPath} does not contain valid PEM certificate markers.\n");
        exit(1);
    }
    fwrite(STDOUT, "MySQL CA certificate verified successfully.\n");
}
exit(0);
