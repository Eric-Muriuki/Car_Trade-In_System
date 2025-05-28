<?php
session_start();
require_once '../includes/db_connect.php';

// Check dealer login
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch current dealer info
$sql = "SELECT * FROM dealers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$dealer = $result->fetch_assoc();

// Handle form submission
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = trim($_POST['business_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $contact_person = trim($_POST['contact_person']);
    $address = trim($_POST['address']);

    if ($business_name && $email && $phone && $contact_person && $address) {
        $update_sql = "UPDATE dealers SET business_name=?, email=?, phone=?, contact_person=?, address=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $business_name, $email, $phone, $contact_person, $address, $dealer_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh dealer info with updated values
            $dealer = [
                'business_name' => $business_name,
                'email' => $email,
                'phone' => $phone,
                'contact_person' => $contact_person,
                'address' => $address
            ];
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
