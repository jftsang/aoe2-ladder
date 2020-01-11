<?php
require_once('classdefs.php');
$file = fopen('ladder-history', 'r');
$games = array();

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

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


    /* Flags to record whether this game counts towards ladder/running scores. 
     * (TODO unimplemented feature: allow some games to be marked as
     * friendlies) 
     */
    $isLadder = true;

    /* Check if any AIs played. If yes, then the ladder score doesn't get affected. */
    $AIplayers = array("AIEasiest", "AIEasy", "AIModerate", "AIHard", "AIHardest");
    if (   !empty(array_intersect($game->winners, $AIplayers))
        || !empty(array_intersect($game->surrendered, $AIplayers))
        || !empty(array_intersect($game->dead, $AIplayers)) )
    {
        $isLadder = false;
    }
    /*
    echo"<pre>";
    var_dump($game);
    var_dump(array_intersect($game->died, $AIplayers));
    var_dump($isLadder);
    echo"</pre>";
     */

    foreach ($game->winners as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->won++;
        $player->running += $value;
        if ($isLadder)
        {
            $player->ladder += $value;
            if($player->ladder > $player->peak)
                $player->peak = $player->ladder;
        }
        $player->lastplayed = $game->game_date;
        $players[$playername] = $player;
    } 

    foreach($game->surrendered as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->surrendered++;
        $player->running = $player->running - 1;
        if ($isLadder)
        {
            $player->ladder = max($player->ladder-1, 0);
            $player->lastplayed = $game->game_date;
        }
        $players[$playername] = $player;
    }

    foreach($game->dead as $playername)
    {
        $player = $players[$playername];
        $player->name = $playername;
        $player->played++;
        $player->died++;
        $player->running = $player->running - 1;
        if ($isLadder)
        {
            $player->ladder = 0;
        }
        $player->lastplayed = $game->game_date;
        $players[$playername] = $player;
    }


    $game->value = $value;
}

/* Calculate each player's running score and average score. */
foreach ($players as $player)
{
    $player->running = floatval($player->running);
    $player->average = floatval($player->running / $player->played);

    $player->inactivity = floor( (time() - strtotime($player->lastplayed))/86400 );
}

/* Sort the games */

$sortfield = isset($_REQUEST['sortby']) 
    ? $_REQUEST['sortby']
    : "average";
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
