<?php

require __DIR__."/../bootstrap/init.php";

if (!isset($_GET["table"])) {
	header("Content-Type: text/plain");
	http_response_code(400);
	print "Table parameter required!";
	exit;
}

if (!in_array($_GET["table"], ["participants", "volunteers", "sponsors", "coachers", "speakers"])) {
	header("Content-Type: text/plain");
	http_response_code(400);
	print "Invalid table \"{$_GET["table"]}\"";
	exit;
}

if (isset($_GET["action"])) {
	switch ($_GET["action"]) {
		case "truncate":
				$st = DB::pdo()->exec("TRUNCATE TABLE `{$_GET["table"]}`");
				unset($st);
				header("Location: ?table={$_GET["table"]}");
			break;
		
		default:
			break;
	}
	exit;
}


$st = DB::pdo()->prepare("SELECT * FROM `{$_GET["table"]}`;");
$st->execute();

header("Content-Type: text/html");

$first = $st->fetch(PDO::FETCH_ASSOC);

?><!DOCTYPE html>
<html>
<head>
	<title>Table `<?php print $_GET["table"]; ?>`</title>
	<style type="text/css">
		* {
			font-family: Arial;
		}
		button {
			cursor: pointer;
		}
	</style>
</head>
<body>
	<center>
		<a href="?table=<?php print $_GET["table"]; ?>&amp;action=truncate"><button>Reset Table</button></a>
		<?php if ($first) { ?>
			<table border="1" style="border-collapse: collapse; margin-top: 10px;">
			<thead>
				<tr>
					<th style="padding: 5px;">No.</th>
				<?php
				$f = "<tr><td align=\"center\">1.</td>";
				foreach ($first as $key => $value) {
					$value = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
					$f .= "<td style=\"padding: 5px;\" align=\"center\">{$value}</td>";
					?><th style="padding: 5px;"><?php print $key; ?></th><?php
				}
				unset($first, $key, $value);
				?>
				</tr>
			</thead>
			<tbody>
			<?php print $f; unset($f); $i = 2;
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				print "<tr><td align=\"center\">{$i}.</td>";
				foreach ($r as $v) {
					$v = htmlspecialchars($v, ENT_QUOTES, "UTF-8");
					print "<td style=\"padding: 5px;\" align=\"center\">{$v}</td>";
				}
				$i++;
				print "</tr>";
				flush();
			}
			unset($r, $st, $i, $v);
			?>
			</tbody>
			</table>
		<?php } else { ?>
			<h1>There is no record on this table.</h1>
		<?php } ?>
	</center>
</body>
</html>
