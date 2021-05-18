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
$cqno=$_SESSION['cqno'];
$u_pno=$_SESSION['u_pno'];
$u_name=$_SESSION['u_name'];

$sql="SELECT QUIZ_DATE,QUIZ_S_TIME,QUIZ_E_TIME,TOTAL_QUESTIONS from quizmaster where QUIZ_ID='$qid'";
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
		$totalqns = $result->TOTAL_QUESTIONS;
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
$hours = sprintf('%02d',(int) $qstime[0]);
$minutes = sprintf('%02d',(int) $qstime[1]);
$qstime = $hours.$minutes."00";

$qetime = date('H:i',strtotime($qetime));
$qetime = array_map('intval',explode(':',$qetime,2));
$hours = sprintf('%02d',(int) $qetime[0]);
$minutes = sprintf('%02d',(int) $qetime[1]);
$qetime = $hours.$minutes."00";

$udate = array_map('intval',explode('-',$udate,3));
$udate = $udate[0].$udate[1].$udate[2];

if(($time>=$qstime)&&($udate==$date)&&($time<=$qetime))
{
    //whether ip is from share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   
    {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
    {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from remote address
    else
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
        	
	$sql="SELECT * from quizresult WHERE QUIZ_ID='$qid' and U_PNO='$u_pno' and U_NAME='$u_name'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
        echo "<script>alert('You have already successfully submitted your Quiz.');</script>";
		echo '<script>window.location.replace("//10.129.20.223")</script>';
    }
    else
   	{
   		$sql="SELECT * from quizmarks WHERE QUIZ_ID='$qid' and U_PNO='$u_pno' and U_NAME='$u_name'";
		$query = $dbh->prepare($sql);
		$query->execute();
		$results=$query->fetchAll(PDO::FETCH_OBJ);
		if($query->rowCount() == $totalqns)
		{
			echo '<script>window.location.replace("QuizFinal.php")</script>';
		}

	    $sql="SELECT qq.QUESTION_NUMBER as qno,qq.QUESTION as qs,qq.OPTION1 as o1,qq.OPTION2 as o2,qq.OPTION3 as o3,qq.OPTION4 as o4,qq.QUIZ_NAME as qn,qm.TOTAL_QUESTIONS as totqn,qm.QUIZ_DURATION as qd from quizquestion qq join quizmaster qm on qq.QUIZ_ID=qm.QUIZ_ID where qm.QUIZ_ID='$qid' and qq.QUIZ_ID='$qid' and qq.QUESTION_NUMBER='$cqno'";
	    $query = $dbh->prepare($sql);
	    $query->execute();
	    $results=$query->fetchAll(PDO::FETCH_OBJ);
	    if($query->rowCount() > 0)
	    {
	        foreach($results as $result)
	        {
	        	$_SESSION['totalduration']=$result->qd;
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

				    <div class="col-lg-12 no-gutters"><img src="Images/Steel_Authority_of_India_logo.svg.png" class="mx-auto d-block img-fluid logo" alt="sail-logo"></div>
				    <div class="col-lg-12 no-gutters" style="text-align: center;"><?php echo $u_name; ?></div>

					<div class="container">

						<p class="quiz-name">
						   	<?php 
						   		echo ($result->qn);
						   		$_SESSION['quizname']=$result->qn;
						   	?>
						</p>
<?php


				if((($result->qno) == $cqno)&&($_SESSION['check'] !=0))
				{
					$_SESSION['totalquestions']=$result->totqn;
					$quesno=$result->qno;
					$t=$_SESSION['ctimer'];
					$end=0;
					if($_SESSION['ctimer']==0)
			        {
			        	$t=$result->qd;
			        	/*$t=$t*60;*/
			        	$_SESSION['ctimer']=$t;
			        }

?>						   	
						<form id="q" action="QuizAction.php" class="form-horizontal center" method="POST">

							<!---START DETAILS PART -->
							<div class="form-group">
								<div class="row">
									<input type="text" name="timer" id="timer" style="border: 5px solid blue;background-color: transparent; width: 50%; margin-left:90%; color: red;font-weight: bold;text-align: center;font-size: 30px;" readonly class="stroke">
									<input type="text" name="end" id="end" hidden readonly>
								</div>
							</div>

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

							<?php 
                                $sql="SELECT U_ANSWER from quizmarks where QUIZ_ID='$qid' and U_PNO='$u_pno' and QUESTION_NUMBER='$quesno'";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $outputs=$query->fetchAll(PDO::FETCH_OBJ);
                                if($query->rowCount() > 0)
                                {
                                    foreach($outputs as $output)
                                    {
                            ?>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <input type="radio" class="form-control" id="op" name="op" value="OPTION1" 
                                                    <?php 
                                                        if(($output->U_ANSWER)=="OPTION1")
                                                        { 
                                                             echo "checked";
                                                        }
                                                    ?>>
                                                </div>
                                                <label class="control-label col-sm-10" for="op"><?php echo ($result->o1); ?></label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <input type="radio" class="form-control" id="op" name="op" value="OPTION2" 
                                                    <?php 
                                                        if(($output->U_ANSWER)=="OPTION2")
                                                        { 
                                                            echo "checked";
                                                        }
                                                    ?>>
                                                </div>
                                                <label class="control-label col-sm-10" for="op"><?php echo ($result->o2); ?></label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <input type="radio" class="form-control" id="op" name="op" value="OPTION3" 
                                                    <?php 
                                                        if(($output->U_ANSWER)=="OPTION3")
                                                        { 
                                                            echo "checked";
                                                        }
                                                    ?>>
                                                </div>
                                                <label class="control-label col-sm-10" for="op"><?php echo ($result->o3); ?></label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <input type="radio" class="form-control" id="op" name="op" value="OPTION4" 
                                                    <?php 
                                                        if(($output->U_ANSWER)=="OPTION4")
                                                        { 
                                                            echo "checked";
                                                        }
                                                    ?>>
                                                </div>
                                                <label class="control-label col-sm-10" for="op"><?php echo ($result->o4); ?></label>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }

                                else
                                {
                            ?>
									<div class="form-group">
										<div class="row">
										    <div class="col-sm-2">
										        <input type="radio" class="form-control" id="op" name="op" value="OPTION1">
										    </div>
										    <label class="control-label col-sm-10" for="op"><?php echo ($result->o1); ?></label>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
										    <div class="col-sm-2">
										        <input type="radio" class="form-control" id="op" name="op" value="OPTION2">
										    </div>
										    <label class="control-label col-sm-10" for="op"><?php echo ($result->o2); ?></label>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
										    <div class="col-sm-2">
										        <input type="radio" class="form-control" id="op" name="op" value="OPTION3">
										    </div>
										    <label class="control-label col-sm-10" for="op"><?php echo ($result->o3); ?></label>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
										    <div class="col-sm-2">
										        <input type="radio" class="form-control" id="op" name="op" value="OPTION4">
										    </div>
										    <label class="control-label col-sm-10" for="op"><?php echo ($result->o4); ?></label>
										</div>
									</div>
							<?php

								}

								if(($_SESSION['cqno']) < ($result->totqn))
								{

							?>
									<div class="form-group">
										<div class="row">
										    <div class="col-sm-offset-2 col-sm-11">
										        <input type="submit" id="next" value="Next" name="submit">
										    </div>
										</div>
									</div>

							<?php

								}

								elseif(($_SESSION['cqno']) == ($result->totqn))
								{

							?>
									<div class="form-group">
										<div class="row"> 
										    <div class="col-sm-offset-2 col-sm-11">
										        <input type="submit" id="sub" value="Submit" name="submit">
										    </div>
										</div>
									</div>

							<?php

								}

							?>

						</form>
<?php
				}

?>

					</div>

					<footer id="footer">

						<p>Maintained by: ERP | SAILCMO</p>

					</footer>

				</body>

				</html>

				<script type="text/javascript">
					var total_seconds = <?php echo $t; ?>;
					var c_minutes = parseInt(total_seconds / 60);
					var c_seconds = parseInt(total_seconds % 60);
					var timer;

					function CheckTime() 
					{
						document.getElementById("timer").value = c_minutes+':'+c_seconds;

						if (total_seconds == 0) 
						{
							var end=1;
							document.getElementById('end').value = end;
							alert("Time is up for this question. So submiting answer for this question wont be considered now. You will get another chance!");
							alert("Please press Submit or Next button!");
						}
						else 
						{
							total_seconds = total_seconds - 1;
							c_minutes = parseInt(total_seconds / 60);
							c_seconds = parseInt(total_seconds % 60);
							timer = setTimeout(CheckTime, 1000);
						}
					}
					timer = setTimeout(CheckTime, 1000);
				</script>

<?php
			}
		}
	}
}

else
{
	echo "<script>alert('Either the Quiz has not started yet or it has ended. Please contact your Quiz Administrator.');</script>";
	echo '<script>window.location.replace("//10.129.20.223")</script>';
}

?>