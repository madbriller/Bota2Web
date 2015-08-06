<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php
			session_start();
			include("common.php");
			$db = connect_and_get_DB();
			$teamList = mysqli_query($db, "SELECT * FROM dotainfo.teams;");
			if(isset($_POST["submitted"])) {
				$teamID = get_teamID_by_name($db,$_POST["teamCombo"]);
				$query = $db->prepare("DELETE FROM dotainfo.teams WHERE teamID = ? LIMIT 1;");
				$query->bind_param("i",$teamID);
				if($query->execute()){
					$_SESSION["teamRemoved"] = true;
					if(isset($_POST["remove"]) && $_POST["remove"] == true) {
						$query = $db->prepare("DELETE FROM dotainfo.matches WHERE teamOneID = ? OR teamTwoID = ?;");
						$query->bind_param("ii",$teamID,$teamID);
						$query->execute();
					} 
					header("Location: /index.php");
				} else {
					echo "<script type='text/javascript'>alert('Team Not Successfully Removed');</script>";
				}
				
			}
		?>
	</head>
	<body>
		<h1>Remove Team</h1><br/><br/>
		<form action="removeTeam.php" method="POST">
			<select name="teamCombo">
			<p>Team to Remove:</p>
				<?php $teamList->data_seek(0); while($row = $teamList -> fetch_assoc()): ?>
						<option value="<?=($row["teamName"])?>"><?=($row["teamName"])?></option>
				<?php endwhile; ?>
			</select>
			<p>Remove all Related games?
			<Input type = 'Checkbox' Name ='remove' value="true"></p></br></br>
			<input type="hidden" name="submitted" value=true>
			<input type="submit" value="Submit"></br>
		</form>
		<p><a href="/index.php"> Go Back </a></p>
	</body>
</html>