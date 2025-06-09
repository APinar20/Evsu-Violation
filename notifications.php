<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - EVSU Violation System</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .notifications-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .notification-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .notification-title {
            font-weight: bold;
            color: #333;
        }
        
        .notification-date {
            color: #666;
            font-size: 0.9em;
        }
        
        .notification-content {
            color: #444;
            line-height: 1.5;
        }
        
        .back-btn {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #3f0606;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .back-btn:hover {
            background-color: #5a0000;
        }
    </style>
</head>
<body>
    <header>
        <h1>Notifications</h1>
        <nav>
            <button class="back-btn" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        </nav>
    </header>

    <div class="notifications-container">
        <?php
        // Fetch notifications from the database
        $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="notification-card">';
                echo '<div class="notification-header">';
                echo '<span class="notification-title">' . htmlspecialchars($row['title']) . '</span>';
                echo '<span class="notification-date">' . date('M d, Y H:i', strtotime($row['created_at'])) . '</span>';
                echo '</div>';
                echo '<div class="notification-content">' . htmlspecialchars($row['message']) . '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="notification-card">';
            echo '<div class="notification-content">No notifications found.</div>';
            echo '</div>';
        }
        ?>
    </div>

    <script>
        // Function to fetch new notifications periodically
        function fetchNewNotifications() {
            fetch('get_notifications.php')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        // Update notifications list
                        const container = document.querySelector('.notifications-container');
                        data.notifications.forEach(notification => {
                            const card = document.createElement('div');
                            card.className = 'notification-card';
                            card.innerHTML = `
                                <div class="notification-header">
                                    <span class="notification-title">${notification.title}</span>
                                    <span class="notification-date">${new Date(notification.created_at).toLocaleString()}</span>
                                </div>
                                <div class="notification-content">${notification.message}</div>
                            `;
                            container.insertBefore(card, container.firstChild);
                        });
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Fetch new notifications every 30 seconds
        setInterval(fetchNewNotifications, 30000);
    </script>
</body>
</html> 