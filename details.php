<?php 

require('templates/header.php');

if (isset($_GET['routeId'])) {
    $stmt = $pdo->query("SELECT * FROM routeschedules WHERE route_id = {$_GET['routeId']}");
    $routeSchedule = $stmt->fetchAll();
}

print_r($routeSchedule);

?>