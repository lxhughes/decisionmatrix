<?

/* Check for uploaded file
if ($_REQUEST['action'] == 'XMLDecode'){

	if ($_FILES["xmlfile"]["error"] > 0) {
		$warnings .= "<p>Upload error: ".$_FILES["file"]["error"];
    }
	else if ($_FILES["xmlfile"]["type"] == "text/xml"){
		$mmatrix = xmlFileget($_FILES["xmlfile"]["tmp_name"]);
		print_r($mmatrix);
	}
	else {
		$warnings .= "<p>Uploaded file must be in .xml format. File's format was: ".$_FILES["xmlfile"]["type"];
	}
}*/

// You didn't upload a file - check for request data
//else {
	$matrix = $_REQUEST;
//}
	$postcalculate = 0; // assume this is the first load of the program unless we know otherwise

// Process REQUEST data
if (isset($matrix['factors'])){
	$factors = $matrix['factors'];
	$numfactors = count($factors);
	$postcalculate = 1;
}
else {
	$factors = array();
	if (isset($matrix['numfactors'])) $numfactors = intval($matrix['numfactors']);
	else $numfactors = 3;
}

if (isset($matrix['contenders'])){
	$contenders = $matrix['contenders'];
	$numcont = count($contenders);
	$postcalculate = 1;
}
else {
	$contenders = array();
	if (isset($matrix['numcont'])) $numcont = intval($matrix['numcont']);
	else $numcont = 5;
}

/* Download XML File
if ($_REQUEST['action'] == 'XMLEncode'){

	header('Content-type: "text/xml"; charset="utf8"');
    header('Content-disposition: attachment; filename="decisionmatrix.xml"');

	echo xmlEncode(array("factors"=>$factors,"contenders"=>$contenders),1);
	return false;
}*/

// Add factor column 
if ($_REQUEST['action'] == 'AddFactor') $numfactors++;

// Add contender row 
if ($_REQUEST['action'] == 'AddContender') $numcont++;

// Reset form
if ($_REQUEST['action'] == 'Reset'){
	$factors = array();
	$contenders = array();
	$numfactors = 3;
	$numcont = 5;
	$postcalculate = 0;
}

?>

<html>
<head>
<title>Decision Making Matrix</title>
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="decision.css" />
<script type="text/javascript" src="/support/jquery/js/jquery.min.js" /></script>
<script type="text/javascript" src="decision.js" /></script>

</head>
<body>

<div id=lynx>
	<a class=lynx href="http://www.laurahughes.com/">main</a>
</div>
	
<h1>Decision Making Matrix</h1>

<div id=bodytext>
<? 

$formstuff = printForm();
echo "<div id='warnings'>".$warnings.$formstuff[1]."</div>";
echo $formstuff[0];

//print_r($_REQUEST); ?>

<p>Fill in all the factors that influence your decision in the top header row. Examples might be "price" and "reliability" if you're trying to decide between cars, or "prettiness" and "ease of spelling" if you're trying to decide between baby names.

<p>"Weight" refers to the relative importance of that factor. Weight can be any number. If you fill in 1 under "price" and 5 under "reliability," then reliability scores will be given 5 times more weight than price in the final score tally.

<p>Fill in your "contenders," the candidates you're trying to decide between, along the leftmost column.

<p>In scores, higher is always better. (This is counterintuitive for items like "price" where lower is better for you.) Scores can be any number, but it may help you to decide on a predetermined range, such as 1-5, with 5 being the best.

<p>Letter grades go from A+ down to F-. Grading is curved, so there is always at least one A+. Grading is less strict than in school; you must have a grade of less than 17/100 to get an F.

<p>After hitting "Calculate," you can copy the URL to share your decision matrix with your friends. 

</div>

</body>
</html>

<?

function factorHeader($i,$factor=array()){
	global $postcalculate;

 if (!empty($factor)) {
	$name = $factor['name'];
	$weight = $factor['weight'];
	
		if (isset($weight) && isset($name) && $name != "" && $weight != "" && !is_numeric($weight)){
			$warnings .= "<p>The weight for the factor $name is not numeric. For an accurate reading, please correct it to a numeric weight.";
		}
	}
 else {
	$name = "";
	$weight = "";
	}

 $print = "<td class='factorheader'>
			<label for='factors[$i][name]'>Factor</label>
			<br><input type='text' size='9' name='factors[$i][name]' id='factors_".$i."_name' value=\"$name\"";
		if ($postcalculate == 1) $print .= " class='factor secretinput'";
		$print .= "></input>
			<br><label for='factors[$i][weight]'>Weight</label>
			<br><input type='text' size='4' maxlength='4' name='factors[$i][weight]' id='factors_".$i."_weight' value=\"$weight\"";
		if ($postcalculate == 1) $print .= " class='weight secretinput'";
		$print .= "></input>
		</td>";
		
  return array($print,$warnings);
}

function contenderRow($i,$contender=array()){
	global $factors,$numfactors,$postcalculate;

	if ($i&1){
		$headerclass='contenderheader-odd';
		$scoreclass='score-odd';
		$gradeclass = 'grade-odd';
	}
	else {
		$headerclass='contenderheader-even';
		$scoreclass='score-even';
		$gradeclass='grade-even';
	}
	
	if (!empty($contender)){
		$name = $contender['name'];
	}
	else {
		$name = "";
	}

	$print = "<tr>
		<td class='$headerclass'>
			<label for='contenders[$i][name]'>Contender</label>
			<br><input type='text' size='15' name='contenders[$i][name]' id='contenders_".$i."_name' value=\"$name\"";
		if ($postcalculate == 1) $print .= " class='contender secretinput'";
		$print .= "></input>
		</td>";
		
	$totalscore = 0;
		
	for ($j = 0; $j < $numfactors; $j++){
	
		if (!empty($factors)){
			$weight = $factors[$j]['weight'];
			
			if (!is_numeric($weight)){
				$weight = 1;
			}
			
		}
		else {
			$weight = 1;
		}
	
		if (!empty($contender)){
			$thisscore = $contender['factor_'.$j];
			
			if ($name != "" && $thisscore != "" && !is_numeric($thisscore)){
				$warnings .= "<p>The score for the factor ".$factors[$j]['name']." and the contender ".$name." is not numeric. For an accurate reading, please correct it to a numeric score.";
				$thisscore = 0;
			}
			
			$totalscore += ($weight * $thisscore);
		}
		else {
			$thisscore = "";
		}
	
		$print .= "<td class='$scoreclass'>
					<label for='contenders[$i][factor_$j]'>Score</label>
					<br><input type='text' size='4' maxlength='4' name='contenders[$i][factor_$j]' id='contenders_".$i."_$j' value=\"$thisscore\"></input>
				</td>";
	}
	
	if ($totalscore == 0){
		$grade = 'TBA';
	}
	else {
		$grade = $totalscore;
	}
		
	$print .= "<td class='$gradeclass'>
				<span class='fakelabel'>Grade</span>
				<br><span class='grade'>$grade</span>
			</td>";
	
	$print .= "</tr>";
	
	return array($print,$warnings);

}

function printForm(){
	global $factors,$contenders,$numfactors,$numcont;

	$print = "<form method=\"GET\" action=\"".$_SERVER['PHP_SELF']."\">
	
	<input type='hidden' name='numfactors' id='numfactors' value='$numfactors'></input>
	<input type='hidden' name='numcont' id='numcont' value='$numcont'></input>


		<table>

		<tr>
			<td>&nbsp;</td>";

			// Header establishing the factors
			for($i = 0; $i < $numfactors; $i++){
			
				if (!empty($factors)) $thisfactor = $factors[$i];
				else $thisfactor = array();
			
				$factorstuff = factorHeader($i,$thisfactor);
				$print .= $factorstuff[0];
				$warnings .= $factorstuff[1];
			}
		
		$print .= "<td><button type='submit' name='action' value='AddFactor'>+</button></td>";
		$print .= "</tr>";

		// Contender rows
		for($j = 0; $j < $numcont; $j++){
		
			if (!empty($contenders)) $thiscontender = $contenders[$j];
			else $thiscontender = array();
		
			$contenderstuff = contenderRow($j,$thiscontender,$factors);
			$print .= $contenderstuff[0];
			$warnings .= $contenderstuff[1];
		}

		$print .= "</table>";
		
	$print .= "<button type='submit' name='action' value='AddContender'>+</button>";
		
	$print .= "<p><button type='submit' name='action' value='Calculate'>Calculate</button>
	<button type='submit' name='action' value='Reset'>Reset</button>";
	
	/*$print .= "<p><button type='submit' name='action' value='XMLEncode'>Download</button>
	
	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"300000\">
	<br><label for=\"xmlfile\">Choose an XML:</label> <input type=\"file\" name=\"xmlfile\" id=\"xmlfile\" /> 
	<button type='submit' name='action' value='XMLDecode'>Upload</button>
	<br>Download this matrix in XML format. You can then re-upload an appropriately formatted Decision Matrix XML to continue to work with it.";
	*/
	
	$print .= "</form>";
	
	return array($print,$warnings);
}

/* Turns an array into an XML in the same structure
function xmlEncode($arr,$toplevel = 0,$newkey=""){

	if ($toplevel == 1) $xml = "<?xml version=\"1.0\"?><decisionmatrix>";
	
	foreach($arr as $key=>$item){
		if (is_array($item)){
		
			// Recursive call to find child elements
			$tryxml = xmlEncode($item,0,substr($key,0,-1)); // the "newkey" is the key - one letter, to turn "factors" into "factor" e.g. Will only be used in the absence of text keys within the matrix.
			
			// Only put in a node if you get something back from 
			if ($tryxml != "") {
		
				if (!is_numeric($key)) $xml .= "<$key>"; // If there's a key, use that as the parent element
				else if (isset($newkey) && $newkey != "") $xml .= "<$newkey>"; // If there's only a numeric key, use the fallback (described above)
				$xml .= $tryxml;
				if (!is_numeric($key)) $xml .= "</$key>";
				else if (isset($newkey) && $newkey != "") $xml .= "</$newkey>"; 
				
			}
		}
		else {
			if ($item != "") $xml .= "<$key>$item</$key>";
		}
	}
	
	if ($toplevel == 1) $xml .= "</decisionmatrix>";
	
	return $xml;

}

// Turns a temporarily-uploaded XML file into an array
function xmlFileget($filename){

	$fh = fopen($filename, 'r');	// Open file
	$contents = stream_get_contents($fh);
	
	// Get rid of the <?xml line
	$contents = preg_replace("<<\?xml version=\"1.0\"\?>>","",$contents);
	
	// Testing this now 
	$arr = xmlstring_to_array($contents);
	print_r($arr);
	
	fclose($fh); 			// Close file
	
	return array();
}

// THIS DOESN'T MAKE SENSE YET. 
// Take a string of mixed <tags> and text. Returns an array of tag=>text, tag=>text, and an array of unprocessed text.
function xmlstring_to_array($string,$arr=array()){
		
	// Get first tag
	preg_match("<[A-Za-z0-9]+>",$string,$tags);
	
	// Gone down as far as we can
	if (empty($tags)){
		return $arr;
	}
	else {	
		$tag = $tags[0];
		
		// Get content of first tag
		$matchthis = "/<".$tag.">.+<\/".$tag.">/";
		preg_match($matchthis,$string,$tagcontent);
		
		$arr[$tag] = $tagcontent;
		xmlstring_to_array($tagcontent,$arr);
	}
	
}
*/

?>
