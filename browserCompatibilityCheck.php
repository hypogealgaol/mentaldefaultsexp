<?php

session_start();

// Save any submitted data
foreach ($_POST as $key => $value) {
	$_SESSION[$key] = $value;
}

// Include all necessary PHP scripts
// include "time_online.class.php";
// $time = new time_online;

?>

<!DOCTYPE html>
<html>
<head>
	<title>Mechanical Turk HIT Browser Compatibility Test</title>

	<!-- Load External js scripts -->
	<script type="text/javascript" src="http://mbostock.github.com/d3/d3.js?1.29.1"></script>
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<!-- CSS styling goes in a separate document -->
	<link href="iterativeFaces.css" rel="stylesheet" type="text/css">
	
</head>
<body>
<?php 
	// Check if a compatible browser is being used.
	$userAgent = $_SERVER["HTTP_USER_AGENT"];      // Get user-agent of browser

	$safariorchrome = strpos($userAgent, 'Safari') ? true : false;     // Browser is either Safari or Chrome (since Chrome User-Agent includes the word 'Safari')
	$chrome = strpos($userAgent, 'Chrome') ? true : false;             // Browser is Chrome
	$firefox = strpos($userAgent, 'Firefox') ? true : false;             // Browser is Firefox
	$ie = strpos($userAgent, 'MSIE') ? true : false;             // Browser is IE
	if($safariorchrome == true AND $chrome == false){ $safari = true; }     // Browser should be Safari, because there is no 'Chrome' in the User-Agent

	if($chrome) {die('Cannot use Chrome! Please use Firefox or Internet Explorer instead!');}


	//% -------------------------------------------------------------------------
	// Stop mobile users from accessing the website
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
	die('Cannot use mobile devices! Please use a laptop or desktop computer.');
	if (($_SERVER['REQUEST_METHOD'] == 'GET') && (! isset($_POST['browserCheck']))) { ?>

		<center> <div id="container">
		<p id="instructions1"style="text-align:justify; margin: 0 auto; width: 60em;">
			This is a short initial test to make sure your browser can handle the real test. Please move your mouse around the circle
			at least once, and click on the rectangle at the edge of the ring to see its possible colors. Any transitions as you 
			move your mouse should appear smooth.
			<br /> <br />
			<b>Note that failure to answer all three questions below correctly
			in this section will mean that your browser is not capable of displaying the 
			test properly, and you will be ineligible to accept the HIT.</b>
			<br /> <br />
			Now, please answer the questions below.  </p>
		<div id ="dynamic-container">
			<div id ="marker"></div>
			<div id ="innerCircle"> <!-- <p id="outputNumber"> <?php echo $outputNumber ?>  </p> -->
				<div class="circle" id="testSquare"> </div>
			</div>                
		</div>   </div> </center> 
		
		<form name="browserCheckForm" id="browserCheckForm" method="post" autocomplete="off" action='<?php echo $_SERVER['SCRIPT_NAME'] ?>' align="center">	

			<div id="browserCheckSurvey" class="survey">
						<label id="rectangleColorsLabel">Click on the rectangle at the edge of the ring a few times and report its colors:</label> 
							<select id="rectangleColors" name="rectangleColors">
								<option value="red/black" selected="selected">red/black</option>
								<option value="red/green">red/green</option>
								<option value="black/green">black/green</option>
								<option value="yellow/blue">yellow/blue</option>
							</select>
						<label id="shapeLabel">What shape is in the middle of the ring, if any?</label> 
							<select id="shape" name="shape">
								<option value="None" selected="selected">None</option>
								<option value="Square">Square</option>
								<option value="Circle">Circle</option>
								<option value="Circle/Square hybrid">Circle/Square hybrid</option>
							</select>
						<label id="whatHappenedLabel">Did anything happen in the middle of the ring as you moved the mouse?</label>
							<select id="whatHappened" name="whatHappened">
								<option value="Nothing happened because there was no shape" selected="selected">Nothing happened because there was no shape</option>
								<option value="The shape changed color">The shape changed color</option>
								<option value="The shape changed from one shape to another">The shape changed from one shape to another</option>
								<option value="The shape rotated">The shape rotated</option>
								<option value="The shape changed size">The shape changed size</option>
							</select>
			</div><br />

			<div class="surveyButtonHolder">
				<input type='hidden' name='browserCheckPageTime' id="browserCheckPageTime" />
				<label>This is the next button</label>
				<center><input type="submit" name="browserCheck" value="Next" style="width:100px; height:50px; font-size:20px;" ></center>
			</div>
	
</form>
<script type='text/javascript' src='iterativeFaces_browserCheck_optional.js'></script>


<?php
	} else {
		// Check if browser check was correct
		$rectangleColors = $_SESSION['rectangleColors'];
		$shape = $_SESSION['shape'];
		$whatHappened = $_SESSION['whatHappened'];
		$correctColors = 'yellow/blue';
		$correctShape = 'Square';
		$correctEvent = 'The shape rotated';
		if ( !( ($rectangleColors == $correctColors) && ($shape == $correctShape) && ($whatHappened == $correctEvent) )   ) {
			die('
			<div id="failedBrowserCheck" class="instructions" style="color:red"><center><h2> You are not eligible 
			to complete the HIT as you chose the wrong answers for the browser compatibility check. 
			This means that your browser did not render this initial test accurately,
			and would fail to render the real test as well.
			If you believe you have received this message in error, please contact 
			the experimenter at stefan.uddenberg@yale.edu. 
			Otherwise, please return the HIT. </h2> </center>
			</div>
		');
		} else {

			echo '<div id="passedBrowserCheck" class="instructions" style="color:green"><center><h2>
			Congratulations! You have passed our browser compatibility test.
			Feel free to accept the HIT! </h2> </center>
			</div>';

		}
		
	}

?>






</body>
</html>