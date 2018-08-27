<?php
require_once('classdefs.php');
function cmp_by_player_name($p1, $p2) {
    return strcmp($p1->name, $p2->name);
}

function cmp_by_solo_played($p1, $p2) {
    if ($p1->played_solo == $p2->played_solo) return 0;
    else if ($p1->played_solo > $p2->played_solo) return -1;
    else if ($p1->played_solo < $p2->played_solo) return +1;
}


/* Sort the players according to their average solo score */
function cmp_by_solo_scores($p1, $p2) {
    if ($p1->played_solo == 0) $s1 = -INF;
    else $s1 = $p1->total_solo / $p1->played_solo;
    if ($p2->played_solo == 0) $s2 = -INF; 
    else $s2 = $p2->total_solo / $p2->played_solo;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

/* Alternatively, sort the players according to their average team scores */
function cmp_by_team_scores($p1, $p2) {
    if ($p1->played_team == 0) $s1 = -INF;
    else $s1 = $p1->total_team / $p1->played_team;
    if ($p2->played_team == 0) $s2 = -INF; 
    else $s2 = $p2->total_team / $p2->played_team;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

function cmp_by_solo_ladder($p1, $p2) {
    $s1 = $p1->ladder_solo;
    $s2 = $p2->ladder_solo;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

function cmp_by_team_ladder($p1, $p2) {
    $s1 = $p1->ladder_team;
    $s2 = $p2->ladder_team;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

function cmp_by_peak_solo($p1, $p2) {
    $s1 = $p1->peak_solo;
    $s2 = $p2->peak_solo;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

function cmp_by_peak_team($p1, $p2) {
    $s1 = $p1->peak_team;
    $s2 = $p2->peak_team;
    if ( $s1 == $s2 ) return 0;
    else if ( $s1 > $s2 ) return -1;
    else if ( $s1 < $s2 ) return +1;
}

function cmp_general($p1, $p2, $sortfield) {
    //var_dump($p1);
    //var_dump($p2);
    //var_dump($sortfield);
    return ($p2->$sortfield - $p1->$sortfield);
}


?>
