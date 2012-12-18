<!DOCTYPE HTML>
<html>
<head>
<title>MOST SUPERAWSUM QUIZ EVAH</title>
</head>
<body>
<?
require_once('dbconnect.php');

if (!isset($_REQUEST['answer'])) { //1st time
$qnum = 1;
$ncorrect = 0;
$querynumber = 1;
//Workaround for first iteration
$_REQUEST['public'] = 'pie';
$logintime=$_SERVER["REQUEST_TIME"]+(60*60);
setcookie('qnum', $qnum);
setcookie('ncorrect', $ncorrect);
setcookie('logintime',$logintime);
} else {
//subsequent times
$qnum = (int)$_COOKIE['qnum'];
$ncorrect = (int)$_COOKIE['ncorrect'];
$logintime = $_COOKIE['logintime'];
}
if ($qnum != 1){
$querynumber = $qnum -1;
}
$query1=("SELECT * FROM `choice` WHERE correct = 1 and question_number = ".$querynumber);
$answerresult = mysql_query($query1) or die ('GOED ANTWOORD QUERY FAALT'. mysql_error());
$answer_row= mysql_fetch_array($answerresult) or die ('Goed antwoord vertalen faalt'. mysql_error());
$correct_answer= $answer_row['c_text'];


if ($correct_answer == $_REQUEST['public']){
    ++$ncorrect;
    setcookie('ncorrect',$ncorrect);
    }
// Is there a way to query for number of rows?
if ($qnum == 4){
echo('klaar met de QUIZ <BR /> You answered : '.$ncorrect.'answers out of '.$querynumber.' correctly!');
}else {
    
$query=('SELECT * FROM  `question` where q_number ='.$qnum);
$result= mysql_query($query) or die('QUESTION OPVRAGEN FAALT' . mysql_error());
$row = mysql_fetch_array($result) or die ('ROW VERTALEN FAALT' . mysql_error());
$question = $row['q_text'];
$question_number = $row['q_number'];

$questionarray= array() ;
$query2=("SELECT c_text FROM `choice` where `question_number` = ".$qnum );
$questionqueryresult= mysql_query($query2) or die ('QUESTIONQUERY FAILT' . mysql_error());

while($question_row = mysql_fetch_array($questionqueryresult)){
array_push($questionarray , $question_row['c_text']);
}

echo("
<fieldset id='question'>
<legend>Question number : ". $qnum ." </legend>

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

++$qnum;
setcookie('qnum', $qnum);
}
?>

</body>
</html>