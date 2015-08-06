<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php
			session_start();
			include("/common.php");
			if(isset($_POST["submitted"])) { //if the form has been POSTed
				if(!empty($_POST["teamName"]) && !empty($_POST["teamLocation"]) && !empty($_POST["teamTier"])) { //if no data is missing
					$db = connect_and_get_DB();
					$teamListQuery = mysqli_query($db,"SELECT teamName FROM dotainfo.teams"); //get a list of team names
					while($row = $teamListQuery->fetch_assoc()) { //for each team name
						if($row["teamName"] == $_POST["teamName"]) { //check it against the new team
							$teamExists = true; 
							break; //if it exists, set the flag and break from the while
						}
					}
					if($teamExists == false) { //if the team doesn't exist
						$insertQuery = $db->prepare("INSERT INTO dotainfo.teams(teamName, teamLocation, teamTier) VALUES (?,?,?)"); //prepare the SQL statement
						$insertQuery->bind_param("sss",$_POST["teamName"],$_POST["teamLocation"],$_POST["teamTier"]); //bind the parameters
						if($insertQuery->execute()){ //if the query successfully executes
							$_SESSION["teamAdded"] = true; //set the team added flag for redirect to index
							header("Location: /index.php"); //redirect to index
						} else {
							echo "<script type='text/javascript'>alert('Team Not Successfully Added');</script>";
						}
					} else {
						echo "<script type='text/javascript'>alert('Team Name Taken');</script>";
					}
				} else {
					echo "<script type='text/javascript'>alert('Team Data was Missing');</script>";
				}			
			}
		?>
	</head>
	<body>
		<h1>Add Team</h1><br/><br/>
		<form action="addTeam.php" method="POST">
			Team Name: <input type="text" name="teamName"></br></br>
			Team Location: <input type="text" name="teamLocation"></br></br>
			Team Tier: <select name="teamTier">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select></br></br>
			<input type="hidden" name="submitted" value=true>
			<input type="submit" value="Submit"></br>
		</form>
		<p><a href="/index.php"> Go Back </a></p>
	</body>
</html>