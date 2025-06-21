<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php include 'navbar.php'; ?>
    <h1>Admin Dashboard</h1>
    <section id="dashboard">
        <h2>Welcome, Admin!</h2>
        <p>Here you can manage users, view reports, and adjust settings.</p>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="settings.php">Settings</a></li>
            </ul>
        </nav>
        
    </section>
<?php include 'footer.php'; ?>
</body>   
</html>