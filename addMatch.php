<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php 
			session_start();
			include("/common.php");
			$db = connect_and_get_DB();
			$teamList = mysqli_query($db, "SELECT * FROM dotainfo.teams;");
			if(isset($_POST["submitted"])) {
				if(isset($_POST["teamOneCombo"]) && isset($_POST["teamTwoCombo"]) && isset($_POST["bestOf"]) && isset($_POST["teamOneWins"]) && isset ($_POST["teamTwoWins"])) {
					if($_POST["teamOneCombo"] != $_POST["teamTwoCombo"]) {
						$teamOneWins = $_POST["teamOneWins"];
						$teamTwoWins = $_POST["teamTwoWins"];
						$bestOf = $_POST["bestOf"];
						if($teamOneWins + $teamTwoWins <= $bestOf) {
							$insertQuery = $db->prepare("INSERT INTO dotainfo.matches (TeamOneID,TeamTwoID,teamWinner,matchType,teamOneScore,teamTwoScore) VALUES (?,?,?,?,?,?);");
							$teamOneID = get_teamID_by_name($db,$_POST["teamOneCombo"]);
							$teamTwoID = get_teamID_by_name($db,$_POST["teamTwoCombo"]);
							if($teamOneWins > $teamTwoWins) {
								$teamWinner = $teamOneID;
							} elseif($teamOneWins < $teamTwoWins) {
								$teamWinner = $teamTwoID;
							} elseif($teamOneWins == $teamTwoWins) {
								$teamWinner = 0;
							}
							$insertQuery->bind_param("iiiiii",$teamOneID,$teamTwoID,$teamWinner,$bestOf,$teamOneWins,$teamTwoWins);
							if($insertQuery->execute()){
								$_SESSION["matchAdded"] = true;
								header("Location: /index.php");
							}
						} else {
							echo "<script type='text/javascript'>alert('Invalid Win Counts');</script>";
						}
					} else {
						echo "<script type='text/javascript'>alert('Identical Teams Chosen');</script>";
					}
				} else {
					echo "<script type='text/javascript'>alert('Data was missing');</script>";
				}
			}
		?>
	</head>
	<body>
		<div id="wrapper">
			<header>
				<h1>Add Match</h1>
			</header>
				
			<section id="teamOne">
				<h2>Team One</h2>
					<select name="teamOneCombo" form = "postForm">
						<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
								<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
						<?php endwhile; ?>
					</select>
					<p>Wins: <input type ='text' name = "teamOneWins" form="postForm"></p>
			</section>
			
			<section id="vs">
				<h2> Best Of</h2>
				<Input type = 'Radio' Name ='bestOf' value= "1" form ="postForm"> 1
				<Input type = 'Radio' Name ='bestOf' value= "2" form ="postForm"> 2
				<Input type = 'Radio' Name ='bestOf' value= "3" form ="postForm"> 3
				<Input type = 'Radio' Name ='bestOf' value= "5" form ="postForm"> 5
			</section>
			
			<aside id="teamTwo">
			
				<h2>Team Two</h2>
				<select name="teamTwoCombo" form = "postForm" >
					<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
							<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
					<?php endwhile; ?>
				</select>
				<p>Wins: <input type ='text' name = "teamTwoWins" form="postForm"></p>
			</aside>
			
			<footer>
				<form action="addMatch.php" method="POST" id ="postForm">
					<input type="hidden" name="submitted" value="true">
					<input type="submit">
				</form>
				<p><a href="/index.php">Go Back </a></p>
			</footer>

		</div>
	</body>
</html>