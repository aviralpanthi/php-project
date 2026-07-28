<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the Member class
require_once 'includes/Member.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming 'name' and 'parent' are sent from the frontend
    $name = $_POST['name'];
    $parentId = $_POST['parent'];

    // Create an instance of the Member class
    $member = new Member();

    // Add the member and get the result
    $result = $member->addMember($name, $parentId);

    // Prepare the response array
    $response = [];

    if ($result) {
        // Success, return the added member's name
        $response['success'] = true;
        $response['name'] = $name;

        // If the parent ID is not 0, include the parent's name for appending
        if ($parentId != 0) {
            // Get the parent's name using the new method
            $parentName = $member->getParentName($parentId);
            $response['parentName'] = $parentName;
        }
    } else {
        // Failure, return an error message
        $response['success'] = false;
        $response['message'] = 'Failed to add member.';
    }

    // Send the response as JSON
    echo json_encode($response);
}


?>