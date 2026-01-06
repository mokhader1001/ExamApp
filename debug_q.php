<?php
// Connect to DB with correct credentials from .env
$db = new mysqli('localhost', 'root', '', 'exam_app'); // using exam_app
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch all questions to see types
$result = $db->query("SELECT id, question_text, question_type FROM questions");
echo "ID | Type     | Text Match\n";
echo "---|----------|-----------\n";
while ($row = $result->fetch_assoc()) {
    $match = (stripos($row['question_text'], 'html') !== false) ? 'YES' : 'NO';
    if ($match === 'YES') {
        echo $row['id'] . " | " . $row['question_type'] . " | " . $row['question_text'] . "\n";
    }
}
echo "\nChecking distinct types again:\n";
$result = $db->query("SELECT DISTINCT question_type FROM questions");
while ($row = $result->fetch_assoc()) {
    echo "- '" . $row['question_type'] . "'\n";
}
