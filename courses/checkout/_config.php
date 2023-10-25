<?php

$code_95_slug = "ccv-registratie-voor-code-95";
$code_95_status = ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,];
$_COURSES_SELECTION_CRITERIA = [
    "70" => ["luisterexamen" => false], //VCA Basis
    "537" => ["luisterexamen" => false], //VCA Basis Online & Examen
    "3949" => ["luisterexamen" => false, "ccv-registratie-voor-code-95" => true,  "licentie-lesmateriaal-ccv" => true,], // CODE 95: VCA Basis Cursus en Examen
    "3962" => ["ccv-registratie-voor-code-95" => true,  "licentie-lesmateriaal-ccv" => true,], // CODE 95: VCA Vol Cursus en Examen
    "4134" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], // CODE 95: Heftruck
    "4151" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], //CODE 95: Hoogwerker
    "510" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], //Code 95: EHBO/BHV
    "3906" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], //Code95: Het Nieuwe Rijden
    "565" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], //CODE 95: Veilig Werken Langs de Weg
    "507" => ["ccv-registratie-voor-code-95" => true, "licentie-lesmateriaal-ccv" => true,], //CODE 95: Digitale Tachograaf
];


//3949
//3962
//510
//4134
//4151
$_COURSE_CHECKED = [
    "3949" => "3949", "3962" => "3962",
    "4134" => "4134", "4151" => "4151",
    "510" => "510", "3906" => "3906",
    "565" => "565", "507" => "507"
];

global $wpdb;
$ids = array_keys($_COURSES_SELECTION_CRITERIA);
$ids = implode(',', $ids);
$table_name = $wpdb->prefix . "posts";
$query = "Select * from $table_name  where ID not in ($ids) AND post_type = 'lp_course' AND post_title like 'Code 95%'";
$result = $wpdb->get_results($query);
foreach ($result as $key => $row) {
    $_COURSES_SELECTION_CRITERIA[$row->ID] = $code_95_status;
}

// to set new optional course only for that courses which have ccv-registratie-voor-code-95
foreach ($_COURSES_SELECTION_CRITERIA as $courese_id => $criteria) {
    if (isset($criteria['ccv-registratie-voor-code-95']) && $criteria['ccv-registratie-voor-code-95'] == false) {
        $_COURSES_SELECTION_CRITERIA[$courese_id]["licentie-lesmateriaal-ccv"] = false;
    }
}

//echo "<pre>";
//print_r($_COURSES_SELECTION_CRITERIA);

$_MONTHS = [
    "January" => "januari",
    "February" => "februari",
    "March" => "maart",
    "April" => "april",
    "May" => "mei",
    "June" => "juni",
    "July" => "juli",
    "August" => "augustus",
    "September" => "september",
    "October" => "oktober",
    "November" => "november",
    "December" => "december",
];
$_DAYS = [
    "Sunday" => "Zondag",
    "Monday" => "Maandag",
    "Tuesday" => "Dinsdag",
    "Wednesday" => "Woensdag",
    "Thursday" => "Donderdag",
    "Friday" => "Vrijdag",
    "Saturday" => "Zaterdag",

];
$ORDER_DAYS = [
    "Monday" => "Maandag",
    "Tuesday" => "Dinsdag",
    "Wednesday" => "Woensdag",
    "Thursday" => "Donderdag",
    "Friday" => "Vrijdag",
    "Saturday" => "Zaterdag",
    "Sunday" => "Zondag",

];
$_START_FROM_DAY = 1;
$_NO_TOOL_TIP = ["luisterexamen" => "luisterexamen"];
/*
 *
    //with labels
    "VCA Basis Cursus en Examen"=>"code_95",
    "VCA Vol Cursus en Examen"=>"code_95",
    //
    "CODE 95: ADR Herhaal"=>"code_95",
    "Code95: ADR Basis"=>"code_95",
    //
    "Code95: Veilig werken langs de weg"=>"code_95",
    "Code 95: EHBO BHV"=>"code_95",
    //
    "CODE 95: Digitale Tachograaf",
    "CODE 95:"=>"Het Nieuwe Rijden"
 */

$courseFullyBookedMsg = ' (Cursus Vol)';

define('COURSES_SELECTION_CRITERIA', $_COURSES_SELECTION_CRITERIA);
define('COURSE_CHECKED', $_COURSE_CHECKED);
define('CODE_95_SLUG', $code_95_slug);
//define('CODE_95_STATUS', $code_95_status);
define('NO_TOOL_TIP', $_NO_TOOL_TIP);
define('MONTHS', $_MONTHS);
define('DAYS', $_DAYS);
define('ORDER_DAYS', $ORDER_DAYS);
define('START_FROM_DAY', $_START_FROM_DAY);
define('FULLY_BOOKED_COURSE_MESSAGE', $courseFullyBookedMsg);
