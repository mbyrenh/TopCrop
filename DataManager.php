<?php

class DataManager {

    private $DBConnection = null;

    public function __construct () {
        $this -> DBConnection = new PDO ('mysql:dbname=cropapp;host=127.0.0.1', 'root', 'admin');
    }

    public function addFarmer ($phone) {

        $stmt = $this -> DBConnection -> prepare ("
            INSERT INTO farmers (phone)
            VALUES (:phone)
        ");

        $stmt -> bindParam ('phone', $phone, PDO::PARAM_STR);
        $stmt -> execute ();
    }

    public function farmerExists ($phone) {

        $stmt = $this -> DBConnection -> prepare ("
            SELECT COUNT(*)
            FROM farmers
            WHERE phone = :phone
        ");

        $stmt -> bindParam ('phone', $phone, PDO::PARAM_STR);
        $stmt -> execute ();
        return (intval($stmt->fetchColumn()) == 1);
    }

    public function addCroft ($phone, $croftName, $latitude, $longitude) {

        $stmt = $this -> DBConnection -> prepare ("
            INSERT INTO crofts (farmer_id, name, longitude, latitude)
            SELECT id, :name, :longitude, :latitude
            FROM farmers
            WHERE phone = :phone
        ");

        $stmt -> bindParam ('longitude', $longitude);
        $stmt -> bindParam ('latitude', $latitude);
        $stmt -> bindParam ('name', $croftName);
        $stmt -> bindParam ('phone', $phone);

        $stmt -> execute ();
    }

    public function addReport ($phone, $problem, $start, $description, $latitude, $longitude, $ongoing) {

        $stmt = $this -> DBConnection -> prepare ("
            INSERT INTO reports (:phone, problem, start, reported, end, description, latitude, longitude)
            VALUES (:phone, :problem, :start, NOW(), :end, :description, :latitude, :longitude) 
        ");

        $stmt -> bindParam ('problem', $problem);
        $stmt -> bindParam ('start',(new Datetime($start))->format('Y-m-d H:i:s'));
        if ($ongoing) {
            $stmt -> bindValue ('end', NULL);
        } else {
            $stmt -> bindValue ('end', (new Datetime())->format('Y-m-d H:i:s'));
        }
       
        $stmt -> bindParam ('description', $description);
        $stmt -> bindParam ('latitude', $latitude);
        $stmt -> bindParam ('longitude', $longitude);
        $stmt -> bindParam ('phone', $phone);
        $stmt -> execute ();
    }

    public function updatePhone ($oldNumber, $newNumber) {

        $stmt = $this -> DBConnection -> prepare ("
            UPDATE farmers 
            SET phone = :newPhone
            WHERE phone = :phone
        ");

        $stmt -> bindParam ('phone', $oldNumber);
        $stmt -> bindParam ('newPhone', $newNumber);

        $stmt -> execute ();
    } 
/*
    public function getReportsForFarmer ($phone) {
        
        $stmt = $DBConnection -> prepare ("
            SELECT *
            FROM reports
            WHERE owner     
        ");
    }

    public function updateReport ($report_id, $problem, $start, $description, $end = NULL) {
        
    }



    public function getReports ($croftName) {

    }

    public function revokeReport ($)
*/
    public function getCrofts ($phone = NULL) {

        if ($phone !== NULL) {
            $stmt = $this -> DBConnection -> prepare ("
                SELECT * 
                FROM crofts c
                JOIN farmers f ON c.farmer_id = f.id
                AND f.phone = :phone
                ");

            $stmt -> bindParam ('phone', $phone);
        } else {
            $stmt = $this -> DBConnection -> prepare ("
                SELECT * 
                FROM crofts c
                JOIN farmers f ON c.farmer_id = f.id
                ");
        }
        $stmt -> execute ();

        return $stmt->fetchAll (PDO::FETCH_ASSOC);
    }

    /*
    public function getNeighbours ($phone, $croftName, $distance) {

        $stmt = $this -> DBConnection -> prepare ("
            SELECT f.phone, (((acos( 
                                sin(
                                 ((:latitude)*pi()/180)) * sin((c.latitude*pi()/180))+cos((:latitude)*pi()/180))
                                 * cos((c.latitude*pi()/180)) * cos((((:longitude)-c.longitude)*pi()/180)))
                                 *180/pi())*60*1.1515) as distance 
            FROM farmers f
            JOIN crofts c ON c.farmer_id = f.id
            WHERE distance <= :distance
            ");



        /*
         *  "SELECT *,(((acos(sin((".$latitude."*pi()/180)) * 
sin((`Latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * 
cos((`Latitude`*pi()/180)) * cos(((".$longitude."- `Longitude`)*
pi()/180))))*180/pi())*60*1.1515) as distance FROM `MyTable` 
WHERE distance <= ".$distance."         *


        $stmt -> bindParam ('name', $name);
        $stmt -> bindParam ('problem', $problem);
        $stmt -> bindParam ('start', $start);
        $stmt -> bindParam ('end', $end);
        $stmt -> bindParam ('description', $description);
        $stmt -> bindParam ('phone', $phone);

        $stmt -> execute ();


}*/
}
?>
