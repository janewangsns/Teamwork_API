<?php 
	// - - - - - - - - - - - - - - - - - - - - - -
	// Include files
	// - - - - - - - - - - - - - - - - - - - - - -
	include "infusionsoft.php";
    include "function.php";

	// - - - - - - - - - - - - - - - - - - - - - -
	// New Project Info
	// - - - - - - - - - - - - - - - - - - - - - -
    $company_id = "57247";
    $tags = "Infusionsoft";
    $people_needed = 3;

    $project_name = $opp_contact_name." - ".$opp_name;
    $start_date = $opp_start_date;
    $end_date = $opp_end_date;

	$new_project_arr = array('project' =>
	    array(  'name' => $project_name,
	        'startDate' => $start_date,
	        'endDate' => $end_date,
	        'companyId' => $company_id ));
	$new_project_json = json_encode($new_project_arr);
	$notes_content = $opp_notes_content;

	// - - - - - - - - - - - - - - - - - - - - - -
	// Check whether this project already exists
	// - - - - - - - - - - - - - - - - - - - - - -
	$existing_projects = Teamwork("projects.json", "GET");
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
	    Teamwork($create_project,"POST", $new_project_json);
	    // Update existing projects list
	    $existing_projects = Teamwork("projects.json", "GET");
	}
	foreach ($existing_projects["projects"] as $existing_project){
	    if ($existing_project["name"] == $project_name) {
	        $project_id = $existing_project["id"];
	    }
	}

	// Update Project Tags
	$update_tags = "projects/" . $project_id . "/tags.json";
	$new_project_tags = array("tags"=>array("content"=>$tags));
	Teamwork($update_tags, "PUT", json_encode($new_project_tags));
	// Update Project
	$update_project = "projects/" . $project_id . ".json";
	Teamwork($update_project, "PUT", $new_project_json);

    // - - - - - - - - - - - - - - - - - - - - - -
    // Assign project owner
    // - - - - - - - - - - - - - - - - - - - - - -
    SetProjectOwner($project_id);


	// - - - - - - - - - - - - - - - - - - - - - -
	// Assign people into the new/updated project
	// - - - - - - - - - - - - - - - - - - - - - -
	// Check whether people is already enough
	$people_already_in = Teamwork("projects/" . $project_id . "/people.json", "GET");
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
	    Teamwork($assign_people_url, "POST", $people_assigned_json);
	}

	// - - - - - - - - - - - - - - - - - - - - - -
	// Add Notes into Project
	// - - - - - - - - - - - - - - - - - - - - - -
	$notebook = array("notebook"=>array("name"=>"Project Startup Info", "description"=>$project_name, "content"=>$notes_content, "category-id"=>"458707"));
	$notebook_json = json_encode($notebook);
	$notebook_url = "projects/" . $project_id . "/notebooks.json";
	// Check if the original notebook exists
	$notebooks = Teamwork($notebook_url, "GET");
	$notebooks_names = array_column($notebooks["project"]["notebooks"], "name", "id");
	if (in_array($notebook["notebook"]["name"], $notebooks_names)){
	    $notebook_id = array_search ($notebook["notebook"]["name"], $notebooks_names);
	    Teamwork("notebooks/" . $notebook_id . ".json", "PUT", $notebook_json);
	} else{
	    Teamwork($notebook_url, "POST", $notebook_json);
	}