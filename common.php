<?php
	//takes a DB connection reference and a team name and returns the relevant teamID
	//returns false if the team is not found
	function get_teamID_by_name(&$db,$teamName) {
		$query = $db->prepare("SELECT teamID FROM dotainfo.teams WHERE teamName = ? LIMIT 1;");
		$query->bind_param("s",$teamName);
		if($query->execute()) {
			$results = $query->get_result();
			return $results->fetch_assoc()["teamID"];
		}
		return false;
	}
	
	//takes nothing but instantiates and returns a database connection 
	//dies if fails rather than returning false.
	function connect_and_get_DB() {
		$db = new mysqli("localhost", "data", "data123");
		if ($db->connect_error) {
			die("Connection to database failed: " . $db->connect_error);
		} 
		return $db;
	}
?>