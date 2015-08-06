<?php
	function get_teamID_by_name(&$db,$teamName) {
		$query = $db->prepare("SELECT teamID FROM dotainfo.teams WHERE teamName = ? LIMIT 1;");
		$query->bind_param("s",$teamName);
		if($query->execute()) {
			$results = $query->get_result();
			return $results->fetch_assoc()["teamID"];
		}
		return false;
	}
	
	function connect_and_get_DB() {
		$db = new mysqli("localhost", "data", "data123");
		if ($db->connect_error) {
			die("Connection to database failed: " . $db->connect_error);
		} 
		return $db;
	}
?>