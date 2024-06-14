<?php
include "connection.php";
$sql = "SELECT id, name FROM pdf_files";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Uploaded PDFs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>List of Uploaded PDFs</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td><a href='view_pdf1.php?id=" . $row['id'] . "' target='_blank'>View</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No PDFs found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<?php
$conn->close();
?>
