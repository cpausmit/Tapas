<?php

include_once("app/models/Dbc.php");
include_once("app/models/Utils.php");
include_once("app/models/TeachingTask.php");

function getDivisions($db)
{
    $divisions = array();
    
    $sql = "select distinct Division from Students order by Division";
    $rows = $db->query($sql);
    foreach ($rows as $key => $row)
        $divisions[] = $row[0];
    
    return $divisions;
}

function getAssignments($db,$table,$divisions)
{
    // initialize data
    $semesterData = array();
    foreach ($divisions as $index => $division) {
        $semesterData[$division] = 0;
    }
    
    // show all assignments
    $sql = "select t.Task, t.Person, s.Division from $table as t".
           " inner join Students as s on s.Email = t.Person order by t.Task";
    $rows = $db->query($sql);
    foreach ($rows as $key => $row) {
        $task = $row[0];
        $person = $row[1];
        $division = $row[2];
        
        $weight = 0;
        $myTask = new TeachingTask($task);
        if ($myTask->isTa() && $myTask->getEffort() == 'full')
            $weight = 1.0;
        if ($myTask->isTa() && $myTask->getEffort() == 'half')
            $weight = 0.5;      
        $semesterData[$division] += $weight;
    }

    return $semesterData;
}

//--------------------------------------------------------------------------------------------------
//
// Define the format for the plot (example)
//
//$data = array(
//  	'legend' => array('F2009', 'S2010'),
//  	'data' => array(
//        array('division' => 'NPT', 'numbers' => array(1, 2)),
//        array('division' => 'AST', 'numbers' => array(3, 4))
//  	'averages' => array(
//        array('division' => 'NPT', 'number' => 1.7),
//    )
//);
//
//--------------------------------------------------------------------------------------------------

// make sure we have access to the database
//=========================================

// connect to our database
$db = Dbc::getReader();

// create the empty structure
//===========================

// data array
$data = array('legend' => array(),'totals' => array(), 'data' => array());
$averages = array();

// add the names of the divisions and the empty result array stub
$divisions = getDivisions($db);
foreach ($divisions as $division) {
    $data['data'][] = array('division' => $division, 'numbers' => array(), 'average' => 0);
    $averages[$division] = 0;
}

// fill the values from the database semester by semester
//=======================================================

// find all matching tables and fill per table
$nSemester = 0;
$tables = getTables(Dbc::GetReader(),'Assignments_____');
foreach ($tables as $key => $table) {
    $semester = substr($table,-5,5);
    if (substr($semester,0,1) != 'I') {
        $nSemester += 1;
        $data['legend'][] = $semester;
        $semesterData = getAssignments($db, $table, $divisions);
        $totals = 0;
        foreach ($divisions as $index => $division) {
            $data['data'][$index]['numbers'][] = $semesterData[$division];
            $averages[$division] += $semesterData[$division];
            $totals += $semesterData[$division];

            //print " Semester: ".$semester." Division: ".$division." N: ".$semesterData[$division]; 
        }
        $data['totals'][] = $totals;
    }
}

// calculate the average
if ($nSemester > 0) {
    foreach ($divisions as $index => $division) {
        $averages[$division] = floatval($averages[$division])/floatval($nSemester);
        $data['data'][$index]['average'] = intval($averages[$division]*10)/10.;
    }
}

//$data = array(
//  	'legend' => array('F2009', 'S2010'),
//  	'totals' => array(30, 35),
//  	'data' => array(
//        array('division' => 'NPT', 'numbers' => array(1, 2), 'average' => 1.7),
//        array('division' => 'AST', 'numbers' => array(3, 4), 'average' => 1.4),
//    ),
//);

// encode the data as a json file
echo json_encode($data);

?>
