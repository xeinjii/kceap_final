<?php
// check_deadline.php
$deadlineFile = __DIR__ . "/kceap_admin/deadline.json";
$collegeActive = true;
$hsActive = true;

if (file_exists($deadlineFile)) {
    $config = json_decode(file_get_contents($deadlineFile), true);

    $today = date("Y-m-d");

    require_once __DIR__ . "/config/config.php"; // DB connection

    // Check college
    if (isset($config['college']['limit']) && $config['college']['limit'] > 0) {
        $collegeCount = $conn->query("SELECT COUNT(*) AS total FROM college_schedule")->fetch_assoc()['total'];
        if ((int)$collegeCount >= $config['college']['limit']) {
            $collegeActive = false;
        }
    }

    // Check highschool
    if (isset($config['highschool']['limit']) && $config['highschool']['limit'] > 0) {
        $hsCount = $conn->query("SELECT COUNT(*) AS total FROM highschool_schedule")->fetch_assoc()['total'];
        if ((int)$hsCount >= $config['highschool']['limit']) {
            $hsActive = false;
        }
    }
}
