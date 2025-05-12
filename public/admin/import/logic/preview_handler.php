<?php
include '../../../includes/connect-db.php';

if (isset($_FILES['student_file'])) {
    $file = $_FILES['student_file']['tmp_name'];
    $ext = pathinfo($_FILES['student_file']['name'], PATHINFO_EXTENSION);

    if ($ext != 'csv') {
        die("<p class='text-red-500'>Only CSV files are supported.</p>");
    }

    $handle = fopen($file, "r");
    $header = fgetcsv($handle);

    $students = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $students[] = [
            'f_name' => $data[0],
            'l_name' => $data[1],
            'year' => $data[2],
            'email' => $data[3],
            'student_id' => $data[4],
            'password' => $data[5],
        ];
    }
    fclose($handle);

    // Display preview table
    echo "<table class='w-full text-sm text-left text-gray-700 border'>";
    echo "<thead class='bg-black text-center text-white'><tr><th>First</th><th>Last</th><th>Year</th><th>Email</th><th>ID</th><th>Status</th></tr></thead><tbody>";

    $students_to_insert = [];
    foreach ($students as $stu) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE student_id = ?");
        $stmt->execute([$stu['student_id']]);
        $exists = $stmt->fetchColumn();

        $status = $exists ? "<span class='text-red-500'>Already Exists</span>" : "<span class='text-green-600'>Will Insert</span>";

        if (!$exists) {
            $students_to_insert[] = $stu;
        }

        echo "<tr class='border-b'>
                <td class='p-2'>{$stu['f_name']}</td>
                <td class='p-2'>{$stu['l_name']}</td>
                <td class='p-2'>{$stu['year']}</td>
                <td class='p-2'>{$stu['email']}</td>
                <td class='p-2'>{$stu['student_id']}</td>
                <td class='p-2'>$status</td>
              </tr>";
    }
    echo "</tbody></table>";

    if (count($students_to_insert) > 0) {
        // Encode the array into JSON
        $json_students = htmlspecialchars(json_encode($students_to_insert), ENT_QUOTES, 'UTF-8');

        // Hidden input to carry this JSON for next step
        echo "<input type='hidden' id='studentsJson' value='$json_students'>";

        // Confirm button
        echo "<button id='confirmInsert' class='mt-4 cursor-pointer  bg-black text-white py-2 px-4 rounded hover:bg-gray-800'>Confirm Insert</button>";
    } else {
        echo "<p class='mt-4 text-yellow-600'>No new students to insert.</p>";
    }

} else {
    echo "<p class='text-red-500'>No file uploaded.</p>";
}
?>