<?php
// user/process_offer.php
session_start();
include('../db-connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offer_id = intval($_POST['offer_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        $update = "UPDATE trade_offers SET status = 'accepted' WHERE id = $offer_id AND to_user_id = {$_SESSION['user_id']}";
        mysqli_query($conn, $update);
    } elseif ($action === 'counter') {
        $update = "UPDATE trade_offers SET status = 'countered' WHERE id = $offer_id AND to_user_id = {$_SESSION['user_id']}";
        mysqli_query($conn, $update);
    }

    header("Location: trade_offers.php");
    exit();
}
?>
