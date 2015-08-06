<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php 
			session_start();
			include("/common.php");
			$db = connect_and_get_DB();
			$teamList = mysqli_query($db, "SELECT * FROM dotainfo.teams;");//get a list of team info
			if(isset($_POST["submitted"])) {//if the page has POSTed
				if(isset($_POST["teamOneCombo"]) && isset($_POST["teamTwoCombo"]) && isset($_POST["bestOf"]) && isset($_POST["teamOneWins"]) && isset ($_POST["teamTwoWins"])) {//if all data fields aren't empty
					if($_POST["teamOneCombo"] != $_POST["teamTwoCombo"]) { //if the teams are different
						$teamOneWins = $_POST["teamOneWins"];
						$teamTwoWins = $_POST["teamTwoWins"]; 
						$bestOf = $_POST["bestOf"]; //place post in variables for ease of use
						if($teamOneWins + $teamTwoWins <= $bestOf) { //if the win counts less than or equal to the bestOf count
							$insertQuery = $db->prepare("INSERT INTO dotainfo.matches (TeamOneID,TeamTwoID,teamWinner,matchType,teamOneScore,teamTwoScore) VALUES (?,?,?,?,?,?);");//prepare SQL statement
							$teamOneID = get_teamID_by_name($db,$_POST["teamOneCombo"]);
							$teamTwoID = get_teamID_by_name($db,$_POST["teamTwoCombo"]);//get numeric ID's for use in database
							if($teamOneWins > $teamTwoWins) { //if team one won the most games, set them as the winner
								$teamWinner = $teamOneID;
							} elseif($teamOneWins < $teamTwoWins) {//if team two won the most games, set them as the winner
								$teamWinner = $teamTwoID;
							} elseif($teamOneWins == $teamTwoWins) {//if it was a draw, make nobody the winner
								$teamWinner = 0;
							}
							$insertQuery->bind_param("iiiiii",$teamOneID,$teamTwoID,$teamWinner,$bestOf,$teamOneWins,$teamTwoWins); //bind the parameters to the query
							if($insertQuery->execute()){ //if the query executed successfully
								$_SESSION["matchAdded"] = true; //set session flag for return to index
								header("Location: /index.php"); //return to index
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