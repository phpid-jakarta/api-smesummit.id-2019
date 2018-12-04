<?php

declare(ticks=1);

$port = "8080";
$docRoot = __DIR__."/public";
// $extArgv = "";
$extArgv = "-d extension=".__DIR__."/shared_objects/apismesummit_ext1.so";

function deletePidFile()
{
	@unlink(__DIR__."/php_server.pid");
}

if (function_exists("pcntl_signal")) {
	pcntl_signal(SIGINT, "deletePidFile");
	pcntl_signal(SIGTERM, "deletePidFile");
}

$fileDescriptor = [
	["pipe", "r"],
	["file", "php://stdout", "w"],
	["file", "php://stdout", "w"]
];

file_put_contents(__DIR__."/php_server.pid", getmypid());

$docRoot = escapeshellarg($docRoot);
$cmd = PHP_BINARY." {$extArgv} -S 0.0.0.0:{$port} -t {$docRoot}";
print $cmd.PHP_EOL.PHP_EOL;
$res = proc_open($cmd, $fileDescriptor, $pipes);
proc_close($res);
