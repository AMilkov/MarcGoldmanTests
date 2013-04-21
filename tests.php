<?php
// TASK #1 **********************************************************************
function mysort() {
	return implode(',',mergeSort(func_get_args()));
}

function mergeSort($array)
{
	if(count($array) == 1 ) return $array;

    $mid = count($array) / 2;
    $left = array_slice($array, 0, $mid);
    $right = array_slice($array, $mid);
    $left = mergeSort($left);
    $right = mergeSort($right);

    return merge($left, $right);
}


function merge($left, $right)
{
    $res = array();

    while (count($left) > 0 && count($right) > 0)
    {
        if($left[0] > $right[0])
        {
            $res[] = $right[0];
            $right = array_slice($right , 1);
        }
        else
        {
            $res[] = $left[0];
            $left = array_slice($left, 1);
        }
    }

    while (count($left) > 0)
    {
        $res[] = $left[0];
        $left = array_slice($left, 1);
    }

    while (count($right) > 0)
    {
        $res[] = $right[0];
        $right = array_slice($right, 1);
    }

    return $res;
}

// Test the merge sort
print mysort('a','c','b',4,3,2,1,0,0)."\n";


// TASK #2 **********************************************************************
function checkdomain($s)
{
	return preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*[\.]$/i", $s) //valid chars check
            && preg_match("/^.{1,253}[\.]$/", $s) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*[\.]$/", $s)    //length of each label
	? 'MATCH' : 'NO MATCH';
}

print checkdomain('0-0-0-aBc-d.com.')."\n";
print checkdomain('-something.com')."\n";


// TASK #3 **********************************************************************
function countones($number){
	return array_sum( str_split(base_convert($number,10,2)) );
}

print countones(12)."\n";
print countones(8)."\n";


// TASK #4 **********************************************************************
// Write a function sanitize that accepts an arbitrary HTML fragment $html and the list of allowed tags and attributes $allowed (array('tag1' => array(), 'tag2' => array('attribute1' ,'attribute2'))). The function should process the HTML fragment and remove all disallowed tags and tag attributes

function sanitize($html, $allowed)
{
	$bad_pattern=array(
				"/<script>(.*?)<\/script>/",
				"/<style>(.*?)<\/style>/" 	
			);
	$html= preg_replace($bad_pattern,'', $html); // remove script and style tags
	$allowed_tags=''; 
	$allowable_atts=array();
	foreach ($allowed as $key=>$value) {
		if (is_array($value)) {
			$allowed_tags .= "<$key>";
			$allowable_atts = array_merge($allowable_atts,$value);
		} else $allowed_tags .= "<$value>";
	}
	// strip collector
	$strip_arr = array();
	
	// load XHTML with SimpleXML
	$data_sxml = simplexml_load_string('<root>'. $html .'</root>', 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOXMLDECL);
	
	if ($data_sxml ) {
		// loop all elements with an attribute
		foreach ($data_sxml->xpath('descendant::*[@*]') as $tag) {
			// loop attributes
			foreach ($tag->attributes() as $name=>$value) {
				// check for allowable attributes
				if (!in_array($name, $allowable_atts)) {
					// set attribute value to empty string
					$tag->attributes()->$name = '';
					// collect attribute patterns to be stripped
					$strip_arr[$name] = '/ '. $name .'=""/';
				}
			}
		}
	}
	
	// strip unallowed attributes and root tag
	return strip_tags(preg_replace($strip_arr,array(''),$data_sxml->asXML()), $allowed_tags);
}

$html="<div><p align='left' onclick='alert(1)'>sample <b><i>text</i></b><script>alert(2);</script></p></div>";
$allowed=array('b', 'p' => array('align'));
print sanitize($html,$allowed)."\n";

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// BTW, this approach will NOT help is case of attack such as:
// <img src="javascript:alert('Vulnerable');" />



// TASK #5 **********************************************************************
function truncateString($s, $len, $etc = '...', $break_words = false, $middle = false)
{
	$s_len=mb_strlen($s,"UTF-8");
	if ($len<$s_len){			// Need to be truncated
		if ($middle) {			// Dont bother with words
			return substr($s,0,(int)(($len-strlen($etc))/2)).$etc.substr($s,(int)($s_len-($len-strlen($etc))/2));
		} else {				// Not in the middle
			if ($break_words){	// We don't care about breaking the words
				return substr($s,0,($len-strlen($etc))).$etc;
			} else {			// mind the words
				return substr($s, 0, strpos(wordwrap($s,($len-strlen($etc))),"\n")).$etc;
			}
		}
	
	} else return $s;
}

// which will truncate the string $s to the given length $len.

// Following are the function arguments:
// • string $s — UTF-8 string to process;
// • int $len — the desired length of the string, determines how many characters to truncate to
// • string $etc — text string that replaces the truncated text, its length is included in the $len argument
// • boolean $break_words — whether or not to truncate at a word boundary (false) or at the exact character (true). When false and the first word is longer than $len characters, return the first $len-strlen($etc) characters of the first word
// • boolean $middle — whether the truncation happens at the end of the string (false) or in the middle of the string (true). When true, word boundaries are ignored.

$s = 'Two Sisters Reunite after Eighteen Years at Checkout Counter.';
print truncateString($s, 30, '')."\n"; 			//will return Two Sisters Reunite after
print truncateString($s, 30, '===')."\n";			// will return Two Sisters Reunite after===
print truncateString($s, 30)."\n";					// will return Two Sisters Reunite after...
print truncateString($s, 30, '', true)."\n";		// will return Two Sisters Reunite after Eigh
print truncateString($s, 30, '...', true)."\n";	// will return Two Sisters Reunite after E...
print truncateString($s, 30, '..', true, true)."\n";	// will return Two Sisters Re..ckout Counter.

?>