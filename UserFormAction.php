<?php 
// starting the session
session_start();
include('includes/dbcontroller.php'); 

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

if (isset($_POST['submit']))
{
	// insert form values in variables
	$qid=strtoupper($_SESSION['quizid']);
	$uname=strtoupper($_POST['uname']);
	$upno=strtoupper($_POST['upno']);
	$uloc=strtoupper($_POST['uloc']);
	$ugrp=strtoupper($_POST['ugrp']);

	$_SESSION['u_name']=$uname;
	$_SESSION['u_pno']=$upno;
	$_SESSION['u_loc']=$uloc;
	$_SESSION['u_grp']=$ugrp;
	$_SESSION['cqno']=1;
	$_SESSION['ctimer']=0;
	$_SESSION['check']=0;
 
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
		$sql="SELECT * from quizresult where ((U_PNO='$upno' and QUIZ_ID='$qid' and U_NAME='$uname') or (U_PNO='$upno' and QUIZ_ID='$qid'))";
		$query = $dbh->prepare($sql);
		$query->execute();
		$results=$query->fetchAll(PDO::FETCH_OBJ);
		if($query->rowCount() == 0)
		{
			$sql="SELECT IP_ADDRESS from quizmarks where U_PNO='$upno' and QUIZ_ID='$qid'";
			$query = $dbh->prepare($sql);
			$query->execute();
			$results=$query->fetchAll(PDO::FETCH_OBJ);
			if($query->rowCount() > 0)
			{
			    $sql="SELECT * from quizmarks where U_PNO='$upno' and QUIZ_ID='$qid' and QUESTION_NUMBER=0";
				$query = $dbh->prepare($sql);
				$query->execute();
				$results=$query->fetchAll(PDO::FETCH_OBJ);
				if($query->rowCount() > 0)
				{
					foreach($results as $result)
	        		{
						$_SESSION['u_name']=$result->U_NAME;
						$_SESSION['u_pno']=$result->U_PNO;
						$_SESSION['u_loc']=$result->U_DEPT;
						$_SESSION['u_grp']=$result->REGION;
						$_SESSION['page']=1;
					   	echo "<script>window.location.replace('BeginQuiz.php')</script>";
					}
			    }
			    else
			    {
			    	$sql="SELECT * from quizmarks where U_PNO='$upno' and QUIZ_ID='$qid'";
					$query = $dbh->prepare($sql);
					$query->execute();
					$results=$query->fetchAll(PDO::FETCH_OBJ);
					if($query->rowCount() > 0)
					{
				    	foreach($results as $result)
		        		{
					    	$nRows = $dbh->query("SELECT count(*) FROM quizmarks WHERE U_PNO='$upno' and QUIZ_ID='$qid'")->fetchColumn(); 
							$_SESSION['cqno']=$nRows+1;
							$_SESSION['u_name']=$result->U_NAME;
							$_SESSION['u_pno']=$result->U_PNO;
							$_SESSION['u_loc']=$result->U_DEPT;
							$_SESSION['u_grp']=$result->REGION;
							$_SESSION['check']=1;
							$_SESSION['page']=2;
							echo "<script>window.location.replace('BeginQuiz.php')</script>";
						}
					}
			    }
			}
		    else
		    {
		    	$sql="INSERT INTO quizmarks(U_NAME,U_PNO,QUIZ_ID,U_DEPT,IP_ADDRESS,REGION) VALUES(:uname,:upno,:qid,:uloc,:ip_address,:ugrp)";
		    	$query = $dbh->prepare($sql);
				$query->bindParam(':uname',$uname,PDO::PARAM_STR);
				$query->bindParam(':upno',$upno,PDO::PARAM_STR);		
				$query->bindParam(':qid',$qid,PDO::PARAM_STR);
				$query->bindParam(':uloc',$uloc,PDO::PARAM_STR);
				$query->bindParam(':ip_address',$ip_address,PDO::PARAM_STR);
				$query->bindParam(':ugrp',$ugrp,PDO::PARAM_STR);
				if($query->execute())
				{
					$_SESSION['page']=1;
			    	echo "<script>window.location.replace('BeginQuiz.php')</script>";
					/*echo '<script>window.open ("Quiz.php","mywindow","status=1,toolbar=0");</script>';*/
					
				}
			}
		}
		else
		{	
			echo "<script>alert('You have already successfully completed your quiz. Thank You!');</script>";
			echo '<script>window.location.replace("https://quizmodule.herokuapp.com/")</script>';
		}
	}
	else
	{
		echo "<script>alert('Either the Quiz has not started yet or it has ended. Please contact your Quiz Administrator.');</script>";
		echo '<script>window.location.replace("https://quizmodule.herokuapp.com/")</script>';
	}
}

?>
