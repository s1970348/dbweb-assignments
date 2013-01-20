<?php
ini_set('session.gc-maxlifetime', 60 * 60 * 24 * 7);
session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<title>MOST SUPERAWSUM QUIZ EVAH</title>
</head>
<body>
<?
require_once ('dbconnect.php');
//or $_SESSION['active']>60*60*24*7
if (isset($_REQUEST['username'])) {
    // check user input
    if (empty($_REQUEST['username'])) {
        echo ('please enter a username');
        
    } else {
        if (preg_match('/[^a-z]/i', $_REQUEST['username'])) {
            echo ('Please refrain from wrecking the database <br />
        only alphanumeric characters allowed!<br /><br />');
        } else {
            $query5 = mysql_query("SELECT * FROM `halloffame` WHERE username='".$_REQUEST['username']."'");
            $usernameresult = mysql_fetch_array($query5);
            
            if (!empty($usernameresult[0])) {
                echo ('username already taken');
            } else {
                $_SESSION['username'] = $_REQUEST['username'];
            }
        }
    }
}
if (!isset($_SESSION['active']) or !isset($_SESSION['username'])) { //1st time or expired or when reloading 1st question
    //Workaround for first iteration
    $_SESSION['prev_answer'] = '42';
    $_SESSION['qnum'] = 1;
    $_SESSION['ncorrect'] = 0;
    $_SESSION['active'] = $_SERVER["REQUEST_TIME"];
    $_SESSION['querynumber'] = 1;
    // query for username
    echo ("
<fieldset id='username'>
<legend> Welcome! Enter your username </legend>
<form method = 'post' action=" . $_SERVER['PHP_SELF'] . ">
Username: <input type='text' name='username'>
<br />
<input TYPE='submit'NAME='submitusername' VALUE='submit' />

</form>
</fieldset> ");

} else { //not 1st or expired or no username entered
    $_SESSION['active'] = $_SERVER["REQUEST_TIME"];
    // update session

    if (!empty($_POST['postedqnum'])) {
        $_SESSION['qnum'] = $_POST['postedqnum'];

        if ($_SESSION['qnum'] != 1) {
            $_SESSION['querynumber'] = $_SESSION['qnum'] - 1;
            $_SESSION['prev_answer'] = $_REQUEST['public'];

            // having trouble finding a way around the $_REQUEST[''] statements upon reloading' , used the undesirable @ now
        }

        $query1 = ("SELECT * FROM `choice` WHERE correct = 1 and question_number = " . $_SESSION['querynumber']);
        $answerresult = mysql_query($query1) or die('GOED ANTWOORD QUERY FAALT' .
            mysql_error());
        $answer_row = mysql_fetch_array($answerresult) or die('Goed antwoord vertalen faalt' .
            mysql_error());

        if ($answer_row['c_text'] == $_SESSION['prev_answer']) {
            //check if answer is correct
            ++$_SESSION['ncorrect'];
        }
    }


    // Is there a way to query for number of rows? of a query (that way the code wouldnt have to be updated each time questions are changed)

    if ($_SESSION['qnum'] == 4) {
        // if all questions are answered
        echo ('Hello, ' . $_SESSION['username'] .
            '<br /> You are done with the QUIZ <BR /> You answered : ' . $_SESSION['ncorrect'] .
            'answers out of ' . $_SESSION['querynumber'] . ' correctly!<br /><br />
Here are the current top 5 players:<br />');

        echo ('BUT FIRST 
last answer:' . $_SESSION['prev_answer'] . "<br /> and last correct answer:" . $answer_row['c_text'] .
            "and the qnum: " . $_SESSION['qnum']);
        $query3 = ("INSERT INTO  `s1970348`.`halloffame` (`username` ,`RightAnswers`) VALUES ('" .
            $_SESSION['username'] . "','" . $_SESSION['ncorrect'] . "');");
        mysql_query($query3) or die('Uploading of username to halloffame failed ' .
            mysql_error());

        $query4 = ('SELECT * FROM  `halloffame` WHERE 1 ORDER BY  `RightAnswers` DESC LIMIT 5');
        $halloffame = mysql_query($query4) or die('Requesting of hall of fame failed' .
            mysql_error());
        echo ('<table border="1"><tr><td>User</td><td>#correct</td></tr>');
        while ($row = mysql_fetch_array($halloffame)) {
            echo ('<tr>');
            echo ('<td>' . $row['username'] . '</td>');
            echo ('<td>' . $row['RightAnswers'] . '</td>');
            echo ('<tr>');
        }
        mysql_close($link);
        session_destroy();
    } else {

        $query = ('SELECT * FROM  `s1970348`.`question` where q_number =' . $_SESSION['qnum']);
        $result = mysql_query($query) or die('QUESTION OPVRAGEN FAALT' . mysql_error());
        $row = mysql_fetch_array($result) or die('ROW VERTALEN FAALT' . mysql_error());
        $question = $row['q_text'];

        $questionarray = array();
        $query2 = ("SELECT c_text FROM `choice` where `question_number` = " . $_SESSION['qnum']);
        $questionqueryresult = mysql_query($query2) or die('QUESTIONQUERY FAILT' .
            mysql_error());

        while ($question_row = mysql_fetch_array($questionqueryresult)) {
            array_push($questionarray, $question_row['c_text']);
        }

        echo ("
<fieldset id='question'>
<legend>Question number : " . $_SESSION['qnum'] . " </legend>
");

        $postedqnum = $_SESSION['qnum'] + 1;

        echo ("
'Question: " . $question . "
<form method = 'post' action=" . $_SERVER['PHP_SELF'] . "
<br />
<input type='radio' name='public' value='" . $questionarray[0] . "'> " . $questionarray[0] .
            "
<br />
<input type='radio' name='public' value='" . $questionarray[1] . "'> " . $questionarray[1] .
            "
<br />
<input type='radio' name='public' value='" . $questionarray[2] . "'> " . $questionarray[2] .
            "
<br />
<input TYPE='submit'NAME='answer' ID='answer' VALUE='Answer question' />
<input type='hidden' name='postedqnum' value='" . $postedqnum . "' />

</form>
</fieldset> ");
        mysql_close($link);
    }
}
?>

</body>
</html>