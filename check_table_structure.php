<?php
require 'db_connect.php';

$result = $conn->query("DESCRIBE announcement_recipients");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
