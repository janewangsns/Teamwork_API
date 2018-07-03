<?php
// - - - - - - - - - - - - - - - - - - - - - -
// Infusionsoft Functions
// - - - - - - - - - - - - - - - - - - - - - -
define("CALLBACK_URL", "http://localhost/Projects/Teamwork_API/oauth2client.php");
define("AUTH_URL", "https://signin.infusionsoft.com/app/oauth/authorize");
define("ACCESS_TOKEN_URL", "https://api.infusionsoft.com/token");
define("CLIENT_ID", "5wwajnfs5hpfn8emwjqdmyqr");
define("CLIENT_SECRET", "VjvKwJnkBq");

function getAuthorized(){
    //$apibaseURL = "https://api.infusionsoft.com/crm/rest/v1";
    $url = AUTH_URL."?"
        ."response_type=code"
        ."&client_id=". urlencode(CLIENT_ID)
        ."&scope=full"
        ."&redirect_uri=". urlencode(CALLBACK_URL);

    header('Location:'.$url);
}

function getToken($code){
    $params = array(
        'client_id'     => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'code'          => $code,
        'grant_type'    => 'authorization_code',
        'redirect_uri'  => CALLBACK_URL
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ACCESS_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, count($params));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $decodedResponse = json_decode($response, true);
    return $decodedResponse;
}

function refreshToken($refreshToken){
    $params = array(
        'grant_type'    => 'refresh_token',
        'refresh_token'  => $refreshToken
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ACCESS_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, count($params));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
        'Authorization: BASIC '. base64_encode( CLIENT_ID.':'.CLIENT_SECRET ),
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $decodedResponse = json_decode($response, true);
    print_r($decodedResponse);
    return $decodedResponse;
}


// - - - - - - - - - - - - - - - - - - - - - -
// Teamwork Functions
// - - - - - - - - - - - - - - - - - - - - - -
/**
 * @param $url
 * @param $request_method
 * @param null $value
 * @return mixed
 * Teamwork API's functions: GET, PUT and POST
 */
function Teamwork($url, $request_method, $value = NULL){
    $company = "sitesnstores";
    $key = "twp_u3cNmAdrRFQYaLM0mZnNud44fA5c";

    $channel = curl_init();
    curl_setopt( $channel, CURLOPT_URL, "https://". $company .".teamwork.com/". $url );
    curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $channel, CURLOPT_HTTPHEADER, array(
        "Authorization: BASIC ". base64_encode( $key .":xxx" ),
        "Content-type: application/json"
    ));

    switch ($request_method){
        case "GET":
            $result = json_decode(curl_exec ( $channel ), true);
            return $result;
            break;
        case "POST":
            curl_setopt( $channel, CURLOPT_POST, 1 );
            curl_setopt( $channel, CURLOPT_POSTFIELDS, $value );
            curl_exec ( $channel );
            break;
        case "PUT":
            curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt( $channel, CURLOPT_POSTFIELDS, $value );
            curl_exec ( $channel );
            break;
    }
    curl_close ( $channel );
}

/**
 * @param $tags
 * @return bool
 * Function to check whether new tag already exists
 */
function CheckTags($tags){
    $all_tags = Teamwork("tags.json", "GET");
    $all_tags_names = array_column($all_tags["tags"], "name");

    // Explode new tags to be an array
    $tags_array = explode(', ', $tags);

    // Check if all new tags already exist
    if(count(array_intersect($tags_array, $all_tags_names)) == count($tags_array)){
        return true;
    } else {
        return false;
    }
}

/**
 * @param $tag
 * Function to create a new tag system wide
 */
function CreateTag($tag){
    $colors = array("#d84640", "#f78234", "#f4bd38", "#b1da34", "#53c944", "#37ced0", "#2f8de4", "#9b7cdb", "#f47fbe", "#a6a6a6", "#4d4d4d", "#9e6957");
    $rand_color = array_rand($colors, 1);
    $tag_formatted = array("tag"=>array("name"=>$tag, "color"=>$colors[$rand_color]));
    $tag_formatted_json = json_encode($tag_formatted);
    Teamwork("tags.json","POST", $tag_formatted_json);
}

/**
 * @param $num
 * Function to check who has the less projects in hand
 */
function JobAssign($num){
    // Get all project ids
    $all_projects = Teamwork("projects.json", "GET");
    $all_project_ids = array_column($all_projects["projects"], "id");

    // A collection of all the people's ids who have projects in hand
    $job_id_total = array();
    foreach ($all_project_ids as $project_id){
        $people_in_project = Teamwork("projects/" . $project_id . "/people.json", "GET");
        foreach ($people_in_project["people"] as $person_in_project){
            array_push($job_id_total, $person_in_project["id"]);
        }
    }

    // Count project number of each person
    $job_counts = array_count_values($job_id_total);

    // Find the required number of people who have relatively less projects in hand
    $people_num_count = array_count_values($job_counts);
    krsort($people_num_count);
    $people_num_count_values = array_values($people_num_count);
    $people_num_count_keys = array_keys($people_num_count);

    if($num <= end($people_num_count)){
        $job_assign_range = array();
        while ($job_count_total = current($job_counts)){
            if ($job_count_total <= min($job_counts)){
                array_push($job_assign_range, key($job_counts));
            }
            next($job_counts);
        }
        if ($num == 1){
            $job_assign_index = array();
            array_push($job_assign_index, array_rand($job_assign_range, $num));
        } else{
            $job_assign_index = array_rand($job_assign_range, $num);
        }

        $job_assign = array();
        for ($i = 0; $i < count($job_assign_index); $i++){
            array_push($job_assign, $job_assign_range[$job_assign_index[$i]]);
        }
    } else{
        $level = 0;
        for ($i = (count($people_num_count_values)-1); $i >= 0; $i--){
            $level = $level + $people_num_count_values[$i];
            if ($num <= $level){
                $level_key = $people_num_count_keys[$i];
                break;
            }
        }
        // job_assign_range_1 is the pool of people have to be assigned
        // job_assign_range_2 is the pool of people will randomly be assigned
        $job_assign_range_1 = array();
        $job_assign_range_2 = array();
        while ($job_count_total = current($job_counts)){
            if ($job_count_total < $level_key){
                array_push($job_assign_range_1, key($job_counts));
            } elseif ($job_count_total == $level_key){
                array_push($job_assign_range_2, key($job_counts));
            }
            next($job_counts);
        }

        if (($num-count($job_assign_range_1)) == 1){
            $job_assign_range_pick = array();
            array_push($job_assign_range_pick, array_rand($job_assign_range_2, ($num-count($job_assign_range_1))));
        } else{
            $job_assign_range_pick = array_rand($job_assign_range_2, ($num-count($job_assign_range_1)));
        }

        $job_assign = $job_assign_range_1;

        for ($i = 0; $i < count($job_assign_range_pick); $i++){
            array_push($job_assign, $job_assign_range_2[$job_assign_range_pick[$i]]);
        }
    }

    return $job_assign;
}