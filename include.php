<?php
	require_once "config.php";
	require_once "jsonRPCClient.php";
	function btcerr($e) { file_put_contents($CONFIG["error_log_file"], time()."\n$e\n\n", FILE_APPEND | LOCK_EX); echo $CONFIG["error_message"]; exit; }
	$btcuser = implode("", file($CONFIG["bitcoin_user_file"], FILE_IGNORE_NEW_LINES));
	$btcpass = implode("", file($CONFIG["bitcoin_pass_file"], FILE_IGNORE_NEW_LINES));
	$btcconn = new jsonRPCClient("http://$btcuser:$btcpass@127.0.0.1:8332");
?>