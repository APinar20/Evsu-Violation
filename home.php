<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Violation List</title>
    <link rel="stylesheet" href="home.css">
    <script>
        function confirmLogout() {
            const confirmAction = confirm("Are you sure you want to logout?");
            if (confirmAction) {
                window.location.href = 'index.php';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>EVSU-OCC VIOLATION LIST</h1>
        <nav>
            <button class="logout-btn" onclick="confirmLogout()">Logout</button>
        </nav>
    </header>

    <div class="container">
        <input type="text" placeholder="Search...">
        <button class="search-btn">Search</button>

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>MI</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Violation</th>
                    <th>To-do Fix Violation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example Row -->
                <tr>
                    <td>20230001</td>
                    <td>Juan</td>
                    <td>Dela Cruz</td>
                    <td>M</td>
                    <td>BSIT</td>
                    <td>3A</td>
                    <td>No ID</td>
                    <td>Wear ID daily</td>
                    <td>
                        <button class="edit-btn">Edit</button>
                        <button class="delete-btn">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="nextButton" class="add-btn">Add Student</button>
    </div>
</body>
<script>
        document.getElementById("nextButton").addEventListener("click", function() {
            window.location.href = "add.php";
        });
    </script>
</html>
