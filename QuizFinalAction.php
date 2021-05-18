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
$totqns=$_SESSION['totalquestions'];
$totdura=$_SESSION['totdura'];

if(isset($_POST['submit']))
{
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

	if(($time>=$qstime)&&($time<=$qetime))
	{
		$dura=$totdura;
		$stime=$_POST['timer'];
		$stime = array_map('intval',explode(':',$stime,2));
		$stime = $stime[0]*60+$stime[1];
		$stime = $dura-$stime;
		$_SESSION['stime'] = $stime;
		$end=$_POST['end'];
		$count=$_SESSION['count'];
		$c=0;

		if($end==0)
		{
		//initialize
			for($i=0;$i<=$count-1;$i++)
			{	
				$qns=$_SESSION['qns'][$c];
				if(!isset($_POST['op'.$i]))
				{
					$op="NULL";
				}
				else
				{
					$op = count($_POST['op'.$i]) ? $_POST['op'.$i] : array();
					//echo out their choices separated by a comma
					$op = count($op) ? implode(', ',$op) : "NULL";
				}		
				$sql="SELECT U_ANSWER as uans from quizmarks as qm where qm.QUIZ_ID='$qid' and qm.U_PNO='$u_pno' and qm.QUESTION_NUMBER='$qns' and qm.U_ANSWER='NULL'";
		        $query = $dbh->prepare($sql);
		        $query->execute();
		        $results=$query->fetchAll(PDO::FETCH_OBJ);
		        if($query->rowCount() > 0)
		        {
		            	$sql="SELECT CORRECT_OPTION from quizquestion where QUIZ_ID='$qid' and QUESTION_NUMBER='$qns'";
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
							    	$sql="UPDATE quizmarks set U_MARKS=:marks,U_ANSWER=:op where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qns";
								    $query = $dbh->prepare($sql);
								    $query->bindParam(':marks',$marks,PDO::PARAM_STR);
									$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
									$query->bindParam(':qid',$qid,PDO::PARAM_STR);
									$query->bindParam(':qns',$qns,PDO::PARAM_STR);
									$query->bindParam(':op',$op,PDO::PARAM_STR);
								    if($query->execute())
								    {
								    	
								    }
								    else
								    {
								    	echo '<script>alert("Marks could not be set! Kindly check line26")</script>';
								    }
							    }
							    else
							    {
							    	$marks=0;
							    	$sql="UPDATE quizmarks set U_MARKS=:marks,U_ANSWER=:op where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qns";
								    $query = $dbh->prepare($sql);
								    $query->bindParam(':marks',$marks,PDO::PARAM_STR);	
									$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
									$query->bindParam(':qid',$qid,PDO::PARAM_STR);
									$query->bindParam(':qns',$qns,PDO::PARAM_STR);
									$query->bindParam(':op',$op,PDO::PARAM_STR);
								    if($query->execute())
								    {
								    
								    }
								    else
								    {
								    	echo '<script>alert("Marks could not be set! Kindly check line47")</script>';
								    }

							    }
							}
						}
				}
				++$c;
			}
			echo "<script>window.location.replace('QuizResultsAction.php')</script>";
		}
		elseif($end==1)
		{
			for($i=0;$i<=$count-1;$i++)
			{
				$op="NULL";
				$qns=$_SESSION['qns'][$c];
				$sql="SELECT U_ANSWER as uans from quizmarks as qm where qm.QUIZ_ID='$qid' and qm.U_PNO='$u_pno' and qm.QUESTION_NUMBER='$qns' and qm.U_ANSWER='NULL'";
		        $query = $dbh->prepare($sql);
		        $query->execute();
		        $results=$query->fetchAll(PDO::FETCH_OBJ);
		        if($query->rowCount() > 0)
		        {
		            $marks=0;
					$sql="UPDATE quizmarks set U_MARKS=:marks,U_ANSWER=:op where U_PNO=:u_pno and QUIZ_ID=:qid and QUESTION_NUMBER=:qns";
					$query = $dbh->prepare($sql);
					$query->bindParam(':marks',$marks,PDO::PARAM_STR);	
					$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
					$query->bindParam(':qid',$qid,PDO::PARAM_STR);
					$query->bindParam(':qns',$qns,PDO::PARAM_STR);
					$query->bindParam(':op',$op,PDO::PARAM_STR);
					if($query->execute())
					{
								    
					}
					else
					{
						echo '<script>alert("Marks could not be set! Kindly check line47")</script>';
					}
				}
			}
			echo "<script>window.location.replace('QuizResultsAction.php')</script>";
		}
	}
	else
	{
		echo "<script>window.location.replace('QuizTimeEndResult.php')</script>";
	}
}

?>
