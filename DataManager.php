<?php

class DataManager {

    private $DBConnection = null;

    public function __construct ($dbname) {
        $this -> DBConnection = new PDO ('mysql:dbname='.$dbname.';host=127.0.0.1', 'root', 'admin');
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
            INSERT INTO reports (phone, problem, start, reported, end, description, latitude, longitude)
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

    */

    public function getReports () {

        $stmt = $this -> DBConnection -> prepare ("
            SELECT *
            FROM reports
        ");

        $stmt -> execute ();
        return $stmt -> fetchAll (PDO::FETCH_ASSOC);
    }

    public function getProblems () {

        $stmt = $this -> DBConnection -> prepare ("
            SELECT DISTINCT problem
            FROM reports
        ");

        $stmt -> execute ();
        return $stmt -> fetchAll (PDO::FETCH_NUM);
    }

    public function getCroftsTest () {

        $distance = 500;
        
        // Get all crofts
        $stmt = $this -> DBConnection -> prepare ("
            SELECT c.id, c.name, c.latitude, c.longitude, 'normal' as state 
            FROM crofts c
            ");

        $stmt -> execute (); 
        $crofts = $stmt -> fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        // Run through all reports and find neighbour crofts
        $reports = $this -> getReports ();
        foreach ($reports as $report) {

            $stmt = $this -> DBConnection -> prepare ("
                CALL neighbours (:latitude, :longitude, :distance)
                ");

            $stmt -> bindParam ('latitude', $report['latitude']);
            $stmt -> bindParam ('longitude', $report['longitude']);
            $stmt -> bindParam ('distance', $distance);

            $stmt -> execute ();
            $affected = $stmt -> fetchAll (PDO::FETCH_ASSOC);
            foreach ($affected as $croft) {
                $crofts[$croft['id']][0]['state'] = 'endangered';
            }
        }

        // Find all crofts where a report has been filed for
        $stmt = $this -> DBConnection -> prepare ("
            SELECT c.id, c.name, c.latitude, c.longitude 
            FROM crofts c, reports r
            WHERE c.latitude - r.latitude < 0.1
                AND c.longitude - r.longitude < 0.1
        ");

        $stmt -> execute (); 
        $crisis = $stmt -> fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

        foreach ($crisis as $key => $croft) {
            $crofts [$key][0]['state'] = 'infected';
        }

        return array_values (array_map ('reset', $crofts));
    }

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

    public function getNeighbours ($phone, $croftName, $distance) {

        $stmt = $this -> DBConnection -> prepare  ("
            SELECT c.latitude, c.longitude
            FROM crofts c
            JOIN farmers f ON f.id = c.farmer_id
            WHERE
                f.phone = :phone
                AND c.name = :name
        ");

        $stmt -> bindParam ('phone', $phone);
        $stmt -> bindParam ('name', $croftName);
        $stmt -> execute ();
        $croft = $stmt -> fetch(PDO::FETCH_ASSOC);
        print_r($croft);
        $stmt = $this -> DBConnection -> prepare ("
            CALL neighbours(:latitude, :longitude, :distance)
            ");

        $stmt -> bindParam ('latitude', $croft['latitude']);
        $stmt -> bindParam ('longitude', $croft['latitude']);
        $stmt -> bindParam ('distance', $distance);

        $stmt -> execute ();
        return $stmt -> fetchAll (PDO::FETCH_ASSOC);
    }   
}
?>
