<?php 

/* prevent direct access */
if(!isset($_SERVER['HTTP_REFERER'])) 
{
    /* 
    Up to you which header to send, some prefer 404 even if 
    the files does exist for security
    */
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );

    /* choose the appropriate page to redirect users */
    die( header( 'location: error.php' ) );
}

// starting the session
session_start();
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/QuizBootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="js/QuizBootstrap.min.js"></script>
    <script src="js/QuizJquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Quiz.css">

    <link rel="stylesheet" href="css/QuizBootstrap450.min.css">
    <script src="js/QuizJquery1.min.js"></script>
    <script src="js/QuizPopper.min.js"></script>
    <script src="js/QuizBootstrap450.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>

    <script type="text/javascript">
        function mynewwindow()
        {
            window.open ("Quiz.php","mywindow","titlebar=no,mode=yes,fullscreen=yes,status=0,resizable=no,toolbar=0,height=10000,width=10000,location=no");
        }

        function mypendingwindow()
        {
            window.open ("QuizPending.php","mywindow","titlebar=no,mode=yes,fullscreen=yes,status=0,resizable=no,toolbar=0,height=10000,width=10000,location=no");
        }
                        
        window.onload = function () {
          document.onkeydown = function (e) {
            return (e.which || e.keyCode) != 116;
          };
        }

        function disableF5(e) 
        { 
            if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82 || (e.which || e.keyCode) == 17 || (e.which || e.keyCode) == 67 || (e.which || e.keyCode) == 18 || (e.which || e.keyCode) == 86 || (e.which || e.keyCode) == 88 || (e.which || e.keyCode) == 27 || (e.which || e.keyCode) == 8 || (e.which || e.keyCode) == 13 || (e.which || e.keyCode) == 166) 
            {
                e.preventDefault(); 
            }
        };

        $(document).ready(function()
        {
             $(document).on("keydown", disableF5);
        });
        
    </script>
    <title>QUIZ</title>

</head>

<body oncontextmenu="return false;">

    <style type="text/css">
        body{
            background-image: linear-gradient(to top, rgba(0, 140, 232, 0.6) 0%, rgba(255, 255, 255, 0.9) 100%), url('images/quiz-bg.jpg'); 
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-position: center center;
        }
        @media only screen and (max-width: 767px) {
            body {
                /* The file size of this background image is 93% smaller
                   to improve page load speed on mobile internet connections */
                background-image: url(images/quiz-bg-mobile.jpg);
            }
        }
    </style>
    <div class="container"> 
        <div class="form-horizontal center"> 
            <div class="form-group">
                <div class="row col-lg-11"> 
                    <?php
                        if($_SESSION['page']==1)
                        {
                    ?>    
                    <input type="submit" onclick="mynewwindow();" value="Begin Quiz" name="submit">
                    <?php
                        }
                        elseif($_SESSION['page']==2)
                        {
                    ?>
                    <input type="submit" onclick="mypendingwindow();" value="Resume Quiz" name="submit">
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div> 
    </div>

    <footer id="footer">
        <p>Designed By Kaushik Banerjee</p>
    </footer>

</body>

</html>
