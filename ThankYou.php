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

$sql="SELECT QUESTION_NUMBER,U_ANSWER from quizmarks where QUIZ_ID='$qid' and U_PNO='$u_pno' and U_NAME='$u_name'";
$query = $dbh->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
       /*foreach($results as $result)
       {*/
   $array = array();
   $array = $results;
         /*print_r($array);*/
   $exampleEncoded = json_encode($array);
         /*echo $exampleEncoded;*/
       /*}*/
}

$sql="UPDATE quizresult set U_ANSWER=:exampleEncoded where QUIZ_ID='$qid' and U_PNO='$u_pno' and U_NAME='$u_name'";
$query = $dbh->prepare($sql);
$query->bindParam(':exampleEncoded',$exampleEncoded,PDO::PARAM_STR);
if($query->execute())
{
   $sql="DELETE from quizmarks where QUIZ_ID='$qid' and U_PNO='$u_pno' and U_NAME='$u_name'";
   $query = $dbh->prepare($sql);
   $query->bindParam(':exampleEncoded',$exampleEncoded,PDO::PARAM_STR);
   if($query->execute())
   {
?>
      <!DOCTYPE html>
      <html>
         <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!--- Start of Bootstrap for logo & header title -->
            <link href="css/QuizBootstrap.min.css" rel="stylesheet" id="bootstrap-css">
            <script src="js/QuizBootstrap.min.js"></script>
            <script src="js/QuizJquery.min.js"></script>
            <link rel="stylesheet" type="text/css" href="css/Quiz.css">
            <!--- End of Bootstrap for logo & header title -->
            <!--- Start of Bootstrap for navbar -->
            <link rel="stylesheet" href="css/QuizBootstrap450.min.css">
            <script src="js/QuizJquery1.min.js"></script>
            <script src="js/QuizPopper.min.js"></script>
            <script src="js/QuizBootstrap450.min.js"></script>
            <!--- End of Bootstrap for navbar -->
            <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
            <title>QUIZ</title>

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

         </head>
         <body oncontextmenu="return false;">
            <style type="text/css">
               body
               {
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
               <p style="color: green; font-size: 40px; font-weight: bold; text-align: center;" class="stroke">Congratulations! You have successfully submitted your quiz.</p>
               <p style="color: red; font-size: 40px; font-weight: bold; text-align: center;" class="stroke">Now you can close the window.</p>
            </div>
            <footer id="footer">
               <p>Maintained by: ERP | SAILCMO</p>
            </footer>
         </body>
      </html>
<?php

   }
}

?>