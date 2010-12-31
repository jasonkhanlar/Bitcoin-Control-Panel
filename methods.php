<?php
	//error_reporting(-1); ini_set("display_errors", 1);
	require_once "include.php";
	try { $generate = $btcconn->getgenerate(); } catch (Exception $e) { btcerr($e); }
	try { $methodslist = $btcconn->help(); } catch (Exception $e) { btcerr($e); }
	$methods = array(); $method = strtok($methodslist, "\n");
	while ($method !== false) { if (strpos($method, " ")) $method = substr($method, 0, strpos($method, " ")); $methods[] = $method; $method = strtok("\n"); }
	$cpus = shell_exec("grep processor /proc/cpuinfo|wc -l");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <script src="jquery-1.4.4.min.js" type="text/javascript"></script>
  <script type="text/javascript">
	var evt, tid = 0;
	function add(method, response) {
		response = decodeURIComponent(response).replace(/</g, "&lt;").replace(/>/g, "&gt;");
		var cmd = ""; if (response == "") return false;
		var method_array = method.split(" ");
		var method = method_array[0];
		if (method == "backupwallet") cmd = "backupwallet("+$("#backupwallet_destination").val()+")";
		else if (method == "getaccount") cmd = "getaccount("+$("#getaccount_address").val()+")";
		else if (method == "getaccountaddress") cmd = "getaccountaddress("+$("#getaccountaddress_account").val()+")";
		else if (method == "getaddressesbyaccount") cmd = "getaddressesbyaccount("+$("#getaddressesbyaccount_account").val()+")";
		else if (method == "getbalance") cmd = "getbalance("+$("#getbalance_account").val()+")";
		else if (method == "getblockcount") cmd = "getblockcount()";
		else if (method == "getblocknumber") cmd = "getblocknumber()";
		else if (method == "getconnectioncount") cmd = "getconnectioncount()";
		else if (method == "getdifficulty") cmd = "getdifficulty()";
		else if (method == "getgenerate") cmd = "getgenerate()";
		else if (method == "gethashespersec") cmd = "gethashespersec()";
		else if (method == "getinfo") cmd = "getinfo()";
		else if (method == "getnewaddress") cmd = "getnewaddress("+$("#getnewaddress_account").val()+")";
		else if (method == "getreceivedbyaccount") cmd = "getreceivedbyaccount("+$("#getreceivedbyaccount_account").val()+","+$("#getreceivedbyaccount_minconf").val()+")";
		else if (method == "getreceivedbyaddress") cmd = "getreceivedbyaddress("+$("#getreceivedbyaddress_address").val()+","+$("#getreceivedbyaddress_minconf").val()+")";
		else if (method == "gettransaction") cmd = "gettransaction("+$("#gettransaction_txid").val()+")";
		else if (method == "help") cmd = "help "+method_array.slice(1).join(" ");
		else if (method == "listaccounts") cmd = "listaccounts("+$("#listaccounts_minconf").val()+")";
		else if (method == "listreceivedbyaccount") cmd = "listreceivedbyaccount("+$("#listreceivedbyaccount_minconf").val()+","+$("#listreceivedbyaccount_includeempty")+")";
		else if (method == "listreceivedbyaddress") cmd = "listreceivedbyaddress("+$("#listreceivedbyaddress_minconf").val()+","+$("#listreceivedbyaddress_includeempty")+")";
		else if (method == "listtransactions") cmd = "listtransactions("+$("#listtransactions_account").val()+","+$("#listtransactions_count").val()+")";
		else if (method == "move") cmd = "move("+$("#move_fromaccount").val()+","+$("#move_toaccount").val()+","+$("#move_amount").val()+","+$("#move_minconf").val()+","+$("#move_comment").val()+")";
		else if (method == "sendfrom") cmd = "sendfrom("+$("#sendfrom_account").val()+","+$("#sendfrom_address").val()+","+$("#sendfrom_amount").val()+","+$("#sendfrom_minconf").val()+","+$("#sendfrom_comment").val()+","+$("#sendfrom_commentto").val()+")";
		else if (method == "sendtoaddress") cmd = "sendtoaddress("+$("#sendtoaddress_address").val()+","+$("#sendfrom_amount").val()+","+$("#sendfrom_comment").val()+","+$("#sendfrom_commentto").val()+")";
		else if (method == "setaccount") cmd = "setaccount("+$("#setaccount_address").val()+","+$("#setaccount_account").val()+")";
		else if (method == "setgenerate") cmd = "setgenerate("+$("#setgenerate_generate").val()+","+$("#setgenerate_genproclimit").val()+")";
		else if (method == "stop") cmd = "stop()";
		else if (method == "validateaddress") cmd = "validateaddress("+$("#validateaddress_address").val()+")";
		var html = "<div><div class='cmd'>&gt;<pre> "+cmd+"</pre></div><div class='response'><pre>"+response+"</pre></div></div>\n";
		$("div#output").prepend(html);
	}
	function addtimed(method, response) { clearTimeout(tid); tid = setTimeout('add("'+method+'", "'+encodeURIComponent(response)+'")', 50); }
	function exec(event, method) {
		var any = ["getaccount","getaddressesbyaccount","getbalance","getreceivedbyaccount","getreceivedbyaddress","gettransaction","listaccounts","listreceivedbyaccount","listreceivedbyaddress","listtransactions","validateaddress"];
		var enteronly = ["backupwallet","getaccountaddress","getnewaddress","move","sendfrom","sendtoaddress","setaccount"];
		if (event.type == "keyup") {
			if (event.keyCode) {
				if (event.altKey || event.ctrlKey) return false;
				var c = event.keyCode;
				if (any.indexOf(method) > -1) {
					var keys = [9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,44,45,113,114,115,119,120,121,122,123,144,145];
					if (keys.indexOf(c) > -1) return false;
				} else if (enteronly.indexOf(method) > -1) { var keys = [13]; if (keys.indexOf(c) == -1) return false; }
				else return false;
			} else return false;
		} else if (enteronly.indexOf(method) > -1) return false;
		$("div."+method+" input.method").each(function(index) {
			if ($(this).attr("class").split(" ").indexOf("required") > -1) {
				var attrs = $(this).attr("class").split(" ");
				var classes = ["method","required"];
				for (var i=0; i<classes.length; i++) if (attrs.indexOf(classes[i]) > -1) attrs.splice(attrs.indexOf(classes[i]),1);
				if ($(this).val() == "" || $(this).val() == attrs[0]) return false;
			}
			if ($(this).attr("class").split(" ").indexOf("optional") > -1) {
				var attrs = $(this).attr("class").split(" ");
				var classes = ["method","optional"];
				for (var i=0; i<classes.length; i++) if (attrs.indexOf(classes[i]) > -1) attrs.splice(attrs.indexOf(classes[i]),1);
				if (attrs[0] == "minconf" && !isNumeric($(this).val())) $(this).val(1);
			}
		});
		if (method == "backupwallet") $.ajax({type: "POST", url: "method.php", data: "method=backupwallet&destination="+$("#backupwallet_destination").val(), success: function(response){ addtimed("backupwallet", response); } });
		if (method == "getaccount") $.ajax({type: "POST", url: "method.php", data: "method=getaccount&address="+$("#getaccount_address").val(), success: function(response){ addtimed("getaccount", response); } });
		if (method == "getaccountaddress") $.ajax({type: "POST", url: "method.php", data: "method=getaccountaddress&account="+$("#getaccountaddress_account").val(), success: function(response){ addtimed("getaccountaddress", response); } });
		if (method == "getaddressesbyaccount") $.ajax({type: "POST", url: "method.php", data: "method=getaddressesbyaccount&account="+$("#getaddressesbyaccount_account").val(), success: function(response){ addtimed("getaddressesbyaccount", response); } });
		if (method == "getbalance") $.ajax({type: "POST", url: "method.php", data: "method=getbalance&account="+$("#getbalance_account").val(), success: function(response){ addtimed("getbalance", response); } });
		if (method == "getblockcount") $.ajax({type: "POST", url: "method.php", data: "method=getblockcount", success: function(response){ addtimed("getblockcount", response); } });
		if (method == "getblocknumber") $.ajax({type: "POST", url: "method.php", data: "method=getblocknumber", success: function(response){ addtimed("getblocknumber", response); } });
		if (method == "getconnectioncount") $.ajax({type: "POST", url: "method.php", data: "method=getconnectioncount", success: function(response){ addtimed("getconnectioncount", response); } });
		if (method == "getdifficulty") $.ajax({type: "POST", url: "method.php", data: "method=getdifficulty", success: function(response){ addtimed("getdifficulty", response); } });
		if (method == "getgenerate") $.ajax({type: "POST", url: "method.php", data: "method=getgenerate", success: function(response){ addtimed("getgenerate", response); } });
		if (method == "gethashespersec") $.ajax({type: "POST", url: "method.php", data: "method=gethashespersec", success: function(response){ addtimed("gethashespersec", response); } });
		if (method == "getinfo") $.ajax({type: "POST", url: "method.php", data: "method=getinfo", success: function(response){ addtimed("getinfo", response); } });
		if (method == "getnewaddress") $.ajax({type: "POST", url: "method.php", data: "method=getnewaddress&account="+$("#getnewaddress_account").val(), success: function(response){ addtimed("getnewaddress", response); } });
		if (method == "getreceivedbyaccount") $.ajax({type: "POST", url: "method.php", data: "method=getreceivedbyaccount&account="+$("#getreceivedbyaccount_account").val()+"&minconf="+$("#getreceivedbyaccount_minconf").val(), success: function(response){ addtimed("getreceivedbyaccount", response); } });
		if (method == "getreceivedbyaddress") $.ajax({type: "POST", url: "method.php", data: "method=getreceivedbyaddress&address="+$("#getreceivedbyaddress_address").val()+"&minconf="+$("#getreceivedbyaddress_minconf").val(), success: function(response){ addtimed("getreceivedbyaddress", response); } });
		if (method == "gettransaction") $.ajax({type: "POST", url: "method.php", data: "method=gettransaction&txid="+$("#gettransaction_txid").val(), success: function(response){ addtimed("gettransaction", response); } });
		if (method == "help") { var help = arguments[2] ? arguments[2] : ""; $.ajax({type: "POST", url: "method.php", data: "method=help&help="+help, success: function(response){ addtimed("help "+help, response); } }); }
		if (method == "listaccounts") $.ajax({type: "POST", url: "method.php", data: "method=listaccounts&minconf="+$("#listaccounts_minconf").val(), success: function(response){ addtimed("listaccounts", response); } });
		if (method == "listreceivedbyaccount") $.ajax({type: "POST", url: "method.php", data: "method=listreceivedbyaccount&minconf="+$("#listreceivedbyaccount_minconf").val()+"&includeempty="+$("#listreceivedbyaccount_includeempty").val(), success: function(response){ addtimed("listreceivedbyaccount", response); } });
		if (method == "listreceivedbyaddress") $.ajax({type: "POST", url: "method.php", data: "method=listreceivedbyaddress&minconf="+$("#listreceivedbyaddress_minconf").val()+"&includeempty="+$("#listreceivedbyaddress_includeempty").val(), success: function(response){ addtimed("listreceivedbyaddress", response); } });
		if (method == "listtransactions") $.ajax({type: "POST", url: "method.php", data: "method=listtransactions&account="+$("#listtransactions_account").val()+"&count="+$("#listtransactions_count").val(), success: function(response){ addtimed("listtransactions", response); } });
		if (method == "move") $.ajax({type: "POST", url: "method.php", data: "method=move&fromaccount="+$("#move_fromaccount").val()+"&toaccount="+$("#move_toaccount").val()+"&amount="+$("#move_amount").val()+"&minconf="+$("#move_minconf").val()+"&comment="+$("#move_comment").val(),success: function(response){ addtimed("move", response); } });
		if (method == "sendfrom") $.ajax({type: "POST", url: "method.php", data: "method=sendfrom&account="+$("#sendfrom_account").val()+"&address="+$("#sendfrom_address").val()+"&amount="+$("#sendfrom_amount").val()+"&minconf="+$("#sendfrom_minconf").val()+"&comment="+$("#sendfrom_comment").val()+"&commentto="+$("#sendfrom_commentto").val(),success: function(response){ addtimed("sendfrom", response); } });
		if (method == "sendtoaddress") $.ajax({type: "POST", url: "method.php", data: "method=sendtoaddress&address="+$("#sendtoaddress_address").val()+"&amount="+$("#sendtoaddress_amount").val()+"&comment="+$("#sendtoaddress_comment").val()+"&commentto="+$("#sendtoaddress_commentto").val(),success: function(response){ addtimed("sendtoaddress", response); } });
		if (method == "setaccount") $.ajax({type: "POST", url: "method.php", data: "method=setaccount&address="+$("#setaccount_address").val()+"&account="+$("#setaccount_account").val(), success: function(response){ addtimed("setaccount", response); } });
		if (method == "setgenerate") $.ajax({type: "POST", url: "method.php", data: "method=setgenerate&generate="+$("#setgenerate_generate").val()+"&genproclimit="+$("#setgenerate_genproclimit").val(), success: function(response){ addtimed("setgenerate", response); } });
		if (method == "stop") $.ajax({type: "POST", url: "method.php", data: "method=stop", success: function(response){ addtimed("stop", response); } });
		if (method == "validateaddress") $.ajax({type: "POST", url: "method.php", data: "method=validateaddress&address="+$("#validateaddress_address").val(), success: function(response){ addtimed("validateaddress", response); } });
	}
	function exectimed(t, event) {
		if (t == "#stop" && !confirm("Please confirm:\n\nStop Bitcoin daemon.")) return false;
		var allowemptystring = ["getaccountaddress","getaddressesbyaccount","getbalance"];
		if ((allowemptystring.indexOf($(t).attr("id").split("_")[0]) == -1 && $(t).val() != "") || allowemptystring.indexOf($(t).attr("id").split("_")[0]) != -1) exec(event, $(t).attr("id").split("_")[0]);
		var params = ["account","address","amount","comment","commentto","count","destination","fromaccount","minconf","toaccount","txid"];
		for (var i=0; i<params.length; i++) if ($(t).attr("class").split(" ").indexOf(params[i]) > -1) if ($(t).val() == params[i]) $(t).css("color", "#808080"); else $(t).css("color", "#000000");
	}
	// http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
	function isNumeric(input) { return (input - 0) == input && input.length > 0; }
	$(document).ready(function() {
		$(window).resize();
		$("div.method h4").click(function(event) { exec(event, "help", $(this).text()); });
		$("input.method").click(function(event) { if ($(this).attr("type") == "button") { evt = event; clearTimeout(tid); tid = setTimeout('exectimed("#'+$(this).attr("id")+'",evt);', 50); } });
		$("input.method").bind("paste", null, function(event) { evt = event; clearTimeout(tid); tid = setTimeout('exectimed("#'+$(this).attr("id")+'",evt);', 50); });
		$("input.method").each(function(index) {
			var params = ["account","address","amount","comment","commentto","count","destination","fromaccount","minconf","toaccount","txid"];
			for (var i=0; i<params.length; i++) if ($(this).attr("class").split(" ").indexOf(params[i]) > -1) $(this).val(params[i]).css("color", "#808080");
		});
		$("input.method").focus(function() {
			var params = ["account","address","amount","comment","commentto","count","destination","fromaccount","minconf","toaccount","txid"];
			for (var i=0; i<params.length; i++) if ($(this).attr("class").split(" ").indexOf(params[i]) > -1 && $(this).val() == params[i]) $(this).select();
		});
		$("input.method").keyup(function(event) { exectimed(this, event); });
		$("select.method").change(function(event) {
			if ($(this).attr("id") == "setgenerate_generate") {
				if ($(this).val() == "1" && $("#setgenerate_genproclimit").val() == "0") $("#setgenerate_genproclimit").val(-1);
				else if ($(this).val() == "0") $("#setgenerate_genproclimit").val(0);
			}
			else if ($(this).attr("id") == "setgenerate_genproclimit") {
				if ($(this).val() == "0") $("#setgenerate_generate").val("0");
				else $("#setgenerate_generate").val("1");
			}
			evt = event; exec(event, $(this).attr("id").split("_")[0]);
		});
	});
	$(window).resize(function() {
		$("#main").css("height", "50px");
		$("#main").css("height", $(document).height() - $("#header").height());
		$("#output").css("width", $(document).width() - $("#menu").width());
	});
  </script>
  <style type="text/css">
	html, body, form { height: 100%; width: 100%; }
	body { margin: 0; overflow: hidden; padding: 0; text-align: center; }
	body, input, textarea { font-family: monospace; }
	a:link, a:visited { text-decoration: none; }
	a:hover { text-decoration: underline; }
	div.block { border: 1px solid black; text-align: left; width: 298px; }
	div.block h4 { background-color: black; color: white; cursor: pointer; margin: 0; text-align: center; }
	div.cmd { background-color: black; color: white; padding-bottom: 2px; padding-top: 2px; }
	div#header { font-size: 2em; }
	div#menu { float: left; height: 100%; overflow: auto; width: 320px; }
	div#output { float: left; height: 100%; overflow: auto; text-align: left; }
	input { border: 0; border-bottom: 1px solid black; padding: 0; text-align: left; width: 100%; }
	pre { display: inline; margin: 0; }
  </style>
  <title>Bitcoin</title>
 </head>
 <body>
  <div id="header">Bitcoin</div>
  <div id="main">
   <div id="menu">
<?php if (in_array("backupwallet", $methods)) { ?>
    <div class="block method backupwallet"><h4>backupwallet</h4>
     <input class="destination method required" id="backupwallet_destination" type="text"/>
    </div>
<?php } if (in_array("getaccount", $methods)) { ?>
    <div class="block method getaccount"><h4>getaccount</h4>
     <input class="address method required" id="getaccount_address" type="text"/>
    </div>
<?php } if (in_array("getaccountaddress", $methods)) { ?>
    <div class="block method getaccountaddress"><h4>getaccountaddress</h4>
     <input class="account method required" id="getaccountaddress_account" type="text"/>
    </div>
<?php } if (in_array("getaddressesbyaccount", $methods)) { ?>
    <div class="block method getaddressesbyaccount"><h4>getaddressesbyaccount</h4>
     <input class="account method required" id="getaddressesbyaccount_account" type="text"/>
    </div>
<?php } if (in_array("getbalance", $methods)) { ?>
    <div class="block method getbalance"><h4>getbalance</h4>
     <input class="account method optional" id="getbalance_account" type="text"/>
    </div>
<?php } if (in_array("getblockcount", $methods)) { ?>
    <div class="block method getblockcount"><h4>getblockcount</h4>
     <input class="method" id="getblockcount" type="button" value="getblockcount"/>
    </div>
<?php } if (in_array("getblocknumber", $methods)) { ?>
    <div class="block method getblocknumber"><h4>getblocknumber</h4>
     <input class="method" id="getblocknumber" type="button" value="getblocknumber"/>
    </div>
<?php } if (in_array("getconnectioncount", $methods)) { ?>
    <div class="block method getconnectioncount"><h4>getconnectioncount</h4>
     <input class="method" id="getconnectioncount" type="button" value="getconnectioncount"/>
    </div>
<?php } if (in_array("getdifficulty", $methods)) { ?>
    <div class="block method getdifficulty"><h4>getdifficulty</h4>
     <input class="method" id="getdifficulty" type="button" value="getdifficulty"/>
    </div>
<?php } if (in_array("getgenerate", $methods)) { ?>
    <div class="block method getgenerate"><h4>getgenerate</h4>
     <input class="method" id="getgenerate" type="button" value="getgenerate"/>
    </div>
<?php } if (in_array("gethashespersec", $methods)) { ?>
    <div class="block method gethashespersec"><h4>gethashespersec</h4>
     <input class="method" id="gethashespersec" type="button" value="gethashespersec"/>
    </div>
<?php } if (in_array("getinfo", $methods)) { ?>
    <div class="block method getinfo"><h4>getinfo</h4>
     <input class="method" id="getinfo" type="button" value="getinfo"/>
    </div>
<?php } if (in_array("getnewaddress", $methods)) { ?>
    <div class="block method getnewaddress"><h4>getnewaddress</h4>
     <input class="account method optional" id="getnewaddress_account" type="text"/>
    </div>
<?php } if (in_array("getreceivedbyaccount", $methods)) { ?>
    <div class="block method getreceivedbyaccount"><h4>getreceivedbyaccount</h4>
     <input class="account method required" id="getreceivedbyaccount_account" type="text"/>
     <input class="minconf method optional" id="getreceivedbyaccount_minconf" type="text"/>
    </div>
<?php } if (in_array("getreceivedbyaddress", $methods)) { ?>
    <div class="block method getreceivedbyaddress"><h4>getreceivedbyaddress</h4>
     <input class="address method required" id="getreceivedbyaddress_address" type="text"/>
     <input class="minconf method optional" id="getreceivedbyaddress_minconf" type="text"/>
    </div>
<?php } if (in_array("gettransaction", $methods)) { ?>
    <div class="block method gettransaction"><h4>gettransaction</h4>
     <input class="txid method required" id="gettransaction_txid" type="text"/>
    </div>
<?php } if (in_array("help", $methods)) { ?>
    <div class="block method help"><h4>help</h4>
     <input class="method" id="help" type="button" value="help"/>
    </div>
<?php } if (in_array("listaccounts", $methods)) { ?>
    <div class="block method listaccounts"><h4>listaccounts</h4>
     <input class="minconf method optional" id="listaccounts_minconf" type="text"/>
    </div>
<?php } if (in_array("listreceivedbyaccount", $methods)) { ?>
    <div class="block method listreceivedbyaccount"><h4>listreceivedbyaccount</h4>
     <input class="minconf method optional" id="listreceivedbyaccount_minconf" type="text"/>
     <select class="method optional" id="listreceivedbyaccount_includeempty">
      <option value="0">false</option>
      <option value="1">true</option>
     </select>
    </div>
<?php } if (in_array("listreceivedbyaddress", $methods)) { ?>
    <div class="block method listreceivedbyaddress"><h4>listreceivedbyaddress</h4>
     <input class="minconf method optional" id="listreceivedbyaddress_minconf" type="text"/>
     <select class="method optional" id="listreceivedbyaddress_includeempty">
      <option value="0">false</option><option value="1">true</option>
     </select>
    </div>
<?php } if (in_array("listtransactions", $methods)) { ?>
    <div class="block method listtransactions"><h4>listtransactions</h4>
      <input class="account method optional" id="listtransactions_account" type="text"/>
      <input class="count method optional" id="listtransactions_count" type="text"/>
    </div>
<?php } if (in_array("move", $methods)) { ?>
    <div class="block method move"><h4>move</h4>
     <input class="fromaccount method required" id="move_fromaccount" type="text"/>
     <input class="toaccount method required" id="move_toaccount" type="text"/>
     <input class="amount method required" id="move_amount" type="text"/>
     <input class="minconf method optional" id="move_minconf" type="text"/>
     <input class="comment method optional" id="move_comment" type="text"/>
    </div>
<?php } if (in_array("sendfrom", $methods)) { ?>
    <div class="block method sendfrom"><h4>sendfrom</h4>
     <input class="account method required" id="sendfrom_account" type="text"/>
     <input class="address method required" id="sendfrom_address" type="text"/>
     <input class="amount method required" id="sendfrom_amount" type="text"/>
     <input class="minconf method optional" id="sendfrom_minconf" type="text"/>
     <input class="comment method optional" id="sendfrom_comment" type="text"/>
     <input class="commentto method optional" id="sendfrom_commentto" type="text"/>
    </div>
<?php } if (in_array("sendtoaddress", $methods)) { ?>
    <div class="block method sendtoaddress"><h4>sendtoaddress</h4>
     <input class="address method required" id="sendtoaddress_address" type="text"/>
     <input class="amount method required" id="sendtoaddress_amount" type="text"/>
     <input class="comment method required" id="sendtoaddress_comment" type="text"/>
     <input class="commentto method optional" id="sendtoaddress_commentto" type="text"/>
    </div>
<?php } if (in_array("setaccount", $methods)) { ?>
    <div class="block method setaccount"><h4>setaccount</h4>
    <input class="address method required" id="setaccount_address" type="text"/>
    <input class="account method optional" id="setaccount_account" type="text"/>
    </div>
<?php } if (in_array("setgenerate", $methods)) { ?>
    <div class="block method setgenerate"><h4>setgenerate</h4>
     generate: <select class="method required" id="setgenerate_generate">
      <option <?php if (!$generate) echo "selected "; ?>value="0">false</option>
      <option <?php if ($generate) echo "selected "; ?>value="1">true</option>
     </select>
     genproclimit: <select class="method optional" id="setgenerate_genproclimit">
      <?php for ($x=-1; $x<=$cpus; $x++) { if (($x == -1 && $generate) || ($x == 0 && !$generate)) $selected = " selected"; else $selected = ""; echo "<option$selected value=\"$x\">$x</option>"; } ?>
     </select>
    </div>
<?php } if (in_array("stop", $methods)) { ?>
    <div class="block method stop"><h4>stop</h4>
     <input class="method" id="stop" type="button" value="stop"/>
    </div>
<?php } if (in_array("validateaddress", $methods)) { ?>
    <div class="block method validateaddress"><h4>validateaddress</h4>
     <input class="address method required" id="validateaddress_address" type="text"/>
    </div>
<?php } ?>
   </div>
   <div id="output"></div>
  </div>
 </body>
</html>