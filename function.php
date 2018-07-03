<?php
/**
 * @param $url
 * Function to run Get Request through API
 */

// - - - - - - - - - - - - - - - - - - - - - -
// InfusionSoft Functions
// - - - - - - - - - - - - - - - - - - - - - -
function Infusionsoft(){
    define('OAUTH2_CLIENT_ID', '5wwajnfs5hpfn8emwjqdmyqr');
    define('OAUTH2_CLIENT_SECRET', 'VjvKwJnkBq');

    $authorizeURL = 'https://signin.infusionsoft.com/app/oauth/authorize';
    $tokenURL = 'https://api.infusionsoft.com/token';
    $apiURLBase = 'https://api.infusionsoft.com/crm/rest/v1';
    $redirectURL = "http://localhost/Projects/Teamwork_API/infusionsoft.php";

    $params = array(
        'client_id' => OAUTH2_CLIENT_ID,
        'redirect_uri' => $redirectURL,
        'response_type' => 'code',
        'scope' => 'full'
    );

    $channel = curl_init($authorizeURL);
    curl_setopt($channel, CURLOPT_POST, 1);
    curl_setopt($channel, CURLOPT_POSTFIELDS, $params);

    $result = curl_exec($channel);
    $info = curl_getinfo($ch);
    print_r($info);

    curl_close ( $channel );

    return;


    //curl_setopt($channel, CURLOPT_URL, "https://api.infusionsoft.com/token");
    //curl_setopt( $channel, CURLOPT_POST, 1 );

    //curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 );

    //curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, 0);

    //curl_setopt($channel, CURLOPT_POSTFIELDS, array(
       // 'code' => $_GET['code'],
       // 'client_id' => OAUTH2_CLIENT_ID,
       // 'client_secret' => OAUTH2_CLIENT_SECRET,
       // 'redirect_uri' => $redirectURL,
       // 'grant_type' => 'authorization_code'
    //));

   // $result = json_decode(curl_exec ( $channel ), true);

    //curl_close ( $channel );

    //return $result;
}

// - - - - - - - - - - - - - - - - - - - - - -
// Teamwork Functions
// - - - - - - - - - - - - - - - - - - - - - -
function GetRequest($url){
    $company = "sitesnstores";
    $key = "twp_u3cNmAdrRFQYaLM0mZnNud44fA5c";

    $channel = curl_init();

    curl_setopt( $channel, CURLOPT_URL, "https://". $company .".teamwork.com/". $url );
    curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $channel, CURLOPT_HTTPHEADER,
        array( "Authorization: BASIC ". base64_encode( $key .":xxx" ))
    );

    $result = json_decode(curl_exec ( $channel ), true);

    curl_close ( $channel );

    return $result;
}

/**
 * @param $url
 * @param $value
 * Function to run Post Request through API
 */
function PostRequest($url, $value){
    $company = "sitesnstores";
    $key = "twp_u3cNmAdrRFQYaLM0mZnNud44fA5c";

    $channel = curl_init();

    curl_setopt( $channel, CURLOPT_URL, "https://". $company .".teamwork.com/". $url );
    curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $channel, CURLOPT_POST, 1 );
    curl_setopt( $channel, CURLOPT_POSTFIELDS, $value );
    curl_setopt( $channel, CURLOPT_HTTPHEADER, array(
        "Authorization: BASIC ". base64_encode( $key .":xxx" ),
        "Content-type: application/json"
    ));

    curl_exec ( $channel );

    curl_close ( $channel );
}

/**
 * @param $url
 * @param $value
 * Function to run Put Request through API
 */
function PutRequest($url, $value){
    $company = "sitesnstores";
    $key = "twp_u3cNmAdrRFQYaLM0mZnNud44fA5c";

    $channel = curl_init();
    curl_setopt( $channel, CURLOPT_URL, "https://". $company .".teamwork.com/". $url );
    curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt( $channel, CURLOPT_POSTFIELDS, $value );
    curl_setopt( $channel, CURLOPT_HTTPHEADER,
        array( "Authorization: BASIC ". base64_encode( $key .":xxx" ))
    );

    echo curl_exec ( $channel );

    curl_close ( $channel );
}

/**
 * @param $tags
 * @return bool
 * Function to check whether new tag already exists
 */
function CheckTags($tags){
    $all_tags = GetRequest("tags.json");
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
    PostRequest("tags.json", $tag_formatted);
}

/**
 * @param $num
 * Function to check who has the less projects in hand
 */
function JobAssign($num){
    // Get all project ids
    $all_projects = GetRequest("projects.json");
    $all_project_ids = array_column($all_projects["projects"], "id");

    // A collection of all the people's ids who have projects in hand
    $job_id_total = array();
    foreach ($all_project_ids as $project_id){
        $people_in_project = GetRequest("projects/" . $project_id . "/people.json");
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