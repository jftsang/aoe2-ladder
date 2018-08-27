<?php
$str = urldecode($_REQUEST["str"]);
$salthash = $_REQUEST["salthash"];
if (md5($str."manchuria") == $salthash)
{
    $successful = true;
    $f = fopen("ladder-history", 'a+');
    fputs($f, $str. "\n");
}
else
{
    $successful = false;
}
?>
<html>
<body>
<?php
if ($successful)
{
?>
<p>Added game<br/>
<pre>
<?=$str?>
</pre><br/>
to scoreboard.</p>
<?
}
else
{
?>
<p>Sorry, addition failed because hash doesn't match.</p>
<?
}
?>

<p><a href="index.php">Return to table</a></p>

</body>
</html>
