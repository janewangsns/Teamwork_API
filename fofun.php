<?php
// - - - - - - - - - - - - - - - - - - - - - -
// Include function.php file
// - - - - - - - - - - - - - - - - - - - - - -
include "function.php";

// - - - - - - - - - - - - - - - - - - - - - -
// API info
// - - - - - - - - - - - - - - - - - - - - - -
$company = "sitesnstores";
$key = "twp_u3cNmAdrRFQYaLM0mZnNud44fA5c";


// Get all project ids
$all_projects = GetRequest("projects.json");
$all_project_ids = array_column($all_projects["projects"], "id");

//echo "<pre>";
//print_r($all_project_ids);
//echo "</pre>";

// A collection of all the people's ids who have projects in hand
$job_id_total = array();
foreach ($all_project_ids as $project_id){
    $people_in_project = GetRequest("projects/" . $project_id . "/people.json");
    foreach ($people_in_project["people"] as $person_in_project){
        array_push($job_id_total, $person_in_project["id"]);
    }
}

echo "<pre>";
print_r($job_id_total);
echo "</pre>";