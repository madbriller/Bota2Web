<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php
			session_start();
			include("/common.php");
			if(isset($_POST["submitted"])) {
				if(!empty($_POST["teamName"]) && !empty($_POST["teamLocation"]) && !empty($_POST["teamTier"])) {
					$db = connect_and_get_DB();
					$teamListQuery = mysqli_query($db,"SELECT teamName FROM dotainfo.teams");
					while($row = $teamListQuery->fetch_assoc()) {
						if($row["teamName"] == $_POST["teamName"]) {
							$teamExists = true;
							break;
						}
					}
					if($teamExists == false) { 
						$insertQuery = $db->prepare("INSERT INTO dotainfo.teams(teamName, teamLocation, teamTier) VALUES (?,?,?)");
						$insertQuery->bind_param("sss",$_POST["teamName"],$_POST["teamLocation"],$_POST["teamTier"]);
						if($insertQuery->execute()){
							$_SESSION["teamAdded"] = true;
							header("Location: /index.php");
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