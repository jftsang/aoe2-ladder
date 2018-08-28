<?php
require_once('classdefs.php');
$file = fopen('ladder-history', 'r');
$games = array();

/* Read the games from the ladder history */
while($str = fgets($file)) {
    $str = str_replace(' ', '', $str);
    $game = new Game;
    sscanf($str, "%04d-%02d-%02d:%s\n", 
        $year, $month, $day, 
        $teams
    );
    $teams = explode(";",$teams);

    if (strlen($teams[0]) > 0)
        $game->winners = explode(",", $teams[0]);
    else 
        $game->winners = array();

    if (strlen($teams[1]) > 0)
        $game->surrendered = explode(",", $teams[1]);
    else 
        $game->surrendered = array();

    if (strlen($teams[2]) > 0)
        $game->dead = explode(",", $teams[2]);
    else 
        $game->dead = array();

    $game->game_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $games[] = $game;
}

/* Sort the games by date so that the ladder history is accurate */

/* TODO */

/* Go through each game, go through each team in each game, and add scores to 
 * that player. */
foreach ($games as $game) {
    $value = (count($game->surrendered) + count($game->dead)) / count($game->winners);
    foreach ($game->winners as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->won++;
        $player->running += $value;
        $player->ladder += $value;
        if($player->ladder > $player->peak)
            $player->peak = $player->ladder;
        $players[$playername] = $player;
    } 

    foreach($game->surrendered as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->surrendered++;
        $player->running = $player->running - 1;
        $player->ladder = max($player->ladder-1, 0);
        $players[$playername] = $player;
    }

    foreach($game->dead as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->died++;
        $player->running = $player->running - 1;
        $player->ladder = 0;
        $players[$playername] = $player;
    }


    $game->value = $value;
}

foreach ($players as $player)
{
    $player->running = floatval($player->running);
    $player->average = floatval($player->running / $player->played);
}

/* Sort the games */

$sortfield = isset($_REQUEST['sortby']) 
    ? $_REQUEST['sortby']
    : "ladder";
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 1;
switch ($sortfield)
{
    case 'name':
        usort($players, function($p1,$p2) use($sortfield, $order) {
            return $order*strcmp($p1->$sortfield, $p2->$sortfield);
        });
        break;
    default:
        // echo "<pre>"; var_dump($players); echo "</pre>";
        usort($players, function($p1,$p2) use($sortfield, $order) { 
            $ret =  $order*($p2->$sortfield - $p1->$sortfield);
            /*
            echo "<pre>";
            var_dump($sortfield);
            var_dump($p1->$sortfield);
            var_dump($p2->$sortfield);
            var_dump($ret);
            echo "</pre><br/>";
             */
            return $ret > 0 ? 1 : -1;
        }) or die("Sort failed!");
        break;
}
// echo "<pre>"; var_dump($players); echo "</pre>";
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
<h2>Explanation</h2>

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

<p>In a team game, all players on the winning team are counted as winners, even
if they personally are defeated.</p>


<p><em>Surrendering.</em>
A player may surrender by resigning while still having a garrisonable castle.
Otherwise, resignation will be counted as a defeat. It's better to surrender if
you want to keep your position on the ladder.</p>

<p><em>Example 1.</em> 
Alice and Bob play on a team against Charlie and David. David surrenders, Bob
surrenders, and then Charlie is defeated. Bob is counted as a winner. The value
of the game is 2, which is divided between Alice and Bob, who gain 1 point each.
Charlie and David both lose 1 point, and Charlie loses all his ladder
points.</p>

<p><em>Example 2.</em>
Alice, Bob and Charlie play on a team against David and Edward. David is
defeated and Edward surrenders. The value of the game is 2. Alice, Bob and
Charlie each gain 2/3 of a point. David and Edward each lose 1 point, and David
loses all his ladder points.
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
