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
// starting the session
session_start();

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

if (isset($_POST['gotoquiz']))
{
	echo "<script>window.location.replace('Quiz.php')</script>";
}

if (isset($_POST['submit']))
{
	$qid=$_SESSION['quizid'];
	$u_name=$_SESSION['u_name'];
	$u_pno=$_SESSION['u_pno'];
	$u_loc=$_SESSION['u_loc'];
	$u_grp=$_SESSION['u_grp'];
	$totqns=$_SESSION['totalquestions'];
	$_SESSION['totalquestions']=$totqns;

	$sql="SELECT QUIZ_DATE,QUIZ_S_TIME,QUIZ_E_TIME from quizmaster where QUIZ_ID='$qid'";
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

		// insert form values in variables
		$qnsno=$_SESSION['questionnumber'];
		$qns=strtoupper($_SESSION['question']);
		$end=$_POST['end'];
		$_SESSION['end']=$end;
		$dura=$_SESSION['totalduration'];
		$stime=$_POST['timer'];
		$stime = array_map('intval',explode(':',$stime,2));
		$stime = $stime[0]*60+$stime[1];
		$stime = $dura-$stime;

		if((($_SESSION['cqno']) == 1)&&($end == 0))
		{
			if(!isset($_POST['op']))
			{
				$op="NULL";
			}
			else
			{
				$op=$_POST['op'];
			}
			$cqno=$_SESSION['cqno'];
				$sql="UPDATE quizmarks set QUESTION_NUMBER=:qnsno,QUESTION=:qns,U_ANSWER=:op,SUBMIT_TIME=:stime where QUIZ_ID=:qid and U_PNO=:u_pno";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
					if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
						    		$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line26")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
						    		$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line47")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("You have already submitted your Quiz. Sorry!")</script>';
					echo '<script>window.location.replace("/")</script>';
				}
		}

		if((($_SESSION['cqno']) == 1)&&($end == 1))
		{
			$op="NULL";
			$cqno=$_SESSION['cqno'];
				$sql="UPDATE quizmarks set QUESTION_NUMBER=:qnsno,QUESTION=:qns,U_ANSWER=:op,SUBMIT_TIME=:stime where QUIZ_ID=:qid and U_PNO=:u_pno";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
					if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
						    		$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line26")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
						    		$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line47")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("Question, question number user answer could not be set! Kindly check line 22")</script>';
					echo '<script>window.location.replace("Quiz.php")</script>';
				}
		}

		elseif((($_SESSION['cqno']) != 1)&&(($_SESSION['cqno']) < ($_SESSION['totalquestions']))&&($end == 0))
		{
			if(!isset($_POST['op']))
			{
				$op="NULL";
			}
			else
			{
				$op=$_POST['op'];
			}
			$cqno=$_SESSION['cqno'];
				$sql="INSERT INTO quizmarks(U_NAME,U_PNO,U_DEPT,QUIZ_ID,QUESTION_NUMBER,QUESTION,U_ANSWER,IP_ADDRESS,REGION,SUBMIT_TIME) VALUES(:u_name,:u_pno,:u_loc,:qid,:qnsno,:qns,:op,:ip_address,:u_grp,:stime)";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_name',$u_name,PDO::PARAM_STR);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':u_loc',$u_loc,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':ip_address',$ip_address,PDO::PARAM_STR);
				$query->bindParam(':u_grp',$u_grp,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
				    if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line111")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line127")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("Question, question number user answer could not be set! Kindly check line 89")</script>';
					echo '<script>window.location.replace("Quiz.php")</script>';
				}
		}

		elseif((($_SESSION['cqno']) != 1)&&(($_SESSION['cqno']) < ($_SESSION['totalquestions']))&&($end == 1))
		{
			$op="NULL";
			$cqno=$_SESSION['cqno'];
				$sql="INSERT INTO quizmarks(U_NAME,U_PNO,U_DEPT,QUIZ_ID,QUESTION_NUMBER,QUESTION,U_ANSWER,IP_ADDRESS,REGION,SUBMIT_TIME) VALUES(:u_name,:u_pno,:u_loc,:qid,:qnsno,:qns,:op,:ip_address,:u_grp,:stime)";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_name',$u_name,PDO::PARAM_STR);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':u_loc',$u_loc,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':ip_address',$ip_address,PDO::PARAM_STR);
				$query->bindParam(':u_grp',$u_grp,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
				    if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line111")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno+=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('Quiz.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line127")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("Question, question number user answer could not be set! Kindly check line 89")</script>';
					echo '<script>window.location.replace("Quiz.php")</script>';
				}
		}

		elseif(($_SESSION['cqno']) == ($_SESSION['totalquestions'])&&($end == 0))
		{
			if(!isset($_POST['op']))
			{
				$op="NULL";
			}
			else
			{
				$op=$_POST['op'];
			}
				$cqno=$_SESSION['cqno'];
			
				$sql="INSERT INTO quizmarks(U_NAME,U_PNO,U_DEPT,QUIZ_ID,IP_ADDRESS,REGION,QUESTION_NUMBER,QUESTION,U_ANSWER,SUBMIT_TIME)
						        VALUES(:u_name,:u_pno,:u_loc,:qid,:ip_address,:u_grp,:qnsno,:qns,:op,:stime)";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_name',$u_name,PDO::PARAM_STR);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':u_loc',$u_loc,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':ip_address',$ip_address,PDO::PARAM_STR);
				$query->bindParam(':u_grp',$u_grp,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
				    if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('QuizFinal.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line111")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('QuizFinal.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line127")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("Question, question number user answer could not be set! Kindly check line 89")</script>';
					echo '<script>window.location.replace("Quiz.php")</script>';
				}
		}

		elseif(($_SESSION['cqno']) == ($_SESSION['totalquestions'])&&($end == 1))
		{
			$op="NULL";
				$cqno=$_SESSION['cqno'];
			
				$sql="INSERT INTO quizmarks(U_NAME,U_PNO,U_DEPT,QUIZ_ID,IP_ADDRESS,REGION,QUESTION_NUMBER,QUESTION,U_ANSWER,SUBMIT_TIME)
						        VALUES(:u_name,:u_pno,:u_loc,:qid,:ip_address,:u_grp,:qnsno,:qns,:op,:stime)";
				$query = $dbh->prepare($sql);
				$query->bindParam(':u_name',$u_name,PDO::PARAM_STR);
				$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
				$query->bindParam(':u_loc',$u_loc,PDO::PARAM_STR);
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':ip_address',$ip_address,PDO::PARAM_STR);
				$query->bindParam(':u_grp',$u_grp,PDO::PARAM_STR);
				$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
				$query->bindParam(':qns',$qns,PDO::PARAM_STR);
				$query->bindParam(':op',$op,PDO::PARAM_STR);
				$query->bindParam(':stime',$stime,PDO::PARAM_STR);
			    if($query->execute())
			    {
			    	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qnsno'";
			    	$query = $dbh->prepare($sql);
			    	$query->execute();
				    $results=$query->fetchAll(PDO::FETCH_OBJ);
				    if($query->rowCount() > 0)
		            {
		                foreach($results as $result)
		                {
						    if(($result->CORRECT_OPTION) == $op)
						    {
						    	$marks=1;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('QuizFinal.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line111")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }
						    }
						    else
						    {
						    	$marks=0;
						    	$sql="UPDATE quizmarks set U_MARKS=:marks where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qnsno";
							    $query = $dbh->prepare($sql);
							    $query->bindParam(':marks',$marks,PDO::PARAM_STR);				
								$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
								$query->bindParam(':qid',$qid,PDO::PARAM_STR);
								$query->bindParam(':qnsno',$qnsno,PDO::PARAM_STR);
							    if($query->execute())
							    {
							    	$cqno=1;
							    	$_SESSION['cqno']=$cqno;
							    	$_SESSION['ctimer']=0;
							    	echo "<script>window.location.replace('QuizFinal.php')</script>";
							    }
							    else
							    {
							    	echo '<script>alert("Marks could not be set! Kindly check line127")</script>';
									echo '<script>window.location.replace("Quiz.php")</script>';
							    }

						    }
						}
					}
				}
				else
				{
					echo '<script>alert("Question, question number user answer could not be set! Kindly check line 89")</script>';
					echo '<script>window.location.replace("Quiz.php")</script>';
				}
		}
	}
	else
	{
		echo "<script>window.location.replace('QuizTimeEndResult.php')</script>";
	}
}

?>