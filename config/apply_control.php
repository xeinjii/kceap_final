<?php
// Application Control System
// This file handles the logic for enabling/disabling the apply button based on:
// 1. Manual admin control (enabled/disabled)
// 2. Deadline date
// 3. Application limit

function isApplyButtonEnabled() {
    $isApplyActive = true;
    $deadlineFile = __DIR__ . "/../kceap_admin/deadline/deadline.json";
    
    // Check if the deadline file exists
    if (file_exists($deadlineFile)) {
        $config = json_decode(file_get_contents($deadlineFile), true);
        
        // For general apply button, check if either college or highschool is enabled
        $collegeActive = !isset($config['college']['disabled']) || !$config['college']['disabled'];
        $hsActive = !isset($config['highschool']['disabled']) || !$config['highschool']['disabled'];
        
        if (!$collegeActive && !$hsActive) {
            return false;
        }
        
        // Check deadlines for both
        $now = time();
        if (!empty($config['college']['deadline'])) {
            $deadlineTs = strtotime($config['college']['deadline']);
            if ($deadlineTs !== false && $now > $deadlineTs) {
                $collegeActive = false;
            }
        }
        if (!empty($config['highschool']['deadline'])) {
            $deadlineTs = strtotime($config['highschool']['deadline']);
            if ($deadlineTs !== false && $now > $deadlineTs) {
                $hsActive = false;
            }
        }
        
        // If both are inactive due to deadlines, disable apply button
        if (!$collegeActive && !$hsActive) {
            return false;
        }
        
        // Check limits
        require_once __DIR__ . "/config.php"; // Get database connection
        global $conn; // Make the connection variable accessible in this function
        
        if (isset($config['college']['limit']) && $config['college']['limit'] > 0) {
            $stmt = $conn->query("SELECT COUNT(*) AS total FROM college_account");
            $row = $stmt->fetch_assoc();
            if ($row && $row['total'] >= $config['college']['limit']) {
                $collegeActive = false;
            }
        }
        
        if (isset($config['highschool']['limit']) && $config['highschool']['limit'] > 0) {
            $stmt = $conn->query("SELECT COUNT(*) AS total FROM highschool_account");
            $row = $stmt->fetch_assoc();
            if ($row && $row['total'] >= $config['highschool']['limit']) {
                $hsActive = false;
            }
        }
        
        // If both are inactive, disable apply button
        if (!$collegeActive && !$hsActive) {
            return false;
        }
    }
    
    return $isApplyActive;
}

function isCollegeApplicationEnabled() {
    $deadlineFile = __DIR__ . "/../kceap_admin/deadline/deadline.json";
    
    if (file_exists($deadlineFile)) {
        $config = json_decode(file_get_contents($deadlineFile), true);
        
        // Check if manually disabled
        if (isset($config['college']['disabled']) && $config['college']['disabled']) {
            return false;
        }
        
        // Check deadline
        if (!empty($config['college']['deadline'])) {
            $now = time();
            $deadlineTs = strtotime($config['college']['deadline']);
            if ($deadlineTs !== false && $now > $deadlineTs) {
                return false;
            }
        }
        
        // Check limit
        if (isset($config['college']['limit']) && $config['college']['limit'] > 0) {
            require_once __DIR__ . "/config.php";
            global $conn;
            $stmt = $conn->query("SELECT COUNT(*) AS total FROM college_account");
            $row = $stmt->fetch_assoc();
            if ($row && $row['total'] >= $config['college']['limit']) {
                return false;
            }
        }
    }
    
    return true;
}

function isHighSchoolApplicationEnabled() {
    $deadlineFile = __DIR__ . "/../kceap_admin/deadline/deadline.json";
    
    if (file_exists($deadlineFile)) {
        $config = json_decode(file_get_contents($deadlineFile), true);
        
        // Check if manually disabled
        if (isset($config['highschool']['disabled']) && $config['highschool']['disabled']) {
            return false;
        }
        
        // Check deadline
        if (!empty($config['highschool']['deadline'])) {
            $now = time();
            $deadlineTs = strtotime($config['highschool']['deadline']);
            if ($deadlineTs !== false && $now > $deadlineTs) {
                return false;
            }
        }
        
        // Check limit
        if (isset($config['highschool']['limit']) && $config['highschool']['limit'] > 0) {
            require_once __DIR__ . "/config.php";
            global $conn;
            $stmt = $conn->query("SELECT COUNT(*) AS total FROM highschool_account");
            $row = $stmt->fetch_assoc();
            if ($row && $row['total'] >= $config['highschool']['limit']) {
                return false;
            }
        }
    }
    
    return true;
}

function getApplicationStatus() {
    $status = [
        'isActive' => true,
        'reason' => '',
        'remainingSlots' => null,
        'deadline' => null
    ];
    
    $deadlineFile = __DIR__ . "/../kceap_admin/deadline/deadline.json";
    
    if (file_exists($deadlineFile)) {
        $config = json_decode(file_get_contents($deadlineFile), true);
        
        // Set deadline info
        if (!empty($config['deadline'])) {
            $status['deadline'] = $config['deadline'];
        }
        
        // Check if manually disabled
        if (isset($config['disabled']) && $config['disabled']) {
            $status['isActive'] = false;
            $status['reason'] = 'Applications are currently closed by administrator.';
            return $status;
        }
        
        // Check deadline
        if (!empty($config['deadline'])) {
            $now = time();
            $deadlineTs = strtotime($config['deadline']);
            if ($deadlineTs !== false && $now > $deadlineTs) {
                $status['isActive'] = false;
                $status['reason'] = 'Application deadline has passed.';
                return $status;
            }
        }
        
        // Check applicant limit
        if (isset($config['limit']) && $config['limit'] > 0) {
            require_once __DIR__ . "/config.php"; // Get database connection
            global $conn; // Make the connection variable accessible in this function
            $stmt = $conn->query("SELECT COUNT(*) AS total FROM applicants");
            $row = $stmt->fetch_assoc();
            
            if ($row) {
                $total = (int)$row['total'];
                $limit = (int)$config['limit'];
                $status['remainingSlots'] = $limit - $total;
                
                if ($total >= $limit) {
                    $status['isActive'] = false;
                    $status['reason'] = 'Application limit has been reached.';
                    return $status;
                }
            }
        }
    }
    
    return $status;
}