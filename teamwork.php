<?php
/**
 * Created by PhpStorm.
 * User: Jane Wang
 * Date: 28/06/2018
 * Time: 2:13 PM
 */

// - - - - - - - - - - - - - - - - - - - - - -
// Include function.php file
// - - - - - - - - - - - - - - - - - - - - - -
include "function.php";

// - - - - - - - - - - - - - - - - - - - - - -
// New Project Info
// - - - - - - - - - - - - - - - - - - - - - -
$project_name = "API Project Test";
$company_id = "57247";
$start_date = "20180801";
$end_date = "20180815";
$tags = "Overseas";
$people_needed = 3;

$domain_name = "www.sitesnstores.com.au";
$domain_host_with_us = "Yes";
$current_server = "AliBaba02";
$dns_location = "Cloudflare";
$host_ip = "192.168.1.1";
$ftp_username = "test";
$ftp_password = "test";
$control_panel_url = "test.com.au";


$new_project_arr = array('project' =>
    array(  'name' => $project_name,
        'startDate' => $start_date,
        'endDate' => $end_date,
        'companyId' => $company_id ));

$new_project_json = json_encode($new_project_arr);

$notes_content = "<p>Domain Name: " . $domain_name . "</p>
<p>Main Domain Registered with us: " . $domain_host_with_us . "</p>
<p>Current Server: " . $current_server . "</p>
<p>DNS Location: " . $dns_location . "</p>
<p>Host IP: " . $host_ip . "</p>
<p>FTP Username: " . $ftp_username . "</p>
<p>FTP Password: " . $ftp_password . "</p>
<p>Control Panel URL: " . $control_panel_url . "</p>";

// - - - - - - - - - - - - - - - - - - - - - -
// Check whether this project already exists
// - - - - - - - - - - - - - - - - - - - - - -
$existing_projects = GetRequest("projects.json");
$existing_project_names = array();

// Push all existing projects' names into an array
foreach ($existing_projects["projects"] as $existing_project){
    if ($existing_project["company"]["id"] == $company_id) {
        array_push($existing_project_names, $existing_project["name"]);
    }
}

// Check by new project's name
if (!in_array($project_name, $existing_project_names)) {
    // If does not exist, create a new project
    $create_project = "projects.json";
    PostRequest($create_project, $new_project_json);

    // Update existing projects list
    $existing_projects = GetRequest("projects.json");
}

foreach ($existing_projects["projects"] as $existing_project){
    if ($existing_project["name"] == $project_name) {
        $project_id = $existing_project["id"];
    }
}

// Update Project Tags
$update_tags = "projects/" . $project_id . "/tags.json";
$new_project_tags = array("tags"=>array("content"=>$tags));
PutRequest($update_tags, json_encode($new_project_tags));

// Update Project
$update_project = "projects/" . $project_id . ".json";
PutRequest($update_project, $new_project_json);

// - - - - - - - - - - - - - - - - - - - - - -
// Assign people into the new/updated project
// - - - - - - - - - - - - - - - - - - - - - -
// Check whether people is already enough
$people_already_in = GetRequest("projects/" . $project_id . "/people.json");
$people_already_in_count = 0;
for ($i = 0; $i < count($people_already_in["people"]); $i++){
    if ($people_already_in["people"][$i]["administrator"] != 1 && $people_already_in["people"][$i]["permissions"]["project-administrator"] != 1){
        $people_already_in_count++;
    }
}

if ($people_already_in_count < $people_needed){
    $people_actual_needed = $people_needed - $people_already_in_count;
    $people_list = JobAssign($people_actual_needed);
    $people_assigned = implode(",", $people_list);

    $people_assigned_json = json_encode(array("add"=>array("userIdList"=>$people_assigned)));

    $assign_people_url = "projects/" . $project_id . "/people.json";
    PostRequest($assign_people_url, $people_assigned_json);
}

// - - - - - - - - - - - - - - - - - - - - - -
// Add Notes into Project
// - - - - - - - - - - - - - - - - - - - - - -
$notebook = array("notebook"=>array("name"=>"Project Startup Info", "description"=>$project_name, "content"=>$notes_content, "category-id"=>"458707"));
$notebook_json = json_encode($notebook);
$notebook_url = "projects/" . $project_id . "/notebooks.json";

// Check if the original notebook exists
$notebooks = GetRequest($notebook_url);

$notebooks_names = array_column($notebooks["project"]["notebooks"], "name", "id");
if (in_array($notebook["notebook"]["name"], $notebooks_names)){
    $notebook_id = array_search ($notebook["notebook"]["name"], $notebooks_names);
    PutRequest("notebooks/" . $notebook_id . ".json", $notebook_json);
} else{
    PostRequest($notebook_url, $notebook_json);
}


