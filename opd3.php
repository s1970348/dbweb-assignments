
<?php 
ini_set('session.gc-maxlifetime', 60*60*24*7);
session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<title>MOST SUPERAWSUM QUIZ EVAH</title>
</head>
<body>
<?
require_once('dbconnect.php');

if (!isset($_SESSION['active'])) { //1st time or expired
//Workaround for first iteration
$_REQUEST['public'] = 'pie';
$_SESSION['qnum']=1;
$_SESSION['ncorrect']=0;
$_SESSION['active']=$_SERVER["REQUEST_TIME"];
$querynumber=1;
} else {
$_SESSION['active']=$_SERVER["REQUEST_TIME"];
}
if ($_SESSION['qnum'] != 1){
$querynumber = $_SESSION['qnum'] -1;
}
$query1=("SELECT * FROM `choice` WHERE correct = 1 and question_number = ".$querynumber);
$answerresult = mysql_query($query1) or die ('GOED ANTWOORD QUERY FAALT'. mysql_error());
$answer_row= mysql_fetch_array($answerresult) or die ('Goed antwoord vertalen faalt'. mysql_error());
$correct_answer= $answer_row['c_text'];


if ($correct_answer == $_REQUEST['public']){
    ++$_SESSION['ncorrect'];
    }
// Is there a way to query for number of rows?
if ($_SESSION['qnum'] == 4){
echo('klaar met de QUIZ <BR /> You answered : '.$_SESSION['ncorrect'].'answers out of '.$querynumber.' correctly!');
session_destroy();
}else {
    
$query=('SELECT * FROM  `question` where q_number ='.$_SESSION['qnum']);
$result= mysql_query($query) or die('QUESTION OPVRAGEN FAALT' . mysql_error());
$row = mysql_fetch_array($result) or die ('ROW VERTALEN FAALT' . mysql_error());
$question = $row['q_text'];
$question_number = $row['q_number'];

$questionarray= array() ;
$query2=("SELECT c_text FROM `choice` where `question_number` = ".$_SESSION['qnum'] );
$questionqueryresult= mysql_query($query2) or die ('QUESTIONQUERY FAILT' . mysql_error());

while($question_row = mysql_fetch_array($questionqueryresult)){
array_push($questionarray , $question_row['c_text']);
}
//echo questionform
echo("
<fieldset id='question'>
<legend>Question number : ". $_SESSION['qnum'] ." </legend>

'Question: ".$question ."
<form method = 'post' action=". $_SERVER['PHP_SELF'] ."
<br />
<input type='radio' name='public' value='".$questionarray[0]."'> ". $questionarray[0] ."
<br />
<input type='radio' name='public' value='".$questionarray[1]."'> ". $questionarray[1] ."
<br />
<input type='radio' name='public' value='".$questionarray[2]."'> ". $questionarray[2] ."

<br />
<input TYPE='submit'NAME='answer' ID='answer' VALUE='Answer question' />
</form>
</fieldset> ");

++$_SESSION['qnum'];
}
?>

</body>
</html>