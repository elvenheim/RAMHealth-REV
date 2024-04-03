<?php require_once('../scripts/database_connect.php');?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RAM Health</title>
        <link rel="stylesheet" href="../styles/general.css">
        <link rel="stylesheet" href="../styles/homepage/homepage.css">
        <link rel="stylesheet" href="../styles/homepage/tutorial.css">
        <link rel="shortcut icon" href="../images/apc-logo.ico"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
        <script src="../scripts/logout.js"></script>
    </head>
    <body>
        <div class="navbar">
            <div class="logo-container" onclick="location.href='../Home/homepage.php';">
                <div class="ram-health-logo">RAM Health</div>
            </div>
            <div class="log-out-container">
                <div class="log-out">
                    <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" 
                        onselectstart="return false;" onclick="collapse_logout()">
                    </span>
                </div>
                <ul id="btn_logout" class="log-out-display" style="display: none;">
                    <form id="logout" name="logout-form" method="POST" action="../Login/session_logout.php">
                        <button class="logout-button" type="submit" name="logout">
                            <span class="fas fa-power-off"></span>
                            Logout
                        </button>
                    </form>
                </ul>
            </div>
        </div>
        <div class="page-content">
            <div class="homepage-card">
                <div class = "greet-user">
                    <span>
                        <?php include '../scripts/user_name.php'?>
                    </span>
                </div>
                <div class="role-list-card">
                    <?php include '../scripts/role_cards.php'?>
                </div>
                <div class="tutorial-container">
                    <button class="tutorial-button" type="button" onclick="show_tutorial()">TUTORIAL</button>
                    <div class="tutorial-background"></div>
                    <div class="tutorial-card" stlye="display: block;">
                        <div class="tutorial-header">
                            <span class="tutorial-title">Tutorial</span>
                            <span class="tutorial-close">X</span>
                        </div>
                        <div class="tutorial-content">
                            <video width="max-content" height="max-content" controls>
                            <source src="../Home/tutorial/kira the hedgehog.mp4" type="video/mp4">
                            Your browser does not support the video element.
                            </video>
                            <video width="max-content" height="max-content" controls>
                            <source src="../Home/tutorial/LEZ GO.mp4" type="video/mp4">
                            Your browser does not support the video element.
                            </video>
                            <audio controls>
                            <source src="../Home/tutorial/welcome to my WOOOOOOOOOOOOOOORLDDDDDD full.mp3" type="audio/mpeg">
                            Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
    function show_tutorial(){
        var tutorial = document.querySelector('.tutorial-card');
        var tutorial_background = document.querySelector('.tutorial-background');
        tutorial.style.display = 'block';
        tutorial_background.style.display = 'block';
    }
    function hide_tutorial(){
        var tutorial = document.querySelector('.tutorial-card');
        var tutorial_background = document.querySelector('.tutorial-background');
        tutorial.style.display = 'none';
        tutorial_background.style.display = 'none';
    }
    document.querySelector('.tutorial-close').addEventListener('click', hide_tutorial);
</script>