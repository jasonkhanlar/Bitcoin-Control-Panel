<?php
	//error_reporting(-1); ini_set("display_errors", 1);
	require_once "include.php";
	try { $transactions = $btcconn->listtransactions("*",-1); } catch (Exception $e) { btcerr($e); }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <script src="jquery-1.4.4.min.js" type="text/javascript"></script>
  <script type="text/javascript">
	$(document).ready(function() {
		$("span.transactions_type").click(function(){
			var type = $(this).text();
			$("table.transactions tbody tr").each(function(){
				if (type == "Sent/Received") $(this).show();
				else if (type == "Received")
					if ($(this).children("td.credit").text() != "") $(this).show(); else $(this).hide();
				else if (type == "Sent")
					if ($(this).children("td.debit").text() != "") $(this).show(); else $(this).hide();
			});
		});
	});
  </script>
  <style type="text/css">
	html, body, form { height: 100%; width: 100%; }
	body { font-family: monospace; }
	a:link, a:visited { text-decoration: none; }
	a:hover { text-decoration: underline; }
	span.transactions_type { cursor: pointer; float: left; margin: 0 5px; }
	table.transactions td { padding: 0 5px; }
	table.transactions td.credit { text-align: right; }
	table.transactions td.debit { text-align: right; }
  </style>
  <title>Bitcoin transactions</title>
 </head>
 <body>
  <h1>Bitcoin transactions</h1>
  <span class="transactions_type sentreceived">Sent/Received</span>
  <span class="transactions_type sent">Sent</span>
  <span class="transactions_type received">Received</span>
  <br/>
  <table class="transactions">
   <thead><tr><th>confirmations</th><th>date</th><th>description</th><th>credit</th><th>debit</th><th>fee</th><th>txid</th></tr></thead>
   <tbody>
<?php
	$transactions = array_reverse($transactions);
	foreach ($transactions as $transaction) {
		$amount = ""; $fee = ""; $confirmations = ""; $txid = ""; $time = "";
		foreach ($transaction as $var => $val) $$var=$val;
		$amount = number_format($amount, 2, ".", ",");
?>
    <tr>
     <td class="confirmations"><?php echo $confirmations; ?> confirmations</td>
     <td class="date"><?php echo date("m/d/Y H:i:s", $time); ?></td>
     <td class="description">
<?php
	try { $account = $btcconn->getaccount($address); } catch (Exception $e) { btcerr($e); }
	if ($category == "receive") echo "Received with: $address ($account)";
	else echo "To: $address";
?>
     </td>
     <td class="credit"><?php echo $category == "receive" ? "+".$amount : ""; ?></td>
     <td class="debit"><?php echo $category == "send" ? $amount : ""; ?></td>
     <td class="fee"><?php echo $fee; ?></td>
     <td class="txid"><?php echo $txid; ?></td>
    </tr>
<?php } ?>
   </tbody>
  </table>
 </body>
</html>