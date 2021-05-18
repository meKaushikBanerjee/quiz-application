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

include('includes/dbcontroller.php'); 
$c=0;
$msg=0;

if (isset($_GET['submit']))
{
    $qid=strtoupper($_GET['qid']);
    $upno=strtoupper($_GET['upno']);
    if($_GET['ugrp'])
    {
        $ugrp=strtoupper($_GET['ugrp']);
        $sql="SELECT * from quizresult where QUIZ_ID='$qid' and U_PNO='$upno' and REGION='$ugrp'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        if($query->rowCount() > 0)
        {
            foreach($results as $result)
            {
                $c=1;
            }
        }
    }
    else
    {
        $sql="SELECT * from quizresult where QUIZ_ID='$qid' and U_PNO='$upno'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        if($query->rowCount() > 0)
        {
            foreach($results as $result)
            {
                $c=1;
            }
        }
    }
}

elseif (isset($_GET['showall']))
{
    $qid=strtoupper($_GET['qid']);
    if($_GET['ugrp'])
    {
        $ugrp=strtoupper($_GET['ugrp']);
    }
    $c=2;
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/QuizBootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="js/QuizBootstrap.min.js"></script>
    <script src="js/QuizJquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/QuizReport.css">

    <link rel="stylesheet" href="css/QuizBootstrap450.min.css">
    <script src="js/QuizJquery1.min.js"></script>
    <script src="js/QuizPopper.min.js"></script>
    <script src="js/QuizBootstrap450.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
    <title>QUIZ REPORT</title>

    <script type="text/javascript">
        function EnableDisableTextBox() 
        {
            var chkYes = document.getElementById("chkYes");
            var upno = document.getElementById("upno");
            var submit = document.getElementById("submit");
            var showall = document.getElementById("showall");
            upno.disabled = chkNo.checked ? true : false;
            submit.disabled = chkNo.checked ? true : false;
            showall.disabled = chkYes.checked ? true : false;
            if (!upno.disabled) 
            {
                upno.focus();
            }
            if (!submit.disabled) 
            {
                submit.focus();
            }
            if (!showall.disabled) 
            {
                showall.focus();
            }
        }
    </script>

</head>

<body oncontextmenu="return false;" style="margin-top: 20px;">

    <!--<style type="text/css">
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
    </style>-->

    <div class="container">
        
        <form class="form-horizontal center" action="" method="GET">

                <!---START DETAILS PART -->

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-3" for="qno">View Single Record:</label>
                        <div class="col-sm-3">
                            <input type="radio" id="chkYes" name="chkPassPort" onclick="EnableDisableTextBox()"/ required>
                        </div>
                        <label class="control-label col-sm-3" for="qno">View All Records:</label>
                        <div class="col-sm-3">
                            <input type="radio" id="chkNo" name="chkPassPort" onclick="EnableDisableTextBox()"/ required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="qno">Enter Quiz Id:<span class="required">*</span></label>
                        <div class="col-sm-5">
                            <?php 
                                if(isset($_GET['qid'])) 
                                {
                                    $qid=$_GET['qid'];
                            ?>
                                    <input type="text" class="form-control" id="qid" placeholder="Enter Quiz Id" name="qid" required maxlength="7" value="<?php echo $qid; ?>">
                            <?php
                                }
                                else
                                {
                            ?>
                                    <input type="text" class="form-control" id="qid" placeholder="Enter Quiz Id" name="qid" required maxlength="7">
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="qno">Enter P No:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="upno" placeholder="Enter P No" name="upno" required maxlength="7">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="control-label col-sm-5" for="grp">You Belong to Group :</label>
                        <div class="col-sm-5">
                            <select name="ugrp" id="ugrp">
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
                        <div class="col-sm-offset-2 col-sm-7">
                            <input type="submit" value="Single Record" name="submit" id="submit">
                        </div>

                        <div class="col-sm-offset-2 col-sm-2">
                            <input type="submit" value="All Records" name="showall" id="showall">
                        </div>
                    </div>
                </div>

            </form>
    </div>

<?php

if($c==1)
{

?>

    <div class="container">
                <h2 style="text-align: center;">Quiz Report</h2>            
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Participant Name</th>
                            <th style="text-align: center;">Participant PNO</th>
                            <th style="text-align: center;">Region</th>
                            <th style="text-align: center;">Marks</th>
                            <th style="text-align: center;">Submit Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;"><?php echo ($result->U_NAME); ?></td>
                            <td style="text-align: center;"><?php echo ($result->U_PNO); ?></td>
                            <td style="text-align: center;"><?php echo ($result->REGION); ?></td>
                            <td style="text-align: center;"><?php echo ($result->U_MARKS); ?></td>
                            <td style="text-align: center;"><?php echo ($result->SUBMIT_TIMESTAMP); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

<?php
    
    $c=0;
}

elseif($c==2)
{

?>
            <div class="container">
                <h2 style="text-align: center;">Quiz Report</h2>            
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Participant Name</th>
                            <th style="text-align: center;">Participant PNO</th>
                            <th style="text-align: center;">Region</th>
                            <th style="text-align: center;">Marks</th>
                            <th style="text-align: center;">Submit Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    if($_GET['ugrp'])
                    {
                        $sql="SELECT * from quizresult where QUIZ_ID='$qid' and REGION='$ugrp' order by U_MARKS desc,SUBMIT_TIMESTAMP";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0)
                        {
                            foreach($results as $result)
                            {

?>
                                <tr>
                                    <td style="text-align: center;"><?php echo ($result->U_NAME); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->U_PNO); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->REGION); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->U_MARKS); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->SUBMIT_TIMESTAMP); ?></td>
                                </tr>
<?php

                            }
                        }
                    }
                    else
                    {                        
                        $sql="SELECT * from quizresult where QUIZ_ID='$qid' order by U_MARKS desc,SUBMIT_TIMESTAMP";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0)
                        {
                            foreach($results as $result)
                            {

?>
                                <tr>
                                    <td style="text-align: center;"><?php echo ($result->U_NAME); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->U_PNO); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->REGION); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->U_MARKS); ?></td>
                                    <td style="text-align: center;"><?php echo ($result->SUBMIT_TIMESTAMP); ?></td>
                                </tr>
<?php

                            }
                        }
                    }
                    
?>        
                    </tbody>
                </table>
            </div>

<?php
    
    $c=0;
}

?>

</body>

</html>