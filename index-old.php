<?php
require_once('classdefs.php');
$file = fopen('ladder-history', 'r');
$games = array();

/* Read the games from the ladder history */
while($str = fgets($file)) {
    $game = new Game;
    sscanf($str, "%04d-%02d-%02d: %s\n", 
        $year, $month, $day, 
        $teams
    );
    $teams = explode(";",$teams);
    foreach ($teams as $i=>$team) {
        $team = explode(",",$team);
        $scores[$i] = $team[0];
        $teams[$i] = array_slice($team,1);
    }
    $game->game_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $game->teams = $teams;
    $game->scores = $scores;
    $games[] = $game;
}

/* Sort the games by date so that the ladder history is accurate */

/* TODO */

/* Go through each game, go through each team in each game, and add scores to 
 * that player. */
foreach ($games as $game) {
    foreach ($game->teams as $i=>$team) {
        foreach($team as $player) {
            if (is_null($players[$player])) {
                $players[$player] = new Player;
                $players[$player]->name = $player;
            }
            if (count($team) == 1) {
                $players[$player]->played_solo ++;
                $players[$player]->total_solo += $game->scores[$i];
                if ($game->scores[$i] > -1) 
                    $players[$player]->ladder_solo += $game->scores[$i];
                else
                    $players[$player]->ladder_solo = 0;
            } else {
                $players[$player]->played_team ++;
                $players[$player]->total_team += $game->scores[$i];
                if ($game->scores[$i] > -1) 
                    $players[$player]->ladder_team += $game->scores[$i];
                else
                    $players[$player]->ladder_team = 0;
            }

            if ($players[$player]->ladder_solo > $players[$player]->peak_solo)
                $players[$player]->peak_solo = $players[$player]->ladder_solo;
            if ($players[$player]->ladder_team > $players[$player]->peak_team)
                $players[$player]->peak_team = $players[$player]->ladder_team;
        }
    }
}

/* Sort the players by some means or another. */
require_once('sorting_functions.php');
switch ($_REQUEST['sortby']) {
    case 'soloavg':
        usort($players, 'cmp_by_solo_scores');
        break;
    case 'teamavg':
        usort($players, 'cmp_by_team_scores');
        break;
    case 'solo_played':
        usort($players, 'cmp_by_solo_played');
        break;
    case 'sololadder':
        usort($players, 'cmp_by_solo_ladder');
        break;
    case 'teamladder':
        usort($players, 'cmp_by_team_ladder');
        break;
    case 'solopeak':
        usort($players, 'cmp_by_peak_solo');
        break;
    case 'teampeak':
        usort($players, 'cmp_by_peak_team');
        break;
    case 'name':
    default:
        usort($players, 'cmp_by_player_name');
        break;
}


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

<div style="width: 50%; float: left;">

<h2>Scoreboard</h2>
<table>
<thead> <tr>
  <th><a href="<?=$PHP_SELF?>?sortby=name">player</a></th> 
  <th>played<br/>solo</th> 
  <th>played<br/>team</th> 
  <th>total score<br/>solo</th> <th>total score<br/>team</th> 
<!--
  <th><a href="<?=$PHP_SELF?>?sortby=soloavg">average score<br/>solo</a></th> 
  <th><a href="<?=$PHP_SELF?>?sortby=teamavg">average score<br/>team</a></th> 
-->
  <th><a href="<?=$PHP_SELF?>?sortby=sololadder">ladder score<br/>solo</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=teamladder">ladder score<br/>team</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=solopeak">peak solo<br/>team</a></th>
  <th><a href="<?=$PHP_SELF?>?sortby=teampeak">peak team<br/>team</a></th>
</tr> </thead>
<tbody>
<?php
foreach ($players as $player) {
    printf('<tr><td>%s</td>', $player->name);
    printf('<td>%d</td><td>%d</td>', 
        $player->played_solo, $player->played_team);
    printf('<td>%+d</td><td>%+d</td>', 
        $player->total_solo, $player->total_team); 
/*
    printf('<td>%s</td><td>%s</td>', 
        $player->played_solo > 0 ? 
            sprintf('%+.1f', $player->total_solo/$player->played_solo) : '--', 
        $player->played_team > 0 ? 
            sprintf('%+.1f', $player->total_team/$player->played_team) : '--' 
    ); 
*/
    printf('<td>%d</td><td>%d</td>', 
        $player->ladder_solo, $player->ladder_team);
    printf('<td>%d</td><td>%d</td>', 
        $player->peak_solo, $player->peak_team);
    printf("</tr>\n");
}
?>
</tbody>
</table>

<a id="explanation"/>
<h2>Explanation</h2>

<p>Each player has four scores: <em>solo</em> or <em>team</em>, and 
<em>total</em> or <em>ladder</em>.</p>

<p>A game counts towards a player's <em>team</em> score if the player is part of a <em>permanent alliance</em> throughout the game. Otherwise, the game counts towards that player's <em>solo</em> score.</p>

<p>A permanent alliance, or team, can be through 'locked teams' or through two 
players controlling the same civilisation.</p>

<!--
<p><em>Solo</em> scores are counted towards in games where the player plays by 
themself. <em>Team</em> scores are counted towards in games where the player 
plays in a team.</p>
-->

<p>Notes:</p>
<ul>
    <li>The team score only applies to games where the teams are fixed from the 
beginning. In particular, a game may count towards a player's solo score even if 
they win through an allied victory.</li>
    <li>The same game may count towards one player's solo score but towards 
another's team score.</li>
</ul>

<p>Each game continues until either there is only one team surviving, or until 
all surviving teams agree on a draw. At that point, each member of a surviving
team gains 1 point towards their <em>total</em> score for each non-surviving 
opponent player; and each member of a defeated team loses 1 point from 
<em>total</em>, and their <em>ladder</em> scores are reset to zero.</p>

<p>(Note therefore that a player can still gain team points even if defeated, 
provided that they are part of an ultimately surviving team.)</p>

<!--
<p>Each victorious player gains 1 point towards their <em>total</em> and 
<em>ladder</em> scores for each non-victorious player in the game. Each defeated 
player loses 1 point in their <em>total</em> score, and their <em>ladder</em> 
score is reset to zero. If a game is interrupted or declared a draw, then no 
player gains or loses any points.</p>
-->

<p>The <em>played</em> columns simply record the number of games a player has 
played, as a solo or as a team member.</p>

<p><em>Example 1.</em> Players A, B, C, D, E, and F play a game. Team 1 
consists of players A and B; Team 2 of C and D; Team 3 of E on their own; and 
Team 4 of F on their own. These teams are locked. This game therefore counts 
towards the team scores of A, B, C and D; but towards the solo scores of E and 
F.</p> 

<p>Players C, D and E are defeated, and the surviving A, B and F then agree on 
a draw. Since three players were defeated, each of A, B and F score three 
points. The defeated players each lose one point, as well as all their ladder 
points.</p>

<p><em>Example 2.</em> Suppose instead that B, C, D, and E are all defeated, 
and that A and F agree on a draw between them. Then both A and B gain three points; and F gains four points.</p> 

<p><em>Optional rule: Backgammon-style doubling.</em> In games with exactly two 
teams (regardless of how many players are in each team), the teams may choose 
to play with 'backgammon-style' doubling. A team, T1, may offer a 'double'; the 
other team, T2, must either accept or decline the double. (If they play on and
do not respond, then they are taken to have accepted.) If T2 declines, then 
they forfeit the game immediately. If they accept, then the value of the game 
is doubled. T2 may then offer a redouble which T1 may accept or decline, and so 
on.</p>

</div>

<div style="text-align: justify; width: 50%; float: right;">

<h2>Ladder history</h2>
<?php
$lmh = isset($_REQUEST['lasthowmany']) ? $_REQUEST['lasthowmany'] : 5;
?>
    <p>Showing last <?=$lmh?> games. <a href="ladder-history">Click here for full history</a></p>
<ul>
<?php
foreach (array_reverse(array_slice($games, -$lmh)) as $game) {
    printf('<li><em>%s</em><br/>', $game->game_date);
    foreach ($game->teams as $i=>$team) {
        printf('<strong>%d:</strong> %s',$game->scores[$i],
            implode(", ", $team)
        );
        printf($i+1 == count($game->teams) ? "\n" : ";\n");
    }
    printf("</li>\n");
}
?>
</ul>



<div style="background-color: beige; border: 0; padding: 0; white-space: nowrap;">

<a id="source"/>
<h2>Source code</h2>

<?php highlight_file('index.php'); ?>

</div>
</div>

</body>
</html>
