<?
$captcha = strtolower($_REQUEST["captcha"]);
if ($captcha == "michael" || $captcha == "michelle")
{
    $successful = true;    
    $str = sprintf("%s: %s ; %s ; %s", 
        $_REQUEST["date"], $_REQUEST["winners"], $_REQUEST["surrendered"], $_REQUEST["died"]);

    $msg = sprintf(<<<EOF
Hello,

Someone has submitted a game result to the AoE2 Ladder scoreboard. 
The result was 
%s

You can add this game to the scoreboard using the link
  http://www.jftsang.com/aoe2-ladder/addtolist.php?str=%s&salthash=%s

Best,
AoE2 Ladder.
EOF
, $str, urlencode($str), md5($str."manchuria"));

    mail("jftsang@jftsang.com", "Age of Empires II ladder submission", $msg, "From: aoe2-ladder@jftsang.com");
}
else
{
    $successful = false;
}
?>

<html>
<head><title>Submission</title></head>

<body>
<?
if ($successful)
{
?><p>Submission successful! JMFT will review your submission and add it to the list.</p>
<?
} 
else
{
?><p>Submission failed! Try the CAPTCHA again, please.</p>
<?
}
?>

<a id="submit"/>
<h2>Submit another</h2>
<?php require_once("submitform.php") ?>

<p><a href="index.php">Return to table</a></p>
</body>
</html>
