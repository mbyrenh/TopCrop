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

    public function addCroft ($phone, $croftName, $latitude, $longitude) {

        $stmt = $this -> DBConnection -> prepare ("
            INSERT INTO crofts (farmer_id, name, longitude, latitude)
            SELECT id, :name, :longitude, :latitude
            FROM farmers
            WHERE phone = :phone
        ");

        $stmt -> bindParam ('longitude', $longitude);
        $stmt -> bindParam ('latitude', $longitude);
        $stmt -> bindParam ('name', $croftName);
        $stmt -> bindParam ('phone', $phone);

        $stmt -> execute ();
    }

    public function addReport ($phone, $croftName, $problem, $start, $description, $end = NULL) {

        $stmt = $this -> DBConnection -> prepare ("
            INSERT INTO reports (croft_id, problem, start, reported, end, description)
            SELECT c.id, :problem, :start, NOW(), :end, :description 
            FROM farmers f
            JOIN crofts c ON c.farmer_id = f.id
            WHERE f.phone = :phone AND c.name = :name
        ");

        $stmt -> bindParam ('name', $name);
        $stmt -> bindParam ('problem', $problem);
        $stmt -> bindParam ('start', $start);
        $stmt -> bindParam ('end', $end);
        $stmt -> bindParam ('description', $description);
        $stmt -> bindParam ('phone', $phone);

        $stmt -> execute ();

    }
}
?>
