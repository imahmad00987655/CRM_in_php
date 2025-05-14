<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
require 'VisaApplicationObserver.php';

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $action = $data['action'];

        // Fetch all countries for dropdown
        if ($action === 'getCountries') {
            $stmt = $pdo->query("SELECT * FROM countries");
            $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($countries);
        }
        // Fetch pricing details for a specific country
        if ($action === 'getPricing') {
            $country_id = $data['country_id'];
            $stmt = $pdo->prepare("SELECT * FROM visa_charges WHERE country_id = ?");
            $stmt->execute([$country_id]);
            $pricing = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($pricing);
        }

        // Update pricing details
        if ($action === 'updatePricing') {
            $country_id = $data['country_id'];
            $single_person = $data['single_person'];
            $couple = $data['couple'];
            $family_3 = $data['family_3'];
            $family_4 = $data['family_4'];

            // Check if the record exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM visa_charges WHERE country_id = ?");
            $stmt->execute([$country_id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update if record exists
                $stmt = $pdo->prepare("
                    UPDATE visa_charges 
                    SET single_person = ?, couple = ?, family_3 = ?, family_4 = ? 
                    WHERE country_id = ?
                ");
                $stmt->execute([$single_person, $couple, $family_3, $family_4, $country_id]);
            } else {
                // Insert if record does not exist
                $stmt = $pdo->prepare("
                    INSERT INTO visa_charges (country_id, single_person, couple, family_3, family_4) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$country_id, $single_person, $couple, $family_3, $family_4]);
            }

            echo json_encode(['success' => true]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
