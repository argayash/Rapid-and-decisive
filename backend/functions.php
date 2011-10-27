<?
function __autoload($className) {
	$fileName = "classes/" . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	$status = (@
	include_once $fileName);
	if ($status === false)
		die(json_encode(array("Errors" => "Class not found - " . htmlspecialchars($className))));
}

function ass_array_shuffle($array) {
	while (count($array) > 0) {
		$val = array_rand($array);
		$new_arr[$val] = $array[$val];
		unset($array[$val]);
	}
	return $new_arr;
}
?>