<?php
/**
 * Composer install wrapper to bypass SSL verification
 * This is a temporary workaround for firewall/antivirus SSL interception
 */

// Disable SSL verification for streams
$contextOptions = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
    ]
];

$streamContext = stream_context_get_default();
stream_context_set_option($streamContext, $contextOptions);

// Also set environment variables
putenv('COMPOSER_ALLOW_SUPERUSER=1');
putenv('COMPOSER_PROCESS_TIMEOUT=2000');

// Execute composer install
$command = 'composer install --no-interaction --prefer-dist 2>&1';
passthru($command, $exitCode);
exit($exitCode);
?>
