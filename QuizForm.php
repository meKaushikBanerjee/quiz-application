<?php 
// starting the session
session_start();
$_SESSION['quizid']="Q_2";
/*if(isset($_GET['quiz']))
{
    $_SESSION['quizid']=strtoupper($_GET['quiz']);
}
/*else
{
    echo "<script>alert('Kindly enter the Quiz link correctly!');</script>";
    echo '<script>window.location.replace("10.129.7.157/sailquiz")</script>';
}*/

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
        window.onload = function () {
          document.onkeydown = function (e) {
            return (e.which || e.keyCode) != 116;
          };
        }

        function disableF5(e) 
        { 
            if ((e.which || e.keyCode) == 17 || (e.which || e.keyCode) == 18 || (e.which || e.keyCode) == 27) 
            {
                e.preventDefault(); 
            }
        };

        $(document).ready(function()
        {
             $(document).on("keydown", disableF5);
        });
        
    </script>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
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

    <div class="col-lg-12 no-gutters"><img src="Images/Steel_Authority_of_India_logo.svg.png" class="mx-auto d-block img-fluid logo" alt="sail-logo"></div>

    <div class="container">
        
        <form class="form-horizontal center" action="UserFormAction.php" method="POST">

                <!---START DETAILS PART -->
                      
                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="qid">Enter Name:<span class="required">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="uname" placeholder="Enter Name" name="uname" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="qno">Enter P No:<span class="required">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="upno" placeholder="Enter P No" name="upno" required maxlength="7">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="qno">Location/Place of posting (For Calcutta Branch BSO Cal may be written):<span class="required">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="uloc" placeholder="Enter your Location/ Place of posting" name="uloc" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="grp">You Belong to Group :</label>
                        <div class="col-sm-7">
                            <select name="ugrp" id="ugrp" required>
                                <option value="">Select your group</option>
                                <option value="NR">NR, BSOs, CCOs, Warehouses, CD & ITD</option>
                                <option value="WR">WR, BSOs, CCOs, Warehouses, SRM Bhilai</option>
                                <option value="ER">ER, BSOs, CCOs, SRM Bokaro, Rourkela, Durgapur, ISP, ASP, BTSO Kolkata, Haldia, T&S Regional Office, Kolkata</option>
                                <option value="SR">SR, BSOs,CCOs, Warehouses,SRM Salem ,SRM Bhadravati, BTSO Vizag, Paradip and T&S Regional Office, Vizag</option>
                                <option value="HQ">All Departments of Ispat Bhawan, HQ Kolkata including T&S </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!--- END DETAILS PART -->

                <div class="form-group">
                    <div class="row">        
                        <div class="col-sm-offset-2 col-sm-11">
                            <input type="submit" value="Submit" name="submit">
                        </div>
                    </div>
                </div>

            </form>
    </div>

    <footer id="footer">
        <p>Maintained by: ERP | SAILCMO</p>
    </footer>

</body>

</html>