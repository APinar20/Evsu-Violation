<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        if ($password === $db_password) {
            $_SESSION['admin_user'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect username or password.";
        }
    } else {
        $error = "Incorrect username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EVSU OCC Student Violation</title>
    <link rel="stylesheet" href="index.css" />
</head>
<body>
    <header>
        <h1>EVSU - OCC LIBRARY STUDENT VIOLATION</h1>
        <nav>
            <button id="startButton">Login</button>
        </nav>
    </header>

    <div class="main-content-wrapper">
        <div class="info-box">
            <div class="scroll-box">
                <p>
                    EVSU-OCC enforces library rules to ensure a good learning environment. 
                    However, students often break these rules, causing disturbances.
                    Common issues include loud talking, bringing food and drinks, returning books late,
                    misusing computers, leaving trash, and damaging furniture.
                    Others enter without IDs or have group discussions in silent areas.
                    Stricter penalties are suggested for repeat offenders.
                </p>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="login-sidebar" id="loginSidebar">
        <div class="form-container">
            <h2>EVSU - Admin Login</h2>
            <form method="POST" action="">
                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required />

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />

                <br><br>
                <button type="submit">LOGIN</button>
            </form>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById("loginSidebar");
        const overlay = document.getElementById("overlay");

        document.getElementById("startButton").addEventListener("click", () => {
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });

        function closeSidebar() {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        }
    </script>
</body>
</html>
