<?php

$port = "8080";
$docRoot = __DIR__."/public";

$fileDescriptor = [
	["pipe", "r"],
	["file", "php://stdout", "w"],
	["file", "php://stdout", "w"]
];

$docRoot = escapeshellarg($docRoot);

$res = proc_open(PHP_BINARY." -S 0.0.0.0:{$port} -t {$docRoot}", $fileDescriptor, $pipes);
proc_close($res);
