<?php
/**
 * Created by PhpStorm.
 * User: Jane Wang
 * Date: 2/07/2018
 * Time: 11:05 AM
 */

// - - - - - - - - - - - - - - - - - - - - - -
// Include function.php file
// - - - - - - - - - - - - - - - - - - - - - -
include "oauth2client.php";
include "function.php";

if ($infusionsoft->getToken()):

    $opp_id = "20177";

    //$stage_ids = array('185', '83', '193', '131', '209', '93');
    $stage_names = array('02 (M) Layout Design', '02 Layout Design', '05a (M) Logo Design', '05a Logo Design', '07 (M) Development', '08 Development');
    //$opps = $infusionsoft->opportunities()->find($opp_id);
    //echo "<pre>";
    //print_r($opps);
    $opp = $infusionsoft->opportunities()->find($opp_id)->getAttributes();
    $opp_info = $infusionsoft->opportunities()->with('sales_person');

    $contact = $infusionsoft->


    echo "<pre>";
    print_r($contact);
    if (in_array($opp['stage']['name'], $stage_names)){
        $project_name = $opp['contact']['first_name']." ".$opp['contact']['last_name']." - ".$opp['opportunity_title'];
        $start_date = "20180715";
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
    }

    //echo "<pre>";
    //print_r($opp);


endif;
