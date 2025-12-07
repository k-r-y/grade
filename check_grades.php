<?php
require 'db_connect.php';
$res = $conn->query("SELECT * FROM grades LIMIT 5");
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No grades found.";
}
