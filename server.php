<?php

declare(ticks=1);

function deletePidFile()
{
	@unlink(__DIR__."/php_server.pid");
}

if (function_exists("pcntl_signal")) {
	pcntl_signal(SIGINT, "deletePidFile");
	pcntl_signal(SIGTERM, "deletePidFile");
}

$port = "8080";
$docRoot = __DIR__."/public";

$fileDescriptor = [
	["pipe", "r"],
	["file", "php://stdout", "w"],
	["file", "php://stdout", "w"]
];

file_put_contents(__DIR__."/php_server.pid", getmypid());

$docRoot = escapeshellarg($docRoot);

$res = proc_open(PHP_BINARY." -S 0.0.0.0:{$port} -t {$docRoot}", $fileDescriptor, $pipes);
proc_close($res);
