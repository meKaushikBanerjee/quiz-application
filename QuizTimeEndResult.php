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

$qid=$_SESSION['quizid'];
$u_name=$_SESSION['u_name'];
$u_pno=$_SESSION['u_pno'];
$u_grp=$_SESSION['u_grp'];

$sql="SELECT QUIZ_NAME as qname from quizmaster where QUIZ_ID='$qid'";
$query = $dbh->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
	foreach($results as $result)
	{
		$quizname=$result->qname;
	}
}

	$time=$_SESSION['stime'];
	$sql="SELECT SUM(U_MARKS) as sm,SUM(SUBMIT_TIME) as st from quizmarks where QUIZ_ID='$qid' and U_PNO='$u_pno'";
	$query = $dbh->prepare($sql);
	$query->execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	if($query->rowCount() > 0)
	{
		foreach($results as $result)
		{
			$marks=$result->sm;
			$stime=$result->st;
			$stime=$stime+$time;
			$minutes = floor(($stime / 60) % 60);
			$seconds = $stime % 60;
			$timetaken= $minutes.":".$seconds;
			date_default_timezone_set("Asia/Calcutta");
			$timestmp = strtoupper(date('d-m-Y H:i:sa'));

			$sql="INSERT INTO quizresult(U_NAME,U_PNO,QUIZ_ID,QUIZ_NAME,SUBMIT_TIME,U_MARKS,SUBMIT_TIMESTAMP,REGION) VALUES(:u_name,:u_pno,:qid,:quizname,:timetaken,:marks,:timestmp,:u_grp)";
			$query = $dbh->prepare($sql);
			$query->bindParam(':u_name',$u_name,PDO::PARAM_STR);
			$query->bindParam(':u_pno',$u_pno,PDO::PARAM_STR);
			$query->bindParam(':quizname',$quizname,PDO::PARAM_STR);
			$query->bindParam(':timetaken',$timetaken,PDO::PARAM_STR);
			$query->bindParam(':qid',$qid,PDO::PARAM_STR);
			$query->bindParam(':marks',$marks,PDO::PARAM_STR);
			$query->bindParam(':timestmp',$timestmp,PDO::PARAM_STR);
			$query->bindParam(':u_grp',$u_grp,PDO::PARAM_STR);
			if($query->execute())
			{
				echo "<script>window.location.replace('ThankYou.php')</script>";
			}
		}
	}

?>