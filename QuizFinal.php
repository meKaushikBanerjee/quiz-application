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
include('includes/dbcontroller.php');

$qid=$_SESSION['quizid'];
$u_pno=$_SESSION['u_pno'];
$u_name=$_SESSION['u_name'];

$sql="SELECT QUIZ_DATE,QUIZ_S_TIME,QUIZ_E_TIME,UNANSWERED_QS_TIME from quizmaster where QUIZ_ID='$qid'";
$query = $dbh->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
    foreach($results as $result)
    {
        $qstime = $result->QUIZ_S_TIME;
        $udate = $result->QUIZ_DATE;
        $qetime = $result->QUIZ_E_TIME;
        $uqdura = $result->UNANSWERED_QS_TIME;
    }
}

date_default_timezone_set("Asia/Calcutta");
$date = date('d-m-Y');
$time = date('H:i:s');

$date = array_map('intval',explode('-',$date,3));
$date = $date[0].$date[1].$date[2];

$time = array_map('intval',explode(':',$time,3));
$hours = sprintf('%02d',(int) $time[0]);
$minutes = sprintf('%02d',(int) $time[1]);
$seconds = sprintf('%02d',(int) $time[2]);
$time = $hours.$minutes.$seconds;

$qstime = date('H:i',strtotime($qstime));
$qstime = array_map('intval',explode(':',$qstime,2));
$qshours = sprintf('%02d',(int) $qstime[0]);
$qsminutes = sprintf('%02d',(int) $qstime[1]);
$qstime = $qshours.$qsminutes."00";

$qetime = date('H:i',strtotime($qetime));
$qetime = array_map('intval',explode(':',$qetime,2));
$qehours = sprintf('%02d',(int) $qetime[0]);
$qeminutes = sprintf('%02d',(int) $qetime[1]);
$qetime = $qehours.$qeminutes."00";

$udate = array_map('intval',explode('-',$udate,3));
$udate = $udate[0].$udate[1].$udate[2];

if(($time>=$qstime)&&($time<=$qetime))
{ 
    $qetime = strtotime($qetime);
    $time = strtotime($time);
    $timedif = $qetime - $time;
    $timedif = abs($timedif);

    /*if($timedif>$uqdura)
    {
        $timedif = $uqdura;
        $_SESSION['totdura']=$timedif;
    }
    else
    {*/
        $_SESSION['totdura']=$timedif;
    /*}*/ 

    $nonull=0;
    $end=0;
    $count=0;
    $i=0;
    $c=1;
    $sql="SELECT TOTAL_QUESTIONS,QUIZ_NAME from quizmaster where QUIZ_ID='$qid'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
        foreach($results as $result)
        {
            $totqns=$result->TOTAL_QUESTIONS;
            $_SESSION['totalquestions']=$totqns;
            $qn=$result->QUIZ_NAME;
        }
    }
    $sql="SELECT * from quizmarks where QUIZ_ID='$qid' and U_ANSWER='NULL'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
        $_SESSION['nonull']=$nonull;

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
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

            <script type="text/javascript">
                window.onload = function () {
                document.onkeydown = function (e) {
                    return (e.which || e.keyCode) != 116;
                    };
                } 

                window.history.forward(1);
                document.attachEvent("onkeydown", my_onkeydown_handler);
                function my_onkeydown_handler() {
                    switch (event.keyCode) {
                        case 116 : // 'F5'
                            event.returnValue = false;
                            event.keyCode = 0;
                            window.status = "We have disabled F5";
                            break;
                    }
                }

                // Internet Explorer 6-11
                var isIE = false || !!document.documentMode;
                if(isIE)
                {
                    alert('This site does not support INTERNET EXPLORER! Open in another browser');
                    window.location.replace("ie-error.php");
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
                @media only screen and (max-width: 767px) 
                {
                    body 
                    {
                        /* The file size of this background image is 93% smaller
                        to improve page load speed on mobile internet connections */
                        background-image: url(images/quiz-bg-mobile.jpg);
                    }
                }

                .stroke
                {
                    -webkit-text-stroke: 1px black;
                }
            </style>
            <div class="col-lg-12 no-gutters" style="text-align: center;"><?php echo $u_name; ?></div>

            <div class="container">

                <p class="quiz-name">
                    <?php 
                        echo $qn;
                    ?>
                </p>

                <form name="myform" id="myform" action="QuizFinalAction.php" class="form-horizontal center" method="POST">

                    <div class="form-group">
                        <div class="row">
                            <input type="text" name="timer" id="timer" style="border: 3px solid blue;background-color: transparent; width: 50%; margin-left:90%; color: red;font-weight: bold;text-align: center;font-size: 30px;" readonly class="stroke">
                            <input type="text" name="end" id="end" hidden readonly>
                        </div>
                    </div>

<?php

                    while($c<=$totqns)
                    {
                        $sql="SELECT U_ANSWER from quizmarks where QUIZ_ID='$qid' and QUESTION_NUMBER='$c' and U_ANSWER='NULL'";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0)
                        {
                            $sql="SELECT qq.QUESTION_NUMBER as qno,qq.QUESTION as qs,qq.OPTION1 as o1,qq.OPTION2 as o2,qq.OPTION3 as o3,qq.OPTION4 as o4,qq.QUIZ_NAME as qn from quizquestion qq where qq.QUIZ_ID='$qid' and qq.QUESTION_NUMBER='$c'";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                            if($query->rowCount() > 0)
                            {
                                foreach($results as $result)
                                {
                                    $_SESSION['qns'][$i]=$result->qno;
                                    ++$count;
                                    $_SESSION['count']=$count;


?>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="qnsno">Question No:</label>
                                            <div class="col-sm-10">
                                                <h6 style="font-weight: 600;">
                                                    <?php 
                                                        echo $result->qno;  
                                                        $_SESSION['questionnumber']=$result->qno;
                                                    ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="qns">Question:</label>
                                            <div class="col-sm-10">
                                                <h6 style="font-weight: 600;">
                                                    <?php 
                                                        echo $result->qs; 
                                                        $_SESSION['question']=$result->qs;
                                                    ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div><br>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <input type="radio" class="form-control" id="op" name="op<?php echo $i;?>[]" value="OPTION1">
                                            </div>
                                            <label class="control-label col-sm-10" for="op"><?php echo ($result->o1); ?></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <input type="radio" class="form-control" id="op" name="op<?php echo $i;?>[]" value="OPTION2">
                                            </div>
                                            <label class="control-label col-sm-10" for="op"><?php echo ($result->o2); ?></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <input type="radio" class="form-control" id="op" name="op<?php echo $i;?>[]" value="OPTION3">
                                            </div>
                                            <label class="control-label col-sm-10" for="op"><?php echo ($result->o3); ?></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <input type="radio" class="form-control" id="op" name="op<?php echo $i;?>[]" value="OPTION4">
                                            </div>
                                            <label class="control-label col-sm-10" for="op"><?php echo ($result->o4); ?></label>
                                        </div>
                                    </div>

                                    
<?php
                                }
                            }

                            ++$i;
                        }

                        ++$c;
                    }

?>
                                    <div class="form-group">
                                        <div class="row"> 
                                            <div class="col-sm-offset-2 col-sm-11">
                                                <input type="submit" id="sub" value="Submit" name="submit">
                                            </div>
                                        </div>
                                    </div>
                </form>

            </div>

            <footer id="footer">

                <p>Designed By Kaushik Banerjee</p>

            </footer>

        </body>

        </html>

        <script type="text/javascript">
            var total_seconds = <?php echo intval($timedif); ?>;
            var timer;
            c_hours = Math.floor(total_seconds / 3600);
            c_minutes = Math.floor((total_seconds - (c_hours * 3600)) / 60);
            c_seconds = Math.floor(total_seconds - (c_hours * 3600) - (c_minutes * 60));

            function CheckTime() 
            {
                document.getElementById("timer").value = c_hours+':'+c_minutes+':'+
                    c_seconds;

                if (total_seconds == 0) 
                {
                    var end=1;
                    document.getElementById("end").value=end;
                    alert("Your time is up. So submiting answers now wont be considered!");
                    alert("Please press Submit button!");
                }
                else 
                {
                    total_seconds = total_seconds - 1;
                    c_hours = Math.floor(total_seconds / 3600);
                    c_minutes = Math.floor((total_seconds - (c_hours * 3600)) / 60);
                    c_seconds = Math.floor(total_seconds - (c_hours * 3600) - (c_minutes * 60));  
                    timer = setTimeout(CheckTime, 1000);
                }
            }
            timer = setTimeout(CheckTime, 1000);
        </script>

<?php 

    }
    else
    {
        $nonull=1;
        $_SESSION['nonull']=$nonull;
        echo "<script>window.location.replace('QuizResultsAction.php')</script>";
    }
}

else
{
    echo "<script>window.location.replace('QuizTimeEndResult.php')</script>";
}

?>
