<!DOCTYPE html>

<?php
	
	function getPredictionForStop($apiKey, $queryParams) {
		return json_decode(
			file_get_contents("http://realtime.mbta.com/developer/api/v2/predictionsbystop?api_key=$apiKey&format=json&$queryParams"), 
			true);
	}

	function parsePredictions($json, $directionId, $busNumber) {
		$predictions = array();
		// get all modes
		$modes = $json["mode"];

		// select bus mode
		foreach ($modes as $mode) {
			if ($mode["mode_name"] == "Bus") {
				$routes = $mode["route"];
				// for each route
				foreach ($routes as $route) {
					if ($route["route_id"] == $busNumber) {
						// get all directions
						$directions = $route["direction"];
						// for each direction
						foreach ($directions as $direction) {
							// check direction = 0 (outbound)
							if ($direction["direction_id"] == $directionId) {
								// get trips
								$trips = $direction["trip"];
								// display all trips
								foreach ($trips as $trip) {
									array_push($predictions, $trip["pre_away"]);
								}
							}
						}
					}
				}
			}
		}
		return $predictions;
	}

	function printPredictions($busNumber, $predictionsArray) {
		echo "<h4>$busNumber</h4>";
		echo "<ul class='list-group'>";
		if (empty($predictionsArray)) {
			echo '<li class="list-group-item"><span class="badge">!</span>' . 'Please start walking - no buses in the foreseeable future!' . '</li>';
		}
		foreach ($predictionsArray as $prediction) {
			echo '<li class="list-group-item">' . formatSeconds($prediction) . '</li>';
		}
		echo "</ul>";
	}

	function formatSeconds($seconds){
		//convert $seconds into an interval
		$intseconds = intval ($seconds);
		$sec = $intseconds % 60;
		$min = ($intseconds - $sec )/ 60;
		return $min.' min '.$sec.' sec';
	}

?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Will Bus Ever Come</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	</head>

	<body>

	<?php
		$apiKey = 'OCarOTQ4MkihZKLn-9Iojg';
		$bus69 = '69';
		$bus87 = '87';
		$bus88 = '88';
		$inboundId = "1";
		$outboundId = "0";

		echo "<div class='container'>";

			echo "<div class='page-header'>
	  				<h1>Will Bus Ever Come?</h1>
				</div>";

			echo '<h2>To work</h2>';

			// bus 69
			$johnstonStopId = '2168';
			$jsonPredictions = getPredictionForStop($apiKey, "stop=$johnstonStopId");
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $inboundId, $bus69);
			// print predictions
			printPredictions($bus69, $predictions);

			// bus 87
			$beechStopId = '2584';
			$jsonPredictions = getPredictionForStop($apiKey, "stop=$beechStopId");
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $inboundId, $bus87);
			// print predictions
			printPredictions($bus87, $predictions);

			// bus 88
			$willowStopId = '2675';
			$jsonPredictions = getPredictionForStop($apiKey, "stop=$willowStopId");
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $inboundId, $bus88);
			// print predictions
			printPredictions($bus88, $predictions);

			echo '<h2>Home!</h2>';
			$lechmereStopId = '14150';
			$jsonPredictions = getPredictionForStop($apiKey, "stop=$lechmereStopId");

			// bus 69
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $outboundId, $bus69);
			// print predictions
			printPredictions($bus69, $predictions);

			// bus 87
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $outboundId, $bus87);
			// print predictions
			printPredictions($bus87, $predictions);

			// bus 88
			// parse predictions
			$predictions = parsePredictions($jsonPredictions, $outboundId, $bus88);
			// print predictions
			printPredictions($bus88, $predictions);

		echo "</div>";
	?>
 	</body>
 </html>
