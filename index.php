<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php 
			$db = new mysqli("localhost", "data", "data123");
			if ($db->connect_error) {
				die("Connection to database failed: " . $db->connect_error);
			} 
			$teamList = mysqli_query($db, "SELECT * FROM dotainfo.teams;");
			if(isset($_POST["teamOneCombo"])) { 
				$teamOneResult = mysqli_query($db, "SELECT teamID FROM dotainfo.teams WHERE teamName = '" . $_POST["teamOneCombo"] . "';"); 
				if($teamOneResult != false && $row = $teamOneResult->fetch_assoc()) {
					$teamOneID = $row["teamID"];
					$teamOneGamesOne = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamOneID = " . $teamOneID . ";");
					$teamOneGamesTwo = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamTwoID = " . $teamOneID . ";"); //check for both cardinalities
					$teamOneTotalMatchWins = array();
					$teamOneTotalMatches = 0;
					$teamOneTotalWonGames = 0;
					$teamOneTotalGames = 0;
					while($row = $teamOneGamesOne->fetch_Assoc()) {
						if(isset($teamOneTotalMatchWins[$row["teamWinner"]])) {
							$teamOneTotalMatchWins[$row["teamWinner"]]++;
						} else {
							$teamOneTotalMatchWins[$row["teamWinner"]] = 1;
						}
						
						$teamOneTotalMatches++;
						$teamOneTotalWonGames += $row["teamOneScore"];
						$teamOneTotalGames += $row["teamOneScore"] + $row["teamTwoScore"];
					}
					while($row = $teamOneGamesTwo->fetch_Assoc()) {
						if(isset($teamOneTotalMatchWins[$row["teamWinner"]])) {
							$teamOneTotalMatchWins[$row["teamWinner"]]++;
						} else {
							$teamOneTotalMatchWins[$row["teamWinner"]] = 1;
						}
						$teamOneTotalMatches++;
						$teamOneTotalWonGames += $row["teamTwoScore"];
						$teamOneTotalGames += $row["teamOneScore"] + $row["teamTwoScore"];
					}
				}
				
				
			}
			if(isset($_POST["teamTwoCombo"])) { 
				$teamTwoResult = mysqli_query($db, "SELECT teamID FROM dotainfo.teams WHERE teamName = '" . $_POST["teamTwoCombo"] . "';"); 
				if($teamTwoResult != false && $row = $teamTwoResult->fetch_assoc()) {
					$teamTwoID = $row["teamID"];
					$teamTwoGamesOne = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamOneID = " . $teamOneID . ";");
					$teamTwoGamesTwo = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamTwoID = " . $teamOneID . ";"); //check for both cardinalities
					$teamTwoTotalMatchWins = array();
					$teamTwoTotalMatches = 0;
					$teamTwoTotalWonGames = 0;
					$teamTwoTotalGames = 0;
					while($row = $teamTwoGamesOne->fetch_Assoc()) {
						if(isset($teamTwoTotalMatchWins[$row["teamWinner"]])) {
							$teamTwoTotalMatchWins[$row["teamWinner"]]++;
						} else {
							$teamTwoTotalMatchWins[$row["teamWinner"]] = 1;
						}
						
						$teamTwoTotalMatches++;
						$teamTwoTotalWonGames += $row["teamOneScore"];
						$teamTwoTotalGames += $row["teamOneScore"] + $row["teamTwoScore"];
					}
					while($row = $teamTwoGamesTwo->fetch_Assoc()) {
						if(isset($teamTwoTotalMatchWins[$row["teamWinner"]])) {
							$teamTwoTotalMatchWins[$row["teamWinner"]]++;
						} else {
							$teamTwoTotalMatchWins[$row["teamWinner"]] = 1;
						}
						$teamTwoTotalMatches++;
						$teamTwoTotalWonGames += $row["teamTwoScore"];
						$teamTwoTotalGames += $row["teamOneScore"] + $row["teamTwoScore"];
					}
				}
			}

		?>
	</head>
	<body>
		<div id="wrapper">
			<header>
				<h1>Results Analytics</h1>
			</header>
				
			<section id="teamOne">
				<h2>Team One:</h2>
				
					<select name="teamOneCombo" form = "postForm">
						<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
								<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
						<?php endwhile; ?>
					</select>
					<p> Game Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalGames) ?> </p>
					<p> Game Win Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalWonGames) ?> </p>
					<p> Game Win Percent: <?php if(isset($_POST["teamOneCombo"])) echo(bcdiv($teamOneTotalWonGames,$teamOneTotalGames,3)) ?> </p>
					<p> Match Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalMatches) ?> </p>
					<p> Match Win Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalMatchWins[$teamOneID]) ?> </p>
					<p> Match Win Percent: <?php if(isset($_POST["teamOneCombo"])) echo(bcdiv($teamOneTotalMatchWins[$teamOneID],$teamOneTotalMatches,3)) ?> </p>
			</section>
			
			<section id="vs">
				<h2>Best Of:</h2>
				<select name="bestOfCombo" form = "postForm">
					<option value ="1"> 1 </option>
					<option value ="2"> 2 </option>
					<option value ="3"> 3 </option>
					<option value ="5"> 5 </option>
				</select>
			</section>
			<aside id="teamTwo">
			
				<h2>Team Two:</h2>
				<select name="teamTwoCombo" form = "postForm" >
					<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
							<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
					<?php endwhile; ?>
				</select>
			</aside>
			
			<footer>
				<form action="index.php" method="POST" id ="postForm">
					<input type="submit">
				</form>
			</footer>

		</div>
	</body>
</html>