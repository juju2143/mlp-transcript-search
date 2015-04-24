<?php
//date_default_timezone_set("America/New_York");
$file = file_get_contents("episodes.txt");
$lines = explode("\n", $file);
$episodes = array();
for($i=0; $i<count($lines); $i++)
{
	if($lines[$i])
	{
		$line = explode(";", $lines[$i]);
		$episodes[$line[0]][$line[1]]["title"] = $line[2];
		$episodes[$line[0]][$line[1]]["writer"] = $line[3];
		$episodes[$line[0]][$line[1]]["date"] = $line[4];
	}
}
for($l=0;$l<12;$l++)
{
	$season = array_keys($episodes)[mt_rand(0,count(array_keys($episodes))-1)];
	$ep = array_keys($episodes[$season])[mt_rand(0,count(array_keys($episodes[$season]))-1)];
	$title = "Transcripts/".rawurlencode($episodes[$season][$ep]["title"]);
	$thelines = array();
	$script = file_get_contents("http://mlp.wikia.com/api.php?format=json&action=query&titles=".$title."&prop=revisions&rvprop=content");
	$json = json_decode($script, TRUE);
	$content = reset($json["query"]["pages"])["revisions"][0]["*"];
	$lines = explode("\n", $content);
	//echo "Parsing ".$episodes[$season][$ep]["title"]." (season ".$season.", episode ".$ep.")...\n\n";
	for($i=0; $i<count($lines); $i++)
	{
		$line = $lines[$i];

		if($line != "")
		{
			if($line[0] == ":")
			{
				$line = substr($line, 1);
				if(preg_match("/^\s*'''\[(.*)\]''':?\s*(.*)/", $line, $matches))
				{
					$currentvoice = $matches[1];
					//echo "Found song verse by ".$matches[1]."\n\n";
				}
				else if(preg_match("/^\s*'''(.*)''':?\s*(.*)/", $line, $matches))
				{
					$currentvoice = $matches[1];
					//echo "Found dialog line by ".$matches[1].":\n";
					//echo $matches[2]."\n\n";
					$thelines[] = $matches[2];
				}
				else if(preg_match("/^\s*{{squarebrackets\|(.*)\[\[.*theme song\]\]}}/", $line))
				{
					//echo "Found theme song\n\n";
				}
				else if(preg_match("/^\s*(\[.*\])/", $line, $matches))
				{
					//echo "Found sfx:\n";
					//echo $matches[1]."\n\n";
					$thelines[] = $matches[1];
				}
				else if(preg_match("/^\s*{{#lst:(.*)\|(.*)}}/", $line, $matches))
				{
					//echo "Found song: ";
					//echo $matches[1]." (".$matches[2].")\n\n";
				}
				else
				{
					if($line[0] == ":")
					{
						//echo "Found song line by ".$currentvoice.":\n";
						//echo substr($line, 1)."\n\n";
						$thelines[] = substr($line, 1);
					}
					else
					{
						//echo "Found dialog line by ".$currentvoice." (cont.):\n";
						//echo $line."\n\n";
						$thelines[] = $line;
					}
				}
			}
			else
			{
				if(preg_match("/^\s*{{#lst:(.*)\|(.*)}}/", $line, $matches))
				{
					//echo "Found song: ";
					//echo $matches[1]." (".$matches[2].")\n\n";
				}
				else
				{
					//echo "Found info line: ";
					//echo $line."\n\n";
				}
			}
		}
	}
	echo $thelines[mt_rand(0,count($thelines)-1)]." ";
}
echo "\n";
