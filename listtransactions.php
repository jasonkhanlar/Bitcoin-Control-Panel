<?php
	//error_reporting(-1); ini_set("display_errors", 1);
	require_once "include.php";
	if (isset($_GET["account"])) {
		// Check if account exists
		$exists = FALSE;
		try { $accounts = $btcconn->listaccounts(); } catch (Exception $e) { btcerr($e); }
		if (array_key_exists($_GET["account"], $accounts)) $exists = TRUE;
		
		if (!$exists) die("Account doesn't exist");

		try {
			$transactions = $btcconn->listtransactions($_GET["account"],-1);	
		} catch (Exception $e) { btcerr($e); }
	} else die();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <style type="text/css">
	html, body, form { height: 100%; width: 100%; }
	body { font-family: monospace; }
	a:link, a:visited { text-decoration: none; }
	a:hover { text-decoration: underline; }
	table.accounts td.account { text-align: right; white-space: nowrap; }
  </style>
  <title>Bitcoin addresses by account</title>
 </head>
 <body>
  <h1>Bitcoin accounts</h1>
  <h2><?php echo $_GET["account"]; ?></h2>
  <table class="accounts">
   <tr><th>category</th><th>amount</th><th>confirmations</td><th>txid</th><th>time</th></tr>
<?php
	foreach ($transactions as $transaction) {
		echo "<tr>";
		foreach ($transaction as $var => $val) if ($var != "account") echo "<td>".$val."</td>";
		echo "</tr>\n";
	}
?>
<?php foreach ($addresses as $address) { ?>
   <tr><td><?php echo $address; ?></td></tr>
<?php } ?>
  </table>
 </body>
</html>