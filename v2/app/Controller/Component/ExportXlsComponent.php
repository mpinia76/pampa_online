<?php
App::uses('Component', 'Controller');
class ExportXlsComponent extends Component {

	function export($fileName, $headerRow, $data) {
	 ini_set('max_execution_time', 1600); //increase max_execution_time to 10 min if data set is very large
	  $fileContent = implode("\t", $headerRow)."\n";
	  foreach($data as $result) {
	   $fileContent .=  implode("\t", $result)."\n";
	  }
	 header('Content-type: application/ms-excel'); /// you can set csv format
	 header('Content-Disposition: attachment; filename='.$fileName);
	 echo $fileContent;
	exit;
	}

	function exportTable($fileName, $table) {
		ini_set('max_execution_time', 1600);
		header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$fileName);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo "\xEF\xBB\xBF"; // UTF-8 BOM so Excel reads accents correctly
		echo $table;
		exit;
	}
}
?>
