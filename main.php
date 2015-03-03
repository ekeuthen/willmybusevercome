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
		echo "<ul>";
		foreach ($predictionsArray as $prediction) {
			echo "<li>$prediction</li>";
		}
		echo "</ul>";
	}
?>

<html>
	<head>
		<title>Will Bus Ever Come</title>
	</head>

	<body>

	<?php
		$apiKey = 'wX9NwuHnZU2ToO7GmGR9uw';
		$bus87 = '87';
		$bus88 = '88';
		$inboundId = "1";
		$outboundId = "0";

		echo '<h2>To work</h2>';
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
	?>
 	</body>
 </html>
