<?php

require __DIR__."/../bootstrap/init.php";

$u = [
	"email" => "ammarfaizi2@gmail.com",
	"phone" => "085867152777",
	"name" => "Ammar Faizi",
	"position" => "Owner",
	"ticket_price" => "3000000"
];


$to = $u["email"];
$subject = "SME Summit 2019 - Payment Instructions Email";
ob_start();
require BASEPATH."/mail_templates/payment_instruction.php";
$message = ob_get_clean();

// To send HTML mail, the Content-type header must be set
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/html; charset=iso-8859-1";

// Additional headers
$headers[] = "To: {$u["name"]} <{$u["email"]}>";
$headers[] = "From: Payment SME SUMMIT <payment@smesummit.id>";

// Mail it
$q = mail($to, $subject, $message, implode("\r\n", $headers));
var_dump($q);