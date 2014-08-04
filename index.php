<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

function debug($data) {
	echo '<pre>';
	debug_backtrace();
	print_r($data);
	echo '</pre><br />';
}

$title = "Anagram/Scrabble Helper - Mobile";
$url = 'http://www.anagrammer.com/scrabble/';

$fields_string = '';
foreach($_POST as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');

if (!empty($_POST['letters'])) {
	$_POST['groupResultCountLimit'] = 500;
	$_POST['page'] = 0;
	$_POST['page'] = 'GO >>';
	for ($i = 2; $i <= 9; $i++)
		$_POST['wordLengths'][$i] = "on";

	$title = "Words found using ".$_POST['letters'];
	if (!empty($_POST['boardPattern']))
		$title .= " and extending ".$_POST['boardPattern'];

	$ch = curl_init();

	$header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: "; //browsers keep this blank.

	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch,CURLOPT_POST,count($_POST));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch,CURLOPT_AUTOREFERER,true);
	curl_setopt($ch,CURLOPT_ENCODING,'gzip,deflate');
//	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch,CURLOPT_FAILONERROR,true);
	curl_setopt($ch,CURLOPT_VERBOSE,true);

	$curl_result = curl_exec($ch);
//   debug(htmlentities($curl_result));
//   debug(curl_error($ch));

	curl_close($ch);

	$start = strpos($curl_result, "<div class=\"zoomable\">");
	$end = strpos($curl_result, "<button class", $start);
//   debug("start".$start);
//   debug("end".$end);
   $results = preg_replace("|<a href=\'/scrabble/\w+\' target=\'\_blank\'>(\w+)</a>|", '$1', substr($curl_result, $start, $end - $start));
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="cache-control" content="public" />
		<meta name="robots" content="none" />
		<meta name="revisit-after" content="10 days" />
		<meta name="viewport" content="width=230" />
		<meta name="viewport" content="initial-scale=1.4" />

		<title><?=$title?></title>
		<link rel="stylesheet" type="text/css" href="http://www.anagrammer.com/common/anagrammer.css"/>
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	</head>
	<body>
<script type="text/javascript">
function normalizeRackTiles()
{
	rackTilesControlObject = document.getElementById("rackTiles");
	rackTilesValue = rackTilesControlObject.value;

	notWordCharacter = /\W/g;
	normalizedRackTilesValue = rackTilesValue.toUpperCase().replace(notWordCharacter, "?");

	if (normalizedRackTilesValue != rackTilesValue)
	{
		caretPosition = rackTilesControlObject.selectionStart - (rackTilesValue.length() - normalizedRackTilesValue.length());
		rackTilesControlObject.value = normalizedRackTilesValue;
		rackTilesControlObject.selectionStart = caretPosition;
		rackTilesControlObject.selectionEnd = caretPosition;
	}
}

function normalizeInputValue(inputControlObject, replaceChar)
{
	value = inputControlObject.value;

	notWordCharacter = /[^A-Z]/g;
	normalizedValue = value.toUpperCase().replace(notWordCharacter, replaceChar);

	if (normalizedValue != value)
	{
		caretPosition = inputControlObject.selectionStart - (value.length - normalizedValue.length);
		inputControlObject.value = normalizedValue;
		inputControlObject.selectionStart = caretPosition;
		inputControlObject.selectionEnd = caretPosition;
	}
}
</script>

<a name="top"></a>
<div style="width:200px; padding-left:5px;">
<form id="form" method="post" action="?">

 	<div style="font-family: arial,sans-serif; font-size:1em;">
 	<input type="button" value="Start New" onclick="this.form.letters.value='';this.form.letters.focus();" /><br />
	Your Tiles: <input type="text" name="letters" maxlength="16" value="<? if (!empty($_POST['letters'])) echo $_POST['letters']; ?>" onkeyup='normalizeInputValue(this, "?");' size="10" autocorrect="off" autocapitalize="off" />
	<br/>
	Board Tiles: <input type="text" name="boardPattern" maxlength="15" onkeyup='normalizeInputValue(this, "*");' size="10" autocorrect="off" autocapitalize="off" />
	<br/>
	Prefix: <input type="checkbox" name="filterPatternMatchType[prefix]" title="Check this box to use matching string as a prefix" class="vtip" /> &nbsp;
	Suffix: <input type="checkbox" name="filterPatternMatchType[suffix]" title="Check this box to use matching string as a suffix" class="vtip" /><br/>
	Match this: <input type="text" name="filterPattern" maxlength="15" autocorrect="off" autocapitalize="off" onkeyup='normalizeInputValue(this, "");' size="10" title="Put a matching string here to filter result list." /><br/>
	<input type="hidden" name="dictionaryName" value="TWL06" />
	Sort: <input type="radio" name="sort" value="score" onclick="" checked="checked" />score <input type="radio" name="sort" value="length" onclick="" />length
	<br/>
	<input type="submit" name="submitForm" value="Find Words" />
	<br />
	</div>

</form>

	<?
		if (!empty($results)) echo "<hr />".strip_tags($results, '<span><br>');
	?>
</div>
<div style="background-color:#eee; width:40px; height:25px; position:fixed; top:0; right:0; padding-top:10px;"><a href="#top" style="text-decoration:none; font-family: arial,sans-serif; font-size:0.8em;">^TOP^</a></div>

<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=7774601;
var sc_invisible=1;
var sc_security="03a2608c";
</script>
<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script>
<noscript><div class="statcounter"><a title="drupal hit
counter" href="http://statcounter.com/drupal/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/7774601/0/03a2608c/1/"
alt="drupal hit counter"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->

	</body>
</html>

