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

error_reporting(E_ALL);
ini_set('display_errors', '1');
if ($infusionsoft->getToken()):
    $opp_id = "19995";
    $stage_ids = array('185', '83', '193', '131', '209', '93', '273', '271');

    $opp_table = 'Lead';
    $limit = 1000;
    $page = 0;
    $opp_queryData = ['Id' => $opp_id];
    $opp_selectedFields = array('OpportunityTitle', 'ContactID', 'StageID', 'NextActionDate', '_SpecialInstructions', '_ClientManager',
        '_StartDate', '_SiteType', '_SiteTypeifother', '_SiteLevel', '_LogoLevel', '_Programming', '_DomainName',
        '_CurrentServer', '_PreviewLink', '_HostIP', '_FTPUsername', '_FTPPassword', '_ControlPanelURL',
        '_ControlPanelUsername', '_ControlPanelPassword', '_SiteLive', '_CMSAdminUsername', '_CMSAdminPassword',
        '_StoreAdminUsername', '_StoreAdminPassword', '_ProtectedDirectoryUsername', '_ProtectedDirectoryPassword');

    $opp = $infusionsoft->data('xml')->query($opp_table, $limit, $page, $opp_queryData, $opp_selectedFields, 'Id', true);

    // Check if in the correct stage
    if (!in_array($opp[0]['StageID'], $stage_ids)) {
        echo "Sorry, nothing to update. Please check the opportunity's stage status.";
        die();
    }

    // Generate Notes Content
    $opp = array_merge(array_fill_keys($opp_selectedFields, NULL), $opp[0]);
    $opp_ordered = array_filter($opp);
    $opp_notes = array();
    foreach ($opp_ordered as $key => $opp_field){
        switch ($key) {
            case "_SpecialInstructions":
                $text = "<p><b>Special Instructions:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ClientManager":
                $text = "<p><b>Site Build Manager:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_SiteType":
                $text = "<p><b>Site Type:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_SiteTypeifother":
                $text = "<p><b>Site Type (if other):</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_SiteLevel":
                $text = "<p><b>Site Level:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_LogoLevel":
                $text = "<p><b>Logo Level:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_Programming":
                $text = "<p><b>Programming:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_DomainName":
                $text = "<p><b>Domain Name:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_CurrentServer":
                $text = "<p><b>Current Server:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_PreviewLink":
                $text = "<p><b>Preview Link:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_HostIP":
                $text = "<p><b>Host IP:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_FTPUsername":
                $text = "<p><b>FTP Username:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_FTPPassword":
                $text = "<p><b>FTP Password:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ControlPanelURL":
                $text = "<p><b>Control Panel URL:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ControlPanelUsername":
                $text = "<p><b>Control Panel Username:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ControlPanelPassword":
                $text = "<p><b>Control Panel Password:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_SiteLive":
                $text = "<p><b>Site Status:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_CMSAdminUsername":
                $text = "<p><b>CMS Admin Username:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_CMSAdminPassword":
                $text = "<p><b>CMS Admin Password:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_StoreAdminUsername":
                $text = "<p><b>Store Admin Username:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_StoreAdminPassword":
                $text = "<p><b>Store Admin Password:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ProtectedDirectoryUsername":
                $text = "<p><b>Protected Directory Username:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
            case "_ProtectedDirectoryPassword":
                $text = "<p><b>Protected Directory Password:</b><br />" . $opp_field . "</p>";
                array_push($opp_notes, "$text");
                break;
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - -
    // Params passing to Teamwork
    // - - - - - - - - - - - - - - - - - - - - - -

    $opp_name = $opp_ordered['OpportunityTitle'];
    $opp_contact_name_array = $infusionsoft->contacts('xml')->load($opp_ordered['ContactID'], array('FirstName', 'LastName', 'Email'));
    $opp
    $opp_contact_name = implode(" ",$opp_contact_name_array);

    $opp_start_date = $opp_ordered['_StartDate']->format('Ymd');
    if (!empty($opp_ordered['NextActionDate'])){
        $opp_end_date = $opp_ordered['NextActionDate']->format('Ymd');
    } else{
        $opp_end_date = '20181231';
    }
    $opp_notes_content = implode("",$opp_notes);

endif;
