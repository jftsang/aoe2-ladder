<html>
<head>
<title>Age of Empires II: HD scoreboard</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php
class Player {
    public $name = "";
    public $solo_won = 0;
    public $solo_drawn = 0;
    public $solo_lost = 0;
    public $team_won = 0;
    public $team_drawn = 0;
    public $team_lost = 0;
}


$file = fopen('scoreboard.csv', 'r');
while ($line = fgetcsv($file)) {
    $player = new Player();
    $player->name = $line[0];
    $player->solo_won = $line[1];
    $player->solo_drawn = $line[2];
    $player->solo_lost = $line[3];
    $player->team_won = $line[4];
    $player->team_drawn = $line[5];
    $player->team_lost = $line[6];
    $players[] = $player;
};
?>

<h1>Age of Empires II: HD scoreboard</h1>


<table>
<thead>
<tr><th>Name</th><th>solo won</th><th>solo drawn</th><th>solo lost</th>
                 <th>team won</th><th>team drawn</th><th>team lost</th></tr>
</thead>
<?php
foreach ($players as $player) {
    printf('<tr><td>%s</td><td>%d</td><td>%d</td><td>%d</td> <td>%d</td><td>%d</td><td>%d</td></tr>',
        $player->name, 
        $player->solo_won,
        $player->solo_drawn,
        $player->solo_lost,
        $player->team_won,
        $player->team_drawn,
        $player->team_lost 
    );
    printf("\n");
}
?>
</table>
</body>
</html>
