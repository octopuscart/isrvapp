<?php
$globleConnectDB = array();
try {
    $username = "bookbnev_padaiv";
    $password = "G7}=DZyZH5@Z";
    $conn = new PDO('mysql:host=localhost;dbname=bookbnev_isrvapp', $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
     $stmt = $conn->prepare('SELECT * FROM configuration_site');
    $stmt->execute();
    while($row = $stmt->fetch()) {
        $globleConnectDB = $row;
    }
    
    $stmt = $conn->prepare('SELECT * FROM configuration_report');
    $stmt->execute();
    while($row = $stmt->fetch()) {
        $globleConnectReport = $row;
    }
    
    $stmt = $conn->prepare('SELECT * FROM configuration_cartcheckout');
    $stmt->execute();
    while($row = $stmt->fetch()) {
        $globleConnectCartCheckout = $row;
    }
    
    
} catch(PDOException $e) {
 
}