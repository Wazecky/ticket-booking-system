<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Booking System</title>
    <link rel="stylesheet" href="Style/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            overflow: auto; /* Allow content to be scrollable */
        }

        /* Image background */
        #image-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Place behind other elements */
            object-fit: cover; /* Ensure image covers entire viewport */
            opacity: 0.7; /* Set the opacity of the background image */
        }

        /* Centered content */
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 1; /* Place above image */
        }

        h1 {
            color: blue;
            font-size: 3em;
            position: relative; /* Ensure button remains within h1's container */
            display: block; /* Ensure button takes full width */
            margin-bottom: 20px; /* Add space between h1 and button */
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #009999;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1.2em;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include_once 'admin/nav.php'; ?>
    <!-- Image background -->
    <img src="Style/bg2.jpg" id="image-background" alt="Background Image">
    
    <!-- Content -->
    <div class="content">
        <h1>MODERNMAN TICKET BOOKING SYSTEM</h1>
        <button onclick="window.location.href='user/login.php'" class="btn">BOOK NOW</button>
    </div>
</body>
</html>