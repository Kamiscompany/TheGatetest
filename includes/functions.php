<?php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_if_not_logged_in() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /fifi/welcome.php');
        exit();
    }
}
?>

