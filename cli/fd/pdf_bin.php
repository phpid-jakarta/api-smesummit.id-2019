<?php

if (!isset($argv[1], $argv[2])) {
	printf("argv[1] and argv[2] are needed!\n");
	exit(1);
}

if (!file_exists($argv[1])) {
	printf("File {$argv[1]} does not exist\n");
	exit(1);
}


$u = json_decode($argv[2], true);
$hash = $u["hash"];

require __DIR__."/../../bootstrap/init.php";
require __DIR__."/../../vendor/autoload.php";

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML(file_get_contents($argv[1]));
ob_start();
$mpdf->Output();
file_put_contents(
	BASEPATH."/storage/tickets/pdf/{$u["ticket_code"]}_{$hash}.pdf",
	ob_get_clean()
);
printf("Generated!\n");