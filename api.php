<?php

/**
 * Continue: - API for changing the phone number
 *             -> pest reports: Check date of last change against date of report submission
 *           - listCrofts -> extend by optional number-parameter to restrict results
 *
 * Next Step: Implementation and testing of notification
 */

require_once ('DataManager.php');

if (! isset ($_REQUEST['operation'])) {
    die ('No operation given!');
}

switch ($_REQUEST['operation']) {

    case 'register':
        if (! isset ($_REQUEST['phone']) || strlen ($_REQUEST['phone']) == 0) {
            $result = [
                'result' => -1,
                'msg'    => 'No phone number given!'
            ];
        } else {
            $phone = $_REQUEST['phone'];
            $DataManager = new DataManager ();
            $DataManager -> addFarmer ($phone);
            $result = [
                'result' => 1
            ];
        } 
    break;

    case 'changeNumber':

        $oldNumber = $_REQUEST['oldNumber'];
        $newNumber = $_REQUEST['number'];
        $DataManager = new DataManager ();
        $DataManager -> updatePhone ($oldNumber, $newNumber);
        
        $result = [
                'result' => 0
        ];
    break;

    case 'listCrofts':
        $DataManager = new DataManager ();
        if (isset ($_REQUEST['number']) && is_numeric($_REQUEST['number'])) {
            $crofts = $DataManager -> getCrofts ($_REQUEST['number']);
        } else {
            $crofts = $DataManager -> getCrofts ();
        }
        $result = [
            'result' => 0,
            'data'   => $crofts
        ];

    break;

    case 'addReport':

        $phone = $_REQUEST['number'];
        $problem = $_REQUEST['condition'];
        $start = $_REQUEST['startDate'];
        $ongoing = $_REQUEST['onGoing'];
        $description = $_REQUEST['description'];
        $latitude = $_REQUEST['lat'];
        $longitude = $_REQUEST['lng'];

        $DataManager = new DataManager ();
        $DataManager -> addReport (
            $phone,
            $problem,
            $start,
            $description,
            $latitude,
            $longitude,
            $ongoing);

        $result = [
            'result' => 0,
        ];
    break;

    case 'addCroft': 
        if (! isset ($_REQUEST['number']) || strlen ($_REQUEST['number']) == 0) {
            $result = [
                'result' => -1,
                'msg'    => 'No phone number given!'
            ];
        } 
        elseif (! isset ($_REQUEST['name']) || strlen ($_REQUEST['name']) == 0) {
            $result = [
                'result' => -1,
                'msg'    => 'No croft name given!'
            ];
        }
        elseif (! isset ($_REQUEST['lat']) || is_numeric ($_REQUEST['lat']) == FALSE) {
            $result = [
                'result' => -1,
                'msg'    => 'No or invalid latitude given!'
            ];
        }       if (! isset ($_REQUEST['lng']) || is_numeric ($_REQUEST['lng']) == FALSE) {
            $result = [
                'result' => -1,
                'msg'    => 'No or invalud longitude given!'
            ];
        } else {
            $phone = $_REQUEST['number'];
            $croftName = $_REQUEST['name'];
            $latitude = $_REQUEST['lat'];
            $longitude = $_REQUEST['lng'];

            $DataManager = new DataManager ();

            if (! $DataManager->farmerExists ($phone)) {
                $DataManager -> addFarmer ($phone);
            }
        
            $DataManager -> addCroft ($phone, $croftName, $latitude, $longitude);
            $result = [
                'result' => 0
            ];
        }
        break;

    default:
        $result =  [
            'result' => -1,
            'msg'    => 'Unknown operation given!'
        ];
}

echo json_encode ($result);

?>
