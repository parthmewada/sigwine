<?php
	//Required Files for add object of classes
	require_once('Worksheet.php');
	require_once('Workbook.php');

	function HeaderingExcel($filename) 
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}

	// Generate Excel File
	HeaderingExcel('excelworksheets.xls');

	// Creating a workbook
	$workbook = new Workbook("-");

	// Creating the first worksheet
	$worksheet1 =& $workbook->add_worksheet('My Sheet1');
	$worksheet1->set_column(1, 1, 40);
	$worksheet1->set_row(1, 20);
	$worksheet1->write_string(1, 1, "This worksheet's name is ".$worksheet1->get_name());
	
	// Creating the Second worksheet
	$worksheet1 =& $workbook->add_worksheet('My Sheet2');
	$worksheet1->set_column(1, 1, 40);
	$worksheet1->set_row(1, 20);
	$worksheet1->write_string(1, 1, "This worksheet's name is ".$worksheet1->get_name());
	
	$workbook->close();
?>