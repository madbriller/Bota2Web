<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php 
			session_start();
			include("/common.php");
			if(array_key_exists("teamAdded", $_SESSION) && $_SESSION["teamAdded"] == true) {
				echo "<script type='text/javascript'>alert('Team Successfully Added');</script>";
				$_SESSION["teamAdded"] = false;
			}
			if(array_key_exists("matchAdded", $_SESSION) && $_SESSION["matchAdded"] == true) {
				echo "<script type='text/javascript'>alert('Match Successfully Added');</script>";
				$_SESSION["matchAdded"] = false;
			}
			if(array_key_exists("teamRemoved", $_SESSION) && $_SESSION["teamRemoved"] == true) {
				echo "<script type='text/javascript'>alert('Team Successfully Removed');</script>";
				$_SESSION["teamRemoved"] = false;
			}
			$db = connect_and_get_DB();
			$teamList = mysqli_query($db, "SELECT * FROM dotainfo.teams;");
			if(isset($_POST["teamOneCombo"])) { //teamOne General Analysis
			$teamOneID = get_teamID_by_name($db,$_POST["teamOneCombo"]);
				if($teamOneID != false) {
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
				if(!array_key_exists($teamOneID, $teamOneTotalMatchWins)) $teamOneTotalMatchWins[$teamOneID] = 0; //if the team won no games place that in array
				$teamList->data_seek(0);
				while($row = $teamList -> fetch_assoc()) {
					if($row["teamName"] == $_POST["teamTwoCombo"]) {
						$teamOneLocation = $row["teamLocation"];
						$teamOneTier = $row["teamTier"];
					}
				}
			}
			if(isset($_POST["teamTwoCombo"])) { //team Two General Analysis
				$teamTwoResult = mysqli_query($db, "SELECT teamID FROM dotainfo.teams WHERE teamName = '" . $_POST["teamTwoCombo"] . "';"); 
				$teamTwoID = get_teamID_by_name($db,$_POST["teamTwoCombo"]);
					if($teamTwoID != false) {
					$teamTwoGamesOne = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamOneID = " . $teamTwoID . ";");
					$teamTwoGamesTwo = mysqli_query($db,"SELECT * From dotainfo.matches WHERE teamTwoID = " . $teamTwoID . ";"); //check for both cardinalities
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
				if(!array_key_exists($teamTwoID, $teamTwoTotalMatchWins)) $teamTwoTotalMatchWins[$teamTwoID] = 0;
				$teamList->data_seek(0);
				while($row = $teamList -> fetch_assoc()) {
					if($row["teamName"] == $_POST["teamTwoCombo"]) {
						$teamTwoLocation = $row["teamLocation"];
						$teamTwoTier = $row["teamTier"];
					}
				}
			}
			//teams vs analysis
			if(isset($_POST["teamOneCombo"]) && isset($_POST["teamTwoCombo"])) { //if both teams are set
				if ($_POST["teamOneCombo"] != $_POST["teamTwoCombo"]) { // and the teams aren't the same
					$teamVsQueryResult = mysqli_query($db,"SELECT * FROM dotainfo.matches WHERE (teamOneID = " . $teamOneID . " && teamTwoID = " . $teamTwoID . ") || (teamOneID = " . $teamTwoID . " && teamTwoID = " . $teamOneID . ");");
					//prepare the 2d array, first index is best of count, second is team ID, value contained is wincount
					$teamVsResults = array();
					$teamVsResults[1] = array(0=>0,$teamOneID=>0,$teamTwoID=>0);
					$teamVsResults[2] = array(0=>0,$teamOneID=>0,$teamTwoID=>0);
					$teamVsResults[3] = array(0=>0,$teamOneID=>0,$teamTwoID=>0);
					$teamVsResults[5] = array(0=>0,$teamOneID=>0,$teamTwoID=>0);
					$teamVsGameCount = 0;
					while($row = $teamVsQueryResult->fetch_Assoc()){
						$teamVsGameCount++;
						$teamVsResults[$row["matchType"]][$row["teamWinner"]]++;
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
				<h2>Team One: <?php if(isset($_POST["teamOneCombo"])) echo($_POST["teamOneCombo"])?></h2>
				<p>Location: <?php if(isset($_POST["teamOneCombo"])) echo ($teamOneLocation) ?></p>
				<p>Tier: <?php if(isset($_POST["teamOneCombo"])) echo ($teamOneTier) ?></p>
				<?php if(isset($teamOneTotalMatches) && $teamOneTotalMatches > 0): ?>
				<p> Game Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalGames) ?> </p>
				<p> Game Win Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalWonGames) ?> </p>
				<p> Game Win Percent: <?php if(isset($_POST["teamOneCombo"])&& $teamOneTotalWonGames>0 && $teamOneTotalGames>0) echo(bcmul(bcdiv($teamOneTotalWonGames,$teamOneTotalGames,3),100,1)) ?> </p>
				<p> Match Count: <?php if(isset($_POST["teamOneCombo"])) echo($teamOneTotalMatches) ?> </p>
				<p> Match Win Count: <?php if(isset($_POST["teamOneCombo"]) && array_key_exists($teamOneID,$teamOneTotalMatchWins)) echo($teamOneTotalMatchWins[$teamOneID]) ?> </p>
				<p> Match Win Percent: <?php if(isset($_POST["teamOneCombo"]) && $teamOneTotalWonGames>0 && $teamOneTotalGames>0 && array_key_exists($teamOneID,$teamOneTotalMatchWins)) echo(bcmul(bcdiv($teamOneTotalMatchWins[$teamOneID],$teamOneTotalMatches,3),100,1)) ?> </p>
				<?php else: ?>
				<p class="err">No Data to Display</p>
				<?php endif; ?>
				<br/>
				
			</section>
			
			<section id="vs">
				<h2>Vs Stats</h2>
				<h2>Win(%) &nbsp Best Of &nbsp Win(%)</h2>
				<?php if(isset($teamVsResults) && $teamVsGameCount > 0): ?>
				<p><?php echo(bcmul(bcdiv($teamVsResults[1][$teamOneID],$teamVsGameCount,3),100,1) . " &nbsp &nbsp &nbsp 1 &nbsp &nbsp &nbsp " . bcmul(bcdiv($teamVsResults[1][$teamTwoID],$teamVsGameCount,3),100,1)); ?> </p>
				<p><?php echo(bcmul(bcdiv($teamVsResults[2][$teamOneID],$teamVsGameCount,3),100,1) . " &nbsp &nbsp &nbsp 2 &nbsp &nbsp &nbsp " . bcmul(bcdiv($teamVsResults[2][$teamTwoID],$teamVsGameCount,3),100,1)); ?> </p>
				<p><?php echo(bcmul(bcdiv($teamVsResults[3][$teamOneID],$teamVsGameCount,3),100,1) . " &nbsp &nbsp &nbsp 3 &nbsp &nbsp &nbsp " . bcmul(bcdiv($teamVsResults[3][$teamTwoID],$teamVsGameCount,3),100,1)); ?> </p>
				<p><?php echo(bcmul(bcdiv($teamVsResults[5][$teamOneID],$teamVsGameCount,3),100,1) . " &nbsp &nbsp &nbsp 5 &nbsp &nbsp &nbsp " . bcmul(bcdiv($teamVsResults[5][$teamTwoID],$teamVsGameCount,3),100,1)); ?> </p>
				<?php else: ?>
				<p class="err">No Data to Display</p>
				<?php endif; ?>
			</section>
			
			<aside id="teamTwo">
				<h2>Team Two: <?php if(isset($_POST["teamTwoCombo"])) echo($_POST["teamTwoCombo"])?></h2>
				<p>Location: <?php if(isset($_POST["teamOneCombo"])) echo ($teamOneLocation) ?></p>
				<p>Tier: <?php if(isset($_POST["teamOneCombo"])) echo ($teamOneTier) ?></p>
				<?php if(isset($teamTwoTotalMatches) && $teamTwoTotalMatches>0): ?>
				<p> Game Count: <?php if(isset($_POST["teamTwoCombo"])) echo($teamTwoTotalGames) ?> </p>
				<p> Game Win Count: <?php if(isset($_POST["teamTwoCombo"])) echo($teamTwoTotalWonGames) ?> </p>
				<p> Game Win Percent: <?php if(isset($_POST["teamTwoCombo"])&& $teamTwoTotalWonGames>0 && $teamTwoTotalGames>0) echo(bcmul(bcdiv($teamTwoTotalWonGames,$teamTwoTotalGames,3),100,1)) ?> </p>
				<p> Match Count: <?php if(isset($_POST["teamTwoCombo"])) echo($teamTwoTotalMatches) ?> </p>
				<p> Match Win Count: <?php if(isset($_POST["teamTwoCombo"]) && array_key_exists($teamTwoID,$teamTwoTotalMatchWins)) echo($teamTwoTotalMatchWins[$teamTwoID]) ?> </p>
				<p> Match Win Percent: <?php if(isset($_POST["teamTwoCombo"]) && $teamTwoTotalWonGames>0 && $teamTwoTotalGames>0 && array_key_exists($teamTwoID,$teamTwoTotalMatchWins)) echo(bcmul(bcdiv($teamTwoTotalMatchWins[$teamTwoID],$teamTwoTotalMatches,3),100,1))?> </p>
				<?php else: ?>
				<p class="err">No data to Display</p>
				<?php endif; ?>
				<br/>
			</aside>
			
			<footer>
				<?php if(isset($teamVsResults)) : ?>
					<p>Number of Vs Games: <?php echo($teamVsGameCount) ?> </p>
				<?php endif ?>
				
				<select name="teamOneCombo" form = "postForm">
					<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
							<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
					<?php endwhile; ?>
				</select>
				&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
				<select name="teamTwoCombo" form = "postForm" >
					<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
							<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
					<?php endwhile; ?>
				</select><br/>
				<form action="index.php" method="POST" id ="postForm">
					<input type="submit">
				</form>
				<p><a href="/addTeam.php">Add Team</a></p>
				<p><a href="/removeTeam.php">Remove Team</a></p>
				<p><a href="/addMatch.php">Add Match</a></p>
			</footer>

		</div>
	</body>
</html>