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
include "oauth2.php";
//include "function.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');
if ($infusionsoft->getToken()):
    $opp_id = "19751";
    $stage_ids = array('185', '83', '193', '131', '209', '93');

    $opp_table = 'Lead';
    $limit = 1000;
    $page = 0;
    $opp_queryData = ['Id' => $opp_id];
    $opp_selectedFields = ['OpportunityTitle', 'ContactID', 'StageID', '_SpecialInstructions', '_ClientManager', '_StartDate', '_SiteType'];

    $opp = $infusionsoft->data('xml')->query($opp_table, $limit, $page, $opp_queryData, $opp_selectedFields, 'Id', true);

    // Check if in the correct stage
    if (!in_array($opp[0]['StageID'], $stage_ids)) {
        echo "Sorry, nothing to update. Please check the opportunity's stage status.";
        die();
    }

    $opp_name = $opp[0]['OpportunityTitle'];
    $opp_contact_name_array = $infusionsoft->contacts('xml')->load($opp[0]['ContactID'], array('FirstName', 'LastName'));
    $opp_contact_name = implode(" ",$opp_contact_name_array);

    $opp_start_date = $opp[0]['_StartDate']->format('Ymd');
    //$opp_end_date = $opp[0]['NextActionDate']->format('Ymd');

    $opp_special_instructions = $opp[0]['_SpecialInstructions'];

    $opp_site_build_manager = $opp[0]['_ClientManager'];

    $opp_site_type = $opp[0]['_SiteType'];



    echo "<pre>";
    print_r($opp);
    echo "</pre>";

    // - - - - - - - - - - - - - - - - - - - - - -
    // New Project Params
    // - - - - - - - - - - - - - - - - - - - - - -
    $project_name = $opp_contact_name." - ".$opp_name;
    $company_id = "57247";
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
endif;

?>