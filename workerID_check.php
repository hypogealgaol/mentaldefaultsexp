<?php

session_start();

// Save any submitted data
foreach ($_POST as $key => $value) {
	$_SESSION[$key] = $value;
}

// Include all necessary PHP scripts
include "necessary_functions.php";
// include "time_online.class.php";
// $time = new time_online;


?>

<!DOCTYPE html>
<html>
<head>
	<title>Mechanical Turk HIT Eligibility Test</title>

	<!-- Load External js scripts -->
	<script type="text/javascript" src="http://mbostock.github.com/d3/d3.js?1.29.1"></script>
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<!-- CSS styling goes in a separate document -->
	<link href="iterativeFaces.css" rel="stylesheet" type="text/css">
	
</head>
<body>
	
	<?php 
	// % -------------------------------------------------------------------------

	ini_set("display_errors","1");
	ERROR_REPORTING(E_ALL);

	// Sets required field error (DO NOT COMMENT OUT)
	$error= NULL; 
	$workerIDList = 'past_participant_list.csv';
	$delimiter = ',';
	if (($_SERVER['REQUEST_METHOD'] == 'GET') && (! isset($_POST['IDCollection']))) {
		session_unset();   // not sure what this does in this context
		$stage = 1; #This dictates which "page" loads
		
		if(isset($_REQUEST['workerId'])){
			$workerID = $_REQUEST['workerId'];

		} 
		else {
			$workerID = 'A1BGDXZ95IQ3W';

		}


		// display form
		?>
		<div id="instructions1" class="instructions">
			This is a tool that checks whether you are eligible to play our short memory game. Enter your worker ID below and click "Submit" in order to find out if you can participate.<br /> <br />
		</div>
		<div class="survey">
			
			<form name="workerIDform" id="workerIDform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<label id="whatWorkerID">What is your M-TURK Worker ID? (e.g. A1BGDXZ95IQ3W) </label>
					<input type="text" name="workerID" id="workerID" value = "<?php echo $workerID; ?>" size="30" style="height:1em;">

				<div class="buttonHolder">
					<label style="visibility:hidden;  display: block; float: none; clear: left; width: 50%; text-align: left;">This is the next button</label>
					<center><input type="submit" name="IDCollection" value="Submit" style="display:block; float:none; text-align:center; margin-top:20px; vertical-align:middle;"></center>
				</div>

			</form>
		</div>

<?php
	}

	// % -------------------------------------------------------------------------
	// Now that the workerID has been input check if it's eligible.
	else if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_SESSION['IDCollection']))) { 
	// else {

		// Check if subject has done this study before with their worker ID.
		$workerID = trim($_SESSION['workerID']);
		$properID = validate_worker_id($workerID);
		$eligible = $properID;
		$excludedWorkerIDs = readCSV($workerIDList);

		// $workerIDPattern = "/". $workerID . "/i";
		
		// $ineligible = false;

		foreach ($excludedWorkerIDs as $fields) {
			if (in_array_case_insensitive($workerID, $fields)) {
				$eligible = false;
			}
		}	

		if (!$properID) { ?>
			<div id="notEligible" class="instructions" style="color:red"><center><h2> Oops! It looks like you pasted
			something that is not a Worker ID into the Worker ID box. Your worker ID is a series of random
			numbers and letters starting with A (e.g. A1BGDXZ95IQ3W). It is not the study URL,
			nor is it your email address! To try again with your real worker ID, please refresh the page. </h2> </center>
			</div>

		<?php
		}

		else if (!$eligible) { ?>

			<div id="notEligible" class="instructions" style="color:red"><center><h2> You are not eligible to take the test! Please do not accept the HIT. </h2> </center>
			</div>



		<?php	
		} else { ?>

			<div id="youAreEligible" class="instructions" style="color:green"><center><h2> You are eligible to take the test! </h2> </center></div>

		<?php
		}
	}

?>

</body>
</html>