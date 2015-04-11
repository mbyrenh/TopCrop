<?php

require_once ('DataManager.php');

if (! isset ($_REQUEST['operation'])) {
    die ('No operation given!');
}

switch ($_REQUEST['operation']) {

    case 'register':
        $phone = $_REQUEST['phone'];
        $DataManager = new DataManager ();
        $DataManager -> addFarmer ($phone);
        echo "Done!";
    break;

}

?>
