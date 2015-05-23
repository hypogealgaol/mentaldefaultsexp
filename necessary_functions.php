<?php 

function validate_worker_id($id_str){
    $min_length = 10;
    $max_length = 25;
    $bad_reg_ex = '/\W/';
    $id_length = strlen($id_str);
    $firstChar = $id_str[0];    

    if ( ($id_length < $min_length) || ($id_length > $max_length) || 
        (!stristr($firstChar,'A')) || (preg_match($bad_reg_ex, $id_str)) ) 
    {    
        return false; 
    }
    else
    {
        return true;
    }
    
}

function printjs($script) {
    print "\n\n <script language='javascript'> \n\n $script \n\n script>\n\n";
}

function in_array_case_insensitive($needle, $haystack) 
{
    return in_array( strtolower($needle), array_map('strtolower', $haystack) );
}

function readCSV($csvFile){
	global $delimiter;
	$file_handle = fopen($csvFile, 'r');
	while (($line = fgetcsv($file_handle, 1024, $delimiter)) !== FALSE) {
		$line_of_text[] = $line;
	}
	fclose($file_handle);
	return $line_of_text;
}

function readCSVlocked($file_handle){
	global $delimiter;
	while (($line = fgetcsv($file_handle, 1024, $delimiter)) !== FALSE) {
		$line_of_text[] = $line;
	}
	return $line_of_text;

}

function updateCSV($csvFile,$csvData){
	$file_handle = fopen($csvFile, 'w');
	foreach ($csvData as $fields) {
		fputcsv($file_handle, $fields);
	}
	fclose($file_handle);
}

function reportData($data) {
	echo "<br /> data is: ";
	print_r($data); 
	echo "<br />";
}

function in_string ($words, $string, $option)
{
    if ($option == "all")
    {
        foreach ($words as $value)
            if (stripos($string, $value) === false)
                return false;
        return true;
    }
    else
    {
        foreach ($words as $value)
            if (stripos($string, $value) !== false)
                return true;
        return false;
    }
}

function twodshuffle($array)
{
    // Get array length
    $count = count($array);
    // Create a range of indicies
    $indi = range(0,$count-1);
    // Randomize indicies array
    shuffle($indi);
    // Initialize new array
    $newarray = array($count);
    // Holds current index
    $i = 0;
    // Shuffle multidimensional array
    foreach ($indi as $index)
    {
        $newarray[$i] = $array[$index];
        $i++;
    }
    return $newarray;
}
?>