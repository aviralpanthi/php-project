<?php
// includes/Member.php
require_once 'Database.php';

class Member {
    private $db;

    public function __construct() {
        // Initialize the database connection
        $this->db = (new Database())->connection;
    }

    // Retrieve members by parentId, and recursively fetch children
    public function getMembers($parentId = 0) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Members WHERE ParentId = :parentId");
            $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch members as an associative array
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];

            // Recursively fetch children for each member
            foreach ($members as $member) {
                $children = $this->getMembers($member['Id']);  
                $member['children'] = $children;
                $result[] = $member;
            }

            return $result;
        } catch (PDOException $e) {
            // Log error message for debugging
            error_log("Error fetching members: " . $e->getMessage());
            return false;
        }
    }

    // Add a new member to the database
    public function addMember($name, $parentId) {
        try {
            // Get the current date and time in MySQL-compatible format (Y-m-d H:i:s)
            $currentDate = date('Y-m-d H:i:s');
            
            // Prepare the SQL query to insert the new member with current date and time
            $stmt = $this->db->prepare("INSERT INTO Members (Name, ParentId, CreatedDate) VALUES (:name, :parentId, :createdDate)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
            $stmt->bindParam(':createdDate', $currentDate, PDO::PARAM_STR);
            
            // Execute the query and check if it was successful
            if ($stmt->execute()) {
                // Return the ID of the newly added member
                return $this->db->lastInsertId();
            } else {
                return false; // Failure if the query did not execute
            }
        } catch (PDOException $e) {
            // Log the error message and return false
            error_log("Error adding member: " . $e->getMessage());
            return false;
        }
    }
    

    // Get the parent name by parentId
    public function getParentName($parentId) {
        try {
            // Fetch the parent member by ParentId
            $stmt = $this->db->prepare("SELECT Name FROM Members WHERE Id = :parentId");
            $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the result and return the parent's name
            $parent = $stmt->fetch(PDO::FETCH_ASSOC);
            return $parent ? $parent['Name'] : ''; // Return an empty string if no parent found
        } catch (PDOException $e) {
            // Log the error message and return an empty string if error occurs
            error_log("Error fetching parent name: " . $e->getMessage());
            return '';
        }
    }
}
?>
