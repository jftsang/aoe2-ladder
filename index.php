<?php require_once("loadgames.php"); 

error_reporting(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <title>AoE2:HD scoreboard and ladder history</title>
</head>

<body>

<h1>AoE2:HD scoreboard and ladder history</h1>

<div style="width: 60%; float: left;">
<?
unset($players[""]);
?>
<h2>Scoreboard</h2>
<!-- <table style="width: 100%; padding: 5px"> -->
<table>
<thead> <tr>
  <th><a href="<?=$PHP_SELF?>?sortby=name">player</a></th> 
  <th><a href="<?=$PHP_SELF?>?sortby=played">played</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=won">won</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=surrendered">surr</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=died">died</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=ladder">ladder</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=peak">peak</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=running">running</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=average">average</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=inactivity&order=-1">inactivity</a></th>
</tr> </thead>
<tbody>
<?php
unset($player);
foreach ($players as $player) {
    //echo "<pre>"; var_dump($player); echo "</pre>";
    printf('<tr><td>%s</td>', $player->name);
    printf('<td>%d</td><td>%d</td><td>%d</td><td>%d</td>', 
        $player->played, 
        $player->won, 
        $player->surrendered, 
        $player->died
    );
    /*
    printf('<td>%d</td><td>%d (%.1f%%)</td><td>%d (%.1f%%)</td><td>%d (%.1f%%)</td>', 
        $player->played, 
        $player->won, $player->won / $player->played * 100,
        $player->surrendered, $player->surrendered / $player->played * 100,
        $player->died, $player->died / $player->played * 100
    );
     */
    printf('<td>%.2f</td><td>%.2f</td><td>%.2f</td><td>%.2f</td>', 
        $player->ladder, 
        $player->peak,
        $player->running, 
        $player->average
    );
    printf('<td>%d d</td>', $player->inactivity);

    printf("</tr>\n");
}
?>
</tbody>
</table>

</div>


<div style="text-align: justify; width: 40%; float: right;">

<h2>Ladder history</h2>
<?php
$lmh = isset($_REQUEST['lasthowmany']) ? $_REQUEST['lasthowmany'] : 5;
?>
    <p>Showing last <?=$lmh?> games. <a href="ladder-history">Click here for full history</a></p>

<table>
<thead>
<tr>
    <th>date</th>
    <th>value</th>
    <th>winners</th>
    <th>surrendered</th>
    <th>dead</th>
</tr>
</thead>

<tbody>
<?php
foreach (array_reverse(array_slice($games, -$lmh)) as $game) {
    printf('<tr>');
    printf('<td><i>%s</i></td>', $game->game_date);
    printf('<td>%.2f</td>', $game->value);
    printf('<td>');
    foreach ($game->winners as $player) {
        printf('%s<br/>', $player);
    }
    printf('</td>');
    printf('<td>');
    foreach ($game->surrendered as $player) {
        printf('%s<br/>', $player);
    }
    printf('</td>');
    printf('<td>');
    foreach ($game->dead as $player) {
        printf('%s<br/>', $player);
    }
    printf('</td>');
    printf('</tr>');
}
?>
</tbody>
</table>
</div>

<div style="clear: both; width: 50%; margin-left: auto; margin-right: auto; margin-top: 1cm;">
<h2>Submit a game</h2>
<?php require_once("submitform.php") ?>

<a id="explanation"/>
<h2>Scoring rules</h2>

<p>In a given game, a player may either win, surrender or die. The value of each
game is equal to the number of surrendered or dead players not counting those on
the winning team, divided by the number of winners. 
  <ul>
    <li>Each winner then adds the value to their running and ladder 
    scores.</li>
    <li>Each surrendered player loses one point from their running score, and one
    rank on the ladder.</li>
    <li>Each dead player loses one point from their running 
    score, and all of their ranks on the ladder.</li>
  </ul>
</p>


<h3>Notes</h3>

<ul>
  <li><em>Team games.</em> In a team game, all players on the winning team are
counted as winners, even if they personally are defeated.</li>
  <li><em>Definition of surrendering.</em>
You can surrender by resigning while still having a garrisonable castle.
Otherwise, resignation will be counted as a defeat. It's better to surrender if
you want to keep your position on the ladder.</li>
  <li><em>AIs.</em> Games involving AIs (no matter how many, or on what teams) get recorded,
but they don't count towards the ladder score. They affect the running and
average scores.</li>
</ul>

<h3>Examples</h3>

<p><em>Example 1.</em> 
Alice and Bob play on a team against Charlie and David. David surrenders, Bob
surrenders, and then Charlie is defeated. Bob is counted as a winner. The value
of the game is 2, which is divided between Alice and Bob, who gain 1 point each.
Charlie and David both lose 1 point, and Charlie loses all his ladder
points.</p>

<p><em>Example 2.</em>
Alice, Bob and Charlie play on a team against David and Edward. David is
defeated and Edward surrenders. The value of the game is 2. Alice, Bob and
Charlie each gain 2/3 of a point. David and Edward each lose 1 point.  David
also loses all his ladder points.
</p>

</div>


<div style="background-color: beige; border: 0; padding: 0; white-space: nowrap;">

<!--
<a id="source"/>
<h2>Source code</h2>
-->
<?php 
// highlight_file('index.php'); 
?>

</div>

</body>
</html>
