<?php
// Change log
// 03-06-2014: Added assortive mechanism. Creates 10 files per race, chooses the file that was modified longest ago (so it keeps the chains as fresh as possible).
//			   Added "Go around the whole circle to move on" instructions.
// Turn on sessions
// 03-07-2014: Changed mechanism such that it always goes with the first file that isn't in use. works perfectly (I hope)
// 03-08-2014: Added on-line exclusion of people with the same worker ID trying to participate.
//			   Started recording the start and end times of the whole experiment.
// 03-19-2014: Added "Hispanic or Latino American" to the list of possibilities, 
//			   but it is a hidden possibility! Now filtering all of that ilk into 
//		       the "hispanic" family.
// 03-26-2014: Switching to 10 members per chain for replication
// 05-09-2014: 
// 07-17-2014: Made changes from stephan's code (in the xp1 folder, *_11)
// 07-30-2014: fixed some small things and added correct exclusions
//==================================================
// How to use this file:
//		There's a summary below and then a detailed report in each section. I will provide 
//		"find" codes for each section that is of interest
//
// I'm going to provide a couple of ways to look at this code by using ctrl/command + F notations for specific sections. 
// Here is a general overview, organized by what comes first in the code to what comes last:
// 	1) Creates the Turk HIT, loads relevant scripts (animation, necessary_functions (local)), does browser check
// 	2) php variable constants created
//		Of note: startingSpeedRatio is the only variable changed across conditions with the file names
// 	3) Initial screen where we parse the login info
//	4) Worker ID logic
//	5) Browser test: animation code located in iterativeFaces_browserCheck-S2.js
//	6) output file creation (after successful browser test)
// 	7) Experiment screen + stage HTML
// 	---::javascript
//	1) initial collision speed calculation + second object speed calculation
//	2) speed array creation (for the wheel) 
//	3) animation (with tween plugin) + wheel rotation animation (annotation cropper)
//	4) processing + recording (choosing the speed) 
//  ---::php
//	1) recording necessary variables from HTML
//	2) confirmation code, file writing
//	3) unlocking file

//GOING THROUGH THE CODE:

/*
I've provided search codes that will make navigating and jumping back and forth through the code easier
php initial constants: 99CTV	
HTML variables: 99HTV
Initial collision Logic: 99SC1
Speed array calculation: 99SAC
Collision animation: 99CAS
Collision time scaling: 99CTS

Experiment collision (seen): 99EXC
Experiment wheel data: 99CWD 
Experiment wheel updating: 99CWU
Rotate annotation cropper 
(for when you move around the wheel): RTAC

Post experiment php variable passing: 99EAP
Adding data to CSV: 99CVA

You can search console.log to see useful things to look at (there are only two really)

You can also search S:: to see offhand comments I made
throughout the genesis of the code, a few of them might not
be relevant any more since they persisted throughout
multiple revisions. 

*/ 
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
	<title>Mechanical Turk HIT</title>

	<!-- Load External js scripts -->
	<!-- NOTE: TweenMax is necessary, but we are using the web version source. A local copy could be downloaded if we are seeing any issues --> 
	<script type="text/javascript" src="http://mbostock.github.com/d3/d3.js?1.29.1"></script>
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type='text/javascript' src="necessary_functions.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/1.11.8/TweenMax.min.js"></script><!-- S::library for animation --> 
	<!-- CSS styling goes in a separate document -->
	<link href="iterativeFaces-S2.css" rel="stylesheet" type="text/css">
	
</head>
<body>

<?php

// % -------------------------------------------------------------------------
// Check if a compatible browser is being used.
$userAgent = $_SERVER["HTTP_USER_AGENT"];      // Get user-agent of browser

$safariorchrome = strpos($userAgent, 'Safari') ? true : false;     // Browser is either Safari or Chrome (since Chrome User-Agent includes the word 'Safari')
$chrome = strpos($userAgent, 'Chrome') ? true : false;             // Browser is Chrome
$firefox = strpos($userAgent, 'Firefox') ? true : false;             // Browser is Firefox
$ie = strpos($userAgent, 'MSIE') ? true : false;             // Browser is IE
if($safariorchrome == true AND $chrome == false){ $safari = true; }     // Browser should be Safari, because there is no 'Chrome' in the User-Agent

//S:: If you want to exclude chrome, comment out this line. 
//if($chrome) {die('Cannot use Chrome! Please use Firefox or Internet Explorer instead!');} //Sinjihn: why doesn't chrome work? 


//% -------------------------------------------------------------------------
// Stop mobile users from accessing the website
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
die('Cannot use mobile devices! Please use a laptop or desktop computer.');

// % -------------------------------------------------------------------------
// Declare constant variables
//:::::::::99CTV::::::::::::\\

$noiseOn = 0;
$debugging = 0; //S:: remember to turn this off
$debuggingErrors = 0;
$chainLength = 10; // number of individuals in a single chain, used to be 30.
$defaultStartingFace = 31; //just assures the dummy picks the median

//This needs to be changed if you want to change the condition (1/3) or (3) 
$defaultSpeedRatio = 1; //S:: for version 1 of the script, this needs to be changed

$defaultO1 = 0.9; //default speed for 1st object, useful for first participant in chain
$defaultO2 = 0.9;  //default speed for second object

$filePrefix = 'SR3_1TO1_CAUSAL_data_';
$fileExtension = '.csv';
$lockedFileList = 'SR1TO1_CAUSAL_files_in_use.csv';
$workerIDList = 'past_participant_list.csv';

//:::::::::99CTV::::::::::::\\

//This info is useful all throughout the code, the semantics of it
//Random face = the random choice on the wheel
//Chosen face = the face that they chose
//First face = the face that the last person picked from the chain
//Chose face angle = the angle on the circle 

$columnHeadings = array('Worker_ID', 'Chain_Number', 'Participant_Number', 'Nationality', 'IP', 'Date', 'Gender', 'Age', 
						'First_Face', 'Random_Start_Speed '/*S:: added*/, 'First_Object', 'Second_Object', 'Chosen_Face', 'Speed_Ratio_Chosen', 'Chosen_Face_Angle', 'RT', 
						'Browser_Check_Response', 'Browser_Name', 'Confirmation_Code', 'Comments', 'What_Tested', 
						'Demographics_Page_Time', 'Browser_Check_Page_Time','Face_Page_Time', 
						'Face_Instructions_Time', 'Random_Offset', 'Random_Face_Angle', 'Exp_Start_Time',
						 'Exp_End_Time', 'Attention_Check_Number', 'Attention_Check_Response', 
						 'Screen_Width', 'Screen_Height');
$startingData = array('dummy', 1, '0', 'Afghanistan', '127.0.0.1', 'today', 'male', '18', 
					  $defaultStartingFace, $defaultStartingFace, $defaultO1, $defaultO2, $defaultStartingFace, $defaultSpeedRatio, 0, 1, 
					  'Browser_Check_Response', 'Browser_Name', 'Confirmation_Code', 'Comments', 
					  'What_Tested', 'Demographics_Page_Time', 'Browser_Check_Page_Time', 
					  'Face_Page_Time', 'Face_Instructions_Time', 'Random_Offset', 
					  'Random_Face_Angle', 'Exp_Start_Time', 'Exp_End_Time', 
					  'Attention_Check_Number', 'Attention_Check_Response', 'Screen_Width',
					  'Screen_Height');
$workerIDColumn = 1-1;
$chainColumn = 2-1;
$paritipantColumn = 3-1;
$countryColumn = 4-1;
$ipColumn = 5-1;
$dateColumn = 6-1;
$genderColumn = 7-1;
$ageColumn = 8-1;
$raceColumn = 9-1; //new addition
$startingFaceColumn = 10-1;
$startingFaceColumn = 9-1;
$randomFaceColumn = 10-1; // since we have to count from 0
$chosenFaceColumn = 13-1; // since we have to count from 0
$speedRatioColumn = 14-1; //S:: new addition
$objectColumn1 = 11-1;
$objectColumn2 = 12-1; 
$chosenFaceAngleColumn = 15-1; // since we have to count from 0
$RTColumn = 16-1;
$browserCheckColumn = 17-1;
$browserColumn = 18-1;
$confirmationCodeColumn = 19-1;
$commentsColumn = 20-1;
$surveyThoughtsColumn = 21-1;

$delimiter = ',';
$numDuplicateFiles = 10;

// % -------------------------------------------------------------------------
// Print errors if debugging.
if($debuggingErrors){
	ini_set("display_errors","1");
	ERROR_REPORTING(E_ALL);

	// Sets required field error (DO NOT COMMENT OUT)
	$error= NULL; 
}


// % -------------------------------------------------------------------------
// First time loaded, call form for demographic info

if (($_SERVER['REQUEST_METHOD'] == 'GET') && (! isset($_POST['demographics']))) {
	session_unset();   // not sure what this does in this context
	$stage = 1; #This dictates which "page" loads
	$expStartTime = date('H:i:s');
	
	// Get worker ID and the like

	if(isset($_REQUEST['workerId'])){
		$workerID = $_REQUEST['workerId'];

	} 
	else {
		$workerID = 'A1BGDXZ95IQ3W';

	}
	
	// display form
	?> <!-- SS:: end PHP --> 
	
	<div type="hidden" class="output">
		<p id="totalPageTime">  </p>
	</div>

	<center><div id="consentPreamble" class="instructions">
		<h3>In order for us to conduct this test online, we need to include the standard consent form below.</h3>
	</div></center>
	<div id="consentForm" class="instructions consent-box">
		<center><h1> Informed Consent Form</h1></center>
		<p id="consentInstructions">
		These HITs are part of a research study being conducted by researchers at Yale University about 
		how people make decisions. The completed work will be stored at Yale in our laboratory and will be confidential. 
		The response records will be kept for up to three years afterwards to aid in data analysis and interpretation. 
		Participation in this study is voluntary, and you may withdraw at any time, but you will only be 
		compensated for the questions you answer in accordance with the policies of Amazon Mechanical Turk and the terms of this HIT. <br /> <br />
		 
		If you choose to answer all the questions it should take approximately 5-10 minutes.  <br /> <br />
		
		There are no known risks and you will not receive any benefit for participating in this study. <br /> <br />
		 
		Contact Information: 
		If you have any questions or concerns about any aspect of the study,  you 
		can reach us via the "contact this requester" link. Alternately, you can 
		contact the Yale University Human Subjects committee directly:  <br /> <br />
		 
		If you have any questions or concerns regarding this experiment, you may contact us here 
		at the lab. If you have general questions about your rights as a research participant, you 
		may contact the Yale University Human Subjects Committee. This study is exempt
		under 45 CFR 46.101(b)(2).  <br /> <br />
		
		Yale Univ. Human Subjects Com.<br />   
		55 College St. (P.O. Box 208010)<br />
		New Haven, CT 06520-8010<br />   
		203-785-4688<br />   
		human.subjects@yale.edu <br /> 
		Additional information is available at http://www.yale.edu/hrpp/participants/index.html <br /> <br /> 
		 
		<b>By selecting "Yes" below you indicate you have read and agree to this consent statement and are over 18 years of age.</b>
		<br /> <br />   
		 
		</p>
	</div>

	 <br /> <br />

	<div class="survey">
		
		<form name="demographicsForm" id="demographicsForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

			<label id="whatWorkerID">What is your M-TURK Worker ID? (e.g. A1BGDXZ95IQ3W) </label>
				<input type="text" name="workerID" id="workerID" size="30" style="height:1em;">
			
			<div class="survey combo">
				<label for="race">What is your race?</label>
				<input type="text" name="race" id="race" placeholder="Select or type your race" value="Select or type your race">
			<ul>
				<li>White</li>
				<li>Black or African American</li>
				<li>Asian or Pacific Islander</li>
				<li>East Indian</li>
				<li>Native American or Alaska Native</li>
				<!-- <li>Hispanic or Latino American</li> -->
				<li>Multiracial</li>
				<li>Free response</li>
			</ul>
			</div>

			<script type="text/javascript">
				new combo('race', '#F8F8F8', '#D0D0D0');
			</script>
			
			<label id="whatGender">What is your gender? </label>

				<select id="gender" name="gender">
					<option value="Select here" selected="selected">Select here</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
				</select>

			<label id="whatAge">What is your age? </label>
				<select name="age" id="age">
				 <?php 
				 	$minAge = 18;
				 	$maxAge = 90;
				 	for ($i=$minAge; $i <= $maxAge; $i++) { ?>
				 		<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
				 	<?php } ?>
				</select>

			<label id="whatNation">What is your nationality?</label>
					<select id="country" name="country">
						<option value="Afghanistan">Afghanistan</option>
						<option value="Åland Islands">Åland Islands</option>
						<option value="Albania">Albania</option>
						<option value="Algeria">Algeria</option>
						<option value="American Samoa">American Samoa</option>
						<option value="Andorra">Andorra</option>
						<option value="Angola">Angola</option>
						<option value="Anguilla">Anguilla</option>
						<option value="Antarctica">Antarctica</option>
						<option value="Antigua and Barbuda">Antigua and Barbuda</option>
						<option value="Argentina">Argentina</option>
						<option value="Armenia">Armenia</option>
						<option value="Aruba">Aruba</option>
						<option value="Australia">Australia</option>
						<option value="Austria">Austria</option>
						<option value="Azerbaijan">Azerbaijan</option>
						<option value="Bahamas">Bahamas</option>
						<option value="Bahrain">Bahrain</option>
						<option value="Bangladesh">Bangladesh</option>
						<option value="Barbados">Barbados</option>
						<option value="Belarus">Belarus</option>
						<option value="Belgium">Belgium</option>
						<option value="Belize">Belize</option>
						<option value="Benin">Benin</option>
						<option value="Bermuda">Bermuda</option>
						<option value="Bhutan">Bhutan</option>
						<option value="Bolivia">Bolivia</option>
						<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
						<option value="Botswana">Botswana</option>
						<option value="Bouvet Island">Bouvet Island</option>
						<option value="Brazil">Brazil</option>
						<option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
						<option value="Brunei Darussalam">Brunei Darussalam</option>
						<option value="Bulgaria">Bulgaria</option>
						<option value="Burkina Faso">Burkina Faso</option>
						<option value="Burundi">Burundi</option>
						<option value="Cambodia">Cambodia</option>
						<option value="Cameroon">Cameroon</option>
						<option value="Canada">Canada</option>
						<option value="Cape Verde">Cape Verde</option>
						<option value="Cayman Islands">Cayman Islands</option>
						<option value="Central African Republic">Central African Republic</option>
						<option value="Chad">Chad</option>
						<option value="Chile">Chile</option>
						<option value="China">China</option>
						<option value="Christmas Island">Christmas Island</option>
						<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
						<option value="Colombia">Colombia</option>
						<option value="Comoros">Comoros</option>
						<option value="Congo">Congo</option>
						<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
						<option value="Cook Islands">Cook Islands</option>
						<option value="Costa Rica">Costa Rica</option>
						<option value="Cote D'ivoire">Cote D'ivoire</option>
						<option value="Croatia">Croatia</option>
						<option value="Cuba">Cuba</option>
						<option value="Cyprus">Cyprus</option>
						<option value="Czech Republic">Czech Republic</option>
						<option value="Denmark">Denmark</option>
						<option value="Djibouti">Djibouti</option>
						<option value="Dominica">Dominica</option>
						<option value="Dominican Republic">Dominican Republic</option>
						<option value="Ecuador">Ecuador</option>
						<option value="Egypt">Egypt</option>
						<option value="El Salvador">El Salvador</option>
						<option value="Equatorial Guinea">Equatorial Guinea</option>
						<option value="Eritrea">Eritrea</option>
						<option value="Estonia">Estonia</option>
						<option value="Ethiopia">Ethiopia</option>
						<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
						<option value="Faroe Islands">Faroe Islands</option>
						<option value="Fiji">Fiji</option>
						<option value="Finland">Finland</option>
						<option value="France">France</option>
						<option value="French Guiana">French Guiana</option>
						<option value="French Polynesia">French Polynesia</option>
						<option value="French Southern Territories">French Southern Territories</option>
						<option value="Gabon">Gabon</option>
						<option value="Gambia">Gambia</option>
						<option value="Georgia">Georgia</option>
						<option value="Germany">Germany</option>
						<option value="Ghana">Ghana</option>
						<option value="Gibraltar">Gibraltar</option>
						<option value="Greece">Greece</option>
						<option value="Greenland">Greenland</option>
						<option value="Grenada">Grenada</option>
						<option value="Guadeloupe">Guadeloupe</option>
						<option value="Guam">Guam</option>
						<option value="Guatemala">Guatemala</option>
						<option value="Guernsey">Guernsey</option>
						<option value="Guinea">Guinea</option>
						<option value="Guinea-bissau">Guinea-bissau</option>
						<option value="Guyana">Guyana</option>
						<option value="Haiti">Haiti</option>
						<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
						<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
						<option value="Honduras">Honduras</option>
						<option value="Hong Kong">Hong Kong</option>
						<option value="Hungary">Hungary</option>
						<option value="Iceland">Iceland</option>
						<option value="India">India</option>
						<option value="Indonesia">Indonesia</option>
						<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
						<option value="Iraq">Iraq</option>
						<option value="Ireland">Ireland</option>
						<option value="Isle of Man">Isle of Man</option>
						<option value="Israel">Israel</option>
						<option value="Italy">Italy</option>
						<option value="Jamaica">Jamaica</option>
						<option value="Japan">Japan</option>
						<option value="Jersey">Jersey</option>
						<option value="Jordan">Jordan</option>
						<option value="Kazakhstan">Kazakhstan</option>
						<option value="Kenya">Kenya</option>
						<option value="Kiribati">Kiribati</option>
						<option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
						<option value="Korea, Republic of">Korea, Republic of</option>
						<option value="Kuwait">Kuwait</option>
						<option value="Kyrgyzstan">Kyrgyzstan</option>
						<option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
						<option value="Latvia">Latvia</option>
						<option value="Lebanon">Lebanon</option>
						<option value="Lesotho">Lesotho</option>
						<option value="Liberia">Liberia</option>
						<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
						<option value="Liechtenstein">Liechtenstein</option>
						<option value="Lithuania">Lithuania</option>
						<option value="Luxembourg">Luxembourg</option>
						<option value="Macao">Macao</option>
						<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
						<option value="Madagascar">Madagascar</option>
						<option value="Malawi">Malawi</option>
						<option value="Malaysia">Malaysia</option>
						<option value="Maldives">Maldives</option>
						<option value="Mali">Mali</option>
						<option value="Malta">Malta</option>
						<option value="Marshall Islands">Marshall Islands</option>
						<option value="Martinique">Martinique</option>
						<option value="Mauritania">Mauritania</option>
						<option value="Mauritius">Mauritius</option>
						<option value="Mayotte">Mayotte</option>
						<option value="Mexico">Mexico</option>
						<option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
						<option value="Moldova, Republic of">Moldova, Republic of</option>
						<option value="Monaco">Monaco</option>
						<option value="Mongolia">Mongolia</option>
						<option value="Montenegro">Montenegro</option>
						<option value="Montserrat">Montserrat</option>
						<option value="Morocco">Morocco</option>
						<option value="Mozambique">Mozambique</option>
						<option value="Myanmar">Myanmar</option>
						<option value="Namibia">Namibia</option>
						<option value="Nauru">Nauru</option>
						<option value="Nepal">Nepal</option>
						<option value="Netherlands">Netherlands</option>
						<option value="Netherlands Antilles">Netherlands Antilles</option>
						<option value="New Caledonia">New Caledonia</option>
						<option value="New Zealand">New Zealand</option>
						<option value="Nicaragua">Nicaragua</option>
						<option value="Niger">Niger</option>
						<option value="Nigeria">Nigeria</option>
						<option value="Niue">Niue</option>
						<option value="Norfolk Island">Norfolk Island</option>
						<option value="Northern Mariana Islands">Northern Mariana Islands</option>
						<option value="Norway">Norway</option>
						<option value="Oman">Oman</option>
						<option value="Pakistan">Pakistan</option>
						<option value="Palau">Palau</option>
						<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
						<option value="Panama">Panama</option>
						<option value="Papua New Guinea">Papua New Guinea</option>
						<option value="Paraguay">Paraguay</option>
						<option value="Peru">Peru</option>
						<option value="Philippines">Philippines</option>
						<option value="Pitcairn">Pitcairn</option>
						<option value="Poland">Poland</option>
						<option value="Portugal">Portugal</option>
						<option value="Puerto Rico">Puerto Rico</option>
						<option value="Qatar">Qatar</option>
						<option value="Reunion">Reunion</option>
						<option value="Romania">Romania</option>
						<option value="Russian Federation">Russian Federation</option>
						<option value="Rwanda">Rwanda</option>
						<option value="Saint Helena">Saint Helena</option>
						<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
						<option value="Saint Lucia">Saint Lucia</option>
						<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
						<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
						<option value="Samoa">Samoa</option>
						<option value="San Marino">San Marino</option>
						<option value="Sao Tome and Principe">Sao Tome and Principe</option>
						<option value="Saudi Arabia">Saudi Arabia</option>
						<option value="Senegal">Senegal</option>
						<option value="Serbia">Serbia</option>
						<option value="Seychelles">Seychelles</option>
						<option value="Sierra Leone">Sierra Leone</option>
						<option value="Singapore">Singapore</option>
						<option value="Slovakia">Slovakia</option>
						<option value="Slovenia">Slovenia</option>
						<option value="Solomon Islands">Solomon Islands</option>
						<option value="Somalia">Somalia</option>
						<option value="South Africa">South Africa</option>
						<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
						<option value="Spain">Spain</option>
						<option value="Sri Lanka">Sri Lanka</option>
						<option value="Sudan">Sudan</option>
						<option value="Suriname">Suriname</option>
						<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
						<option value="Swaziland">Swaziland</option>
						<option value="Sweden">Sweden</option>
						<option value="Switzerland">Switzerland</option>
						<option value="Syrian Arab Republic">Syrian Arab Republic</option>
						<option value="Taiwan, Province of China">Taiwan, Province of China</option>
						<option value="Tajikistan">Tajikistan</option>
						<option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
						<option value="Thailand">Thailand</option>
						<option value="Timor-leste">Timor-leste</option>
						<option value="Togo">Togo</option>
						<option value="Tokelau">Tokelau</option>
						<option value="Tonga">Tonga</option>
						<option value="Trinidad and Tobago">Trinidad and Tobago</option>
						<option value="Tunisia">Tunisia</option>
						<option value="Turkey">Turkey</option>
						<option value="Turkmenistan">Turkmenistan</option>
						<option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
						<option value="Tuvalu">Tuvalu</option>
						<option value="Uganda">Uganda</option>
						<option value="Ukraine">Ukraine</option>
						<option value="United Arab Emirates">United Arab Emirates</option>
						<option value="United Kingdom">United Kingdom</option>
						<option value="United States" selected="selected">United States</option>
						<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
						<option value="Uruguay">Uruguay</option>
						<option value="Uzbekistan">Uzbekistan</option>
						<option value="Vanuatu">Vanuatu</option>
						<option value="Venezuela">Venezuela</option>
						<option value="Viet Nam">Viet Nam</option>
						<option value="Virgin Islands, British">Virgin Islands, British</option>
						<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
						<option value="Wallis and Futuna">Wallis and Futuna</option>
						<option value="Western Sahara">Western Sahara</option>
						<option value="Yemen">Yemen</option>
						<option value="Zambia">Zambia</option>
						<option value="Zimbabwe">Zimbabwe</option>
					</select>

	<input type='hidden' name='stage' value='<?php echo $stage + 1 ?>'/>
	<input type='hidden' name='demographicsPageTime' id="demographicsPageTime" />
	<input type='hidden' name='expStartTime' id="expStartTime" value="<?php echo $expStartTime; ?>" />

	<center><h3 style="display: block; float: none; clear: both; width: 50%; text-align: justify; vertical-align:middle">Once you enter your Worker ID and click "Consent/Next", you will have to complete the whole survey in one (short) sitting. Do not use the back
		or refresh buttons on your browser at any point, and do not attempt to reload this page, as you may be locked out. </h3></center>
	<div class="buttonHolder">
		<label style="visibility:hidden;  display: block; float: none; clear: left; width: 50%; text-align: left;">This is the next button</label>
		<center><input type="submit" name="demographics" value="Consent/Next" style="width:150px; display:block; float:none; text-align:center; margin-top:20px; vertical-align:middle;"></center>
	</div>
	
</form>
</div>

<?php
}


// % -------------------------------------------------------------------------
// Now that demographics have been set, call the relevant file.
else if (($_SERVER['REQUEST_METHOD'] == 'GET')|| (! isset($_SESSION['browserCheck']))) { 
	$stage = $_SESSION['stage'] + 1;

	// Check if subject has done this study before with their worker ID.

	// Create worker ID list if it doesn't exist
	$workerID = trim($_SESSION['workerID']);
	if (!file_exists($workerIDList)) {
		$fl = fopen($workerIDList, 'w+');
		fputcsv($fl,array('A0')); 
		fclose($fl);
		chmod($workerIDList, 0640);
	}
	$excludedWorkerIDs = readCSV($workerIDList);

	// Stop them if they just pasted the URL
	if   ( (stristr($workerID, 'http:')) || (stristr($workerID, '@')) || (stristr($workerID, 'https:'))
		|| (stristr($workerID, 'www.')) || (stristr($workerID, '.net')) || stristr($workerID, '.') ) {
		die('
			<div id="notEligibleURL" class="instructions" style="color:red"><center><h2> Oops! It looks like you pasted
			something that is not a Worker ID into the Worker ID box. Your worker ID is a series of random
			numbers and letters starting with A (e.g. A1BGDXZ95IQ3W). It is not the study URL,
			nor is it your email address! To try again with your real worker ID, please re-enter the study 
			URL into your address bar, as the refresh button likely will not work at this point.
			If you believe you have received this message in
			error, please contact the experimenter at jonathan.kominsky@yale.edu. Otherwise, please return the HIT. </h2> </center>
			</div>
		');

	} 

	foreach ($excludedWorkerIDs as $fields) {
		if ( in_array_case_insensitive($workerID, $fields) || (empty($workerID)) ) {
			die('
			<div id="notEligible" class="instructions" style="color:red"><center><h2> You are not eligible 
			to take the test as you have already done it (or you left the worker ID question blank)! If you believe you have received this message in
			error, please contact the experimenter at jonathan.kominsky@yale.edu. Otherwise, please return the HIT. </h2> </center>
			</div>
			');
		}
	}	

	// since we got to this point, add the worker ID to the list of excluded ones.
	
	
	
	
	
	
	
	$workerIDarray[] = array($workerID);
	$fw = fopen($workerIDList, 'a');

	foreach ($workerIDarray as $fields) {
		fputcsv($fw, $fields);
	}
		
	fclose($fw); 

	?>
	<div type="hidden" class="output">
		<p id="totalPageTime">  </p>
	</div>
	<script type="text/javascript">
			// getSecs();
	</script>
	<center> <div id="container">
	<p id="instructions1"style="text-align:justify; margin: 0 auto; width: 60em;">
		This is an initial test to make sure your browser can handle the real test. If you were able to correctly perform the 
		optional browser check before accepting the HIT, this should be a breeze. Please move your mouse around the circle
		at least once, and click on the rectangle at the edge of the ring to see its possible colors. 
		<br /> <br />
		<b>Note that failure to answer all three questions below correctly
		in this section will lock you out of the survey and will therefore require 
		that you return the HIT.</b>
		<br /> <br />
		Now, please answer the questions below.  </p>
		<!-- S:: ALL INFORMATION FOR DIVS --> 
	<div id ="dynamic-container">
		<div id ="marker"></div>
		<div id ="innerCircle"> <!-- <p id="outputNumber"> <?php echo $outputNumber ?>  </p> -->
			<div class="circle" id="kineticBox"> </div>
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
					<label id="whatHappenedLabel">What is happening in the middle of the ring?</label>
						<select id="whatHappened" name="whatHappened">
							<option value="Nothing is happening because there was no shape" selected="selected">Nothing is happening because there was no shape</option>
							<option value="Nothing happened but there was a shape">Nothing happened but there was a shape</option>
							<option value="The shape is changing size">The shape is changing size smoothly</option>
							<option value="The shape changed color">The shape changed color</option>
							<option value="The shape changed from one shape to another">The shape changed from one shape to another</option>
							<option value="The shape is changing size choppy">The shape is changing size but looks choppy and skips</option>
							<option value="The shape rotated">The shape rotated</option>
							
						</select>
						
						
		</div><br />

	
		<div class="surveyButtonHolder">
			<input type='hidden' name='browserCheckPageTime' id="browserCheckPageTime" />
			<label>This is the next button</label>
			<center><input type="submit" name="browserCheck" value="Next" style="width:100px; height:50px; font-size:20px;" ></center>
		</div>
	
</form>

<!-- Main js script goes in a separate document -->
<script type='text/javascript' src='iterativeFaces_browserCheck-S2.js'></script>

<?php


}
else if (($_SERVER['REQUEST_METHOD'] == 'GET')|| (! isset($_SESSION['Saving']))) {

	// Check if browser check was correct
	$rectangleColors = $_SESSION['rectangleColors'];
	$shape = $_SESSION['shape'];
	$whatHappened = $_SESSION['whatHappened']; //S:: make sure that this gets recorded
	$correctColors = 'red/green';
	$correctShape = 'Square';
	$correctEvent = 'The shape is changing size';
	$correctEvent2 = 'The shape is changing size choppy'; 

	if ( !( ($rectangleColors == $correctColors) && ($shape == $correctShape) && (($whatHappened == $correctEvent) || ($whatHappened == $correctEvent2)))) {
		die('
			<div id="failedBrowserCheck" class="instructions" style="color:red"><center><h2> You are not eligible 
			to complete this HIT as you chose the wrong answers for the browser compatibility check. 
			This means that your browser did not render this initial test accurately,
			and would fail to render the real test as well.
			If you believe you have received this message in error, please contact 
			the experimenter at jonathan.kominsky@yale.edu. 
			Otherwise, please return the HIT. </h2> </center>
			</div>
		');
	}

	//They're ok, so 
	$race = trim($_SESSION['race']);
	if (empty($race)) {
		$race = 'None';
	}
	$origRace = $race;
	$workerID = $_SESSION['workerID'];
	$country = $_SESSION['country'];
	$gender = $_SESSION['gender'];
	$age = $_SESSION['age'];	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date("m.d.y");
	$stage =  (int) $_SESSION['stage'] + 1;	

	$_SESSION['ip'] = $ip;
	$_SESSION['date'] = $date;
	$_SESSION['stage'] = $stage;
	$_SESSION['origRace'] = $origRace;
	
	// Pare down race info, which no longerm matters since we use the term "universal" 
	if(stristr($race,'White')){
		$race = "White";
	}

	elseif(stristr($race,'Black')|| stristr($race,'African') ){
		$race = "Black";
	}

	elseif(stristr($race,'Asian')){
		$race = "Asian";
	}

	elseif(stristr($race,'Indian')){
		$race = "Indian";
	}
	elseif(stristr($race,'Native')){
		$race = "Native";
	}
	
	elseif(stristr($race,'Multiracial')){
		$race = "Multiracial";
	}

	elseif(stristr($race,'Hispanic')|| stristr($race,'Latin') || stristr($race,'Mexican')){
		$race = "Hispanic";
	}
	$_SESSION['race'] = $race;

	if ($debugging) {
		echo "<br /> Worker ID is $workerID";
		echo "<br /> race is $race";
		echo "<br /> country is $country";
		echo "<br /> gender is $gender";
		echo "<br /> age is $age";
		echo "<br /> IP address is $ip";
		echo "<br /> date is $date";
	}
	//S::race variable is $race



	// Choose relevant CSV file

	$csvRace = 'universal'; //how the files are getting lumped together
	$familyCSV = $filePrefix . $csvRace . '_01'. $fileExtension;  //took out the race extension, using universal chains
	
	// Create output file if it doesn't exist
	if (!file_exists($familyCSV)) {
		$chain = 1;
		$participant = 1;
		$startingFace = $defaultStartingFace;
		$startingRatio = $defaultSpeedRatio; //S::speedRatio
		$data = array($columnHeadings);	//fills it out with dummy
		$data[] = $startingData;

		$fh = fopen($familyCSV, 'w');
		foreach ($data as $fields) {
			fputcsv($fh, $fields);
		}
		fclose($fh); 
		chmod($familyCSV, 0640); 
	}

	// Create locked file list if it doesn't exist
	if (!file_exists($lockedFileList)) {
		$fl = fopen($lockedFileList, 'w+');
		fputcsv($fl,array('blank_line', strtotime("+100 week"))); // tried strtotime("8 December 2050") as well, but that didn't work. No matter!
		fclose($fl);
		chmod($lockedFileList, 0640); 
	}


	// Check if chosen file is currently in locked file list. If so, choose new file. If new file doesn't exist, create it.
	$fileNum = 1;
	$lockedFiles = readCSV($lockedFileList);
	while (list($key, $fields) = each($lockedFiles)) {
	
		if (in_array($familyCSV, $fields)) {
			$fileNum++;
			if ($fileNum > 50) {break;}
			$paddedFileNum = sprintf("%02d", $fileNum);
			$familyCSV = $filePrefix . $csvRace . '_'. $paddedFileNum . $fileExtension;
			//$familyCSV = $filePrefix . 'Multiracial' . '_'. $paddedFileNum . $fileExtension; //S::

			// Create familyCSV if it doesn't exist
			if (!file_exists($familyCSV)) {
				$chain = 1;
				$participant = 1;
				$startingFace = $defaultStartingFace;
				$startingRatio = $defaultSpeedRatio; 
				$data = array($columnHeadings);	
				$data[] = $startingData;

				$fh = fopen($familyCSV, 'w');
				foreach ($data as $fields) {
					fputcsv($fh, $fields);
				}
				
				fclose($fh); 
				chmod($familyCSV, 0640);

			}
			reset($lockedFiles);
		}
	}

	if ($debugging) {
		echo "<br /> familyCSV is $familyCSV";
	}
	
	// Add the file to the locked file list
	$lockedFiles[] = array($familyCSV,strtotime('now'));
	$fl = fopen($lockedFileList, 'r+');
	foreach ($lockedFiles as $fields) {
		fputcsv($fl, $fields);
	}
		
	fclose($fl); 

	$data = readCSV($familyCSV);

	$prevTrialData = end($data);
	$currentIndex = sizeof($data) - 1;
	$startingFace = $prevTrialData[$chosenFaceColumn];
	$startingRatio = $prevTrialData[$speedRatioColumn]; //S:: 
	while (empty($startingFace)) {
		$currentIndex--;
		$prevTrialData = $data[$currentIndex];
		$startingFace = $prevTrialData[$chosenFaceColumn];
		$startingRatio = $prevTrialData[$speedRatioColumn]; //S::
	}
	

	$chain = $prevTrialData[$chainColumn];
	$participant = $prevTrialData[$paritipantColumn] + 1;
	if ($participant > $chainLength) {
		$participant = 1;
		$chain = $chain+1;
		$startingFace = $defaultStartingFace;
		$startingRatio = $defaultSpeedRatio; 
	}


// // since we got to this point, add the worker ID to the list of excluded ones.
// $workerID = trim($_SESSION['workerID']);
// $excludedWorkerIDs = readCSV($workerIDList);
// $excludedWorkerIDs[] = array($workerID);

// $fw = fopen($workerIDList, 'w');
// foreach ($excludedWorkerIDs as $fields) {
// 		fputcsv($fw, $fields);
// 	}
	
// fclose($fw); 

//This stuff is mostly for debugging, if you turn debugging on,
//you'll see this info posed at the top of the page
$_SESSION['startingFace'] = $startingFace;
$_SESSION['startingRatio'] = $startingRatio; 
$_SESSION['chain'] = $chain;
$_SESSION['participant'] = $participant;
$_SESSION['familyCSV'] = $familyCSV;
$_SESSION['csvRace'] = $csvRace;
?>
<center> <div id="container">
	<p id="instructions1" style="text-align:justify; margin: 0 auto; width: 60em;">

	Thanks for participating in our quick survey! Before we continue, please make your window as large as you can.<br /><br />

	We're going to show you a movie where a blue object makes contact with a red object.  Your job is to remember the speed of the red object that you saw as best you can, and then we're going to give you a memory test.<br /><br />

	Because the event will be presented very quickly, it's important that you are paying close attention.  So when you start, you'll first see the words "Pay attention", followed by a countdown from 5 to 0.  During the countdown, one of the numbers will be colored red.  You'll have to remember which number that was, because we'll ask you after the survey is over.  Once the countdown reaches 0, the event will appear.<br /><br />

	After the event disappears, you're going to have to reproduce the speed of the second, red object, using the mouse: Objects will appear in the center of a circle, and as you move your cursor around the circle, the objects will change speeds.  Your job is to move the cursor around until you think the speed depicted in the circle is the same one that you saw initially.  At that point, you can click the mouse to stop the speed from changing, and then hit the "Next" button to move on.  (If you accidentally click the mouse before you've made up your mind, you can just click on the marker to start the changing again.  This part of the survey will end as soon as you click "Next".)
		</p><br />
	<p id="instructions2">Please make sure you can see the entire circle in your browser window, then press the "Start" button when you are ready to begin.</p>
	<p id="READ_THIS" class="instructions" style="color:red"><center><h2>MAKE SURE TO READ THE INSTRUCTIONS ABOVE BEFORE CLICKING "START"!</h2></center>


	<p id="loading">Loading...</p>
	<div class="buttonHolder"><button id='start'>START</button></div>
	<div id ="dynamic-container">
		<div id ="marker"></div>
		<div id ="innerCircle"> <!-- <p id="outputNumber"> <?php echo $outputNumber ?>  </p> -->
			<!--S::<img id="face" /> -->
			<div id="stage1">
				<div class = "box" id="object1c"></div>
				<div class = "box" id="object2c"></div>   
			</div>
			<div id="stageS"><!-- the stage with the moving objects --> 
				<div class = "box2" id="object1"></div>
				<div class = "box2" id="object2"></div>
				<div class = "box2" id="object3"></div>
				<div class = "box2" id="object4"></div>
				<div class = "box2" id="object5"></div>    
			</div>
			<div id = "countdown"> <!--<?php echo $countdown ?> --></div> </div>
		</div>                
	</div>   </center> 
	<center><div><p id="goAround" style="visibility:hidden;"> Go around the whole circle to move on. </p></div>
	<center><div class="buttonHolder"><button id="next"> Next </button></div>
	<br /> <br />
	<form name="faceChoice" id="faceChoice" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<center>
	

		<div class="survey">
			<label id="whichCountdownNumber">Which countdown number was red?</label> 
				<input type="number" name="attentionCheckResponse" maxlength="1" style="width:30px; height:15px; font-size:15px; text-align:center;"><br />
			<label id="issues">If you had any technical issues while performing the experiment, please tell us about them: </label>
			 	<input type="text" name="comments" size="40" style="width:300px; height:17px; font-size:15px;"><br />
			<label id="whatTesting">What do you think we were testing in this survey?</label>
				<input type="text" name="surveyThoughts" size="40" style="width:300px; height:17px; font-size:15px;"><br />
		</div><br />
		
		<div class="buttonHolder">
			<label style="visibility:hidden;  display: block; float: none; clear: left; width: 50%; text-align: left;">This is the next button</label>
			<input type="submit" name="Saving" value="Submit" style="text-align:center;">
		</div>

		<!-- :::::::99HTV::::::::: -->
		<!-- These variables are read by the php later to get exported to the csv. what's important
		is that the ones that are in the class output are the ones updated by the animation wheel,
		and then the variables that aren't in the class are updated with those variables almost at
		the very end of the code. I have no idea why it is done this way, but if I try to generalize
		it it doensn't work. So you'll see some equivalents of variables ("chosenRatio" + "speedRatio") 
		for example. 
		
		-->
		<input type='hidden' name='stage' value='<?php echo $stage + 1; ?>'/>
		<input type='hidden' name='chosenFaceAngle' id="chosenFaceAngle" />
		<input type='hidden' name='chosenRatio' id="chosenRatio"/>
		<input type='hidden' name='absO1' id="absO1"/>
		<input type='hidden' name='absO2' id="absO2"/>
		<input type='hidden' name='RT' id="RT" />
		<input type='hidden' name='attentionCheckNumber' id="attentionCheckNumber" />
		<input type='hidden' name='randomFaceInput' id="randomFaceInput" />
		<input type='hidden' name='randomFaceRawSpeed' id="randomFaceRawSpeed" />
		<input type='hidden' name='finalFace' id="finalFace" />
		<input type='hidden' name='faceChoicePageTime' id="faceChoicePageTime" />
		<input type='hidden' name='faceChoiceInstructionsTime' id="faceChoiceInstructionsTime" />
		<input type='hidden' name='randomOffset' id="randomOffset" />
		<input type='hidden' name='randomFaceAngle' id="randomFaceAngle" />
		<input type='hidden' name='screenWidth' id="screenWidth" />
		<input type='hidden' name='screenHeight' id="screenHeight" />
		<div type="hidden" class="output">
			<!-- these are a little different from the ones above, they are output vars --> 
			<p id="outputNumber"> </p> 
			<p id="degNumber"> </p>
			<p id="speedRatio"></p>
			<p id="absObj1"></p>
			<p id="absObj2"></p>
			<p id="randomRawSpeed"></p><!-- S:: the raw speed of the face --> 
			<p id="outputNumber2"> </p>
			<p id="xcoordinate"> </p>
			<p id="ycoordinate"> </p>
			<p id="totalTime">  </p>
			<p id="randomFace"> </p>
			<p id="attentionCheckNumberPara"></p>
			<p id="totalPageTime"></p>
			<p id="instructionsTime"></p>
		</div>
		</center>
		<!-- jump back to the wheel data with wheel code --> 
		<!-- :::::::99HTV::::::::: -->

		
	</form>

	<script type='text/javascript'>

// % -------------------------------------------------------------------------
// Necessary functions
// %--------------------------------------------------------------------------
//:::::::::::99SC1::::::::::::::::::\\
//====================================
//--------COLISSION STUFF
//====================================

//NOTE: stagewidth is 500px default S:: make sure that the median of the array is actually the correct speed
/* -=Important Variables=-*/ 
var startingSpeedRatio = "<?php echo $startingRatio; ?>"; //S:: this is the previous speed ratio, since its 1:1 this is easy to calculate
var b1 = $('#object1c'); //Left value = 25;  these are from the CSS
var b2 = $('#object2c'); //Left value = 200; 

var numSpeeds = 122; //*** very important *** - number of speed gradations is actually this number / 2, 
					//so if you change this number you'll get more ~accurate~ gradations
					
//NOTE: numSpeeds actually should not change since the creation of the array uses hard coded values
//So if you do change it just make sure you get the logic on that part right. 


/***********************/
var speed2 = 0.9;      //speed of second object, hard coded, should not change
/**********************/

//Title slightly misleading: lowest raw number, fastest speed. 
var LOWER_BOUND = 0.9/4.760793126444734; //the fastest speed it can go. 

//the 4.7 number is from the exponential function. It only affects speeds from
//the median until the fastest speed so

//Speed 1 will change after the first chain. Starting speed ratio was 
//previous speed1 / speed2 picked, so we want to replicate that ratio by multiplying.

// EX: they reported a 3to1 ratio (meaning they thought object 1 was super fast)
// so object 1 was moving at 0.3s and object 2 was moving at 1s
// the ratio passed is 1/3
// so 0.9 * 1/3 means that the first speed will be fast. 
// This was confusing a bit since absolute speed gets faster as the numbers are lower. Which is
// obvious but tripped me up at first. 

var speed1 = 0.9*startingSpeedRatio; //speed of first obect in collision event, needs to be from ratio 
//Speed upper limit, or the slowest it can go 

//This is the upper bound, meaning the slowest speed it can go. Anything
//greater than 4.5 seconds should just snap to 4.5 seconds. 
if(speed1>4.5) {
	speed1 = 4.5;
}
if(speed1<LOWER_BOUND) {
	speed1 = LOWER_BOUND; 
}

var DISTANCE = 150; //This isn't really a used variable, it's just so you know the actual number of pixels oc1 and oc2 move. 
var minSpeed = 4.5;  // this is the max speed of the objects
var minRatio = 5; 
var maxSpeed = 0.1;
var NORMAL_SPEED = 0.9; //this is just to keep a record of the speed desired for the wheel. 

// NOTE: Change speed 3 and 4 to change the min and max of the wheel. The values are in seconds it takes to get from point a to b
var speed3 = speed2-.80; //fastest speed in array
var speed4 = speed2+.80;  //this isn't used anymore, remnant of when I used a completely linear paradigm. 
 
var exponentC = -0.05196479; //best fit for exponential curve, see notes

//:::::::::::99SC1::::::::::::::::::::\\


//:::::::::::99SAC:::::::::::::::::::\\
//Creating the speed array for the circle
//Not using any of these values right now, will come back to these for more robust code later
var speedArray = new Array(numSpeeds); //default 122 different gradations, takes the original speed and changes the scale 
var halfArray = ((numSpeeds-1)/2)+1; 
var timeScale = (speed2-speed3)/(Math.round(numSpeeds/4)); //this is the linear increment, gets passed into better named var. 
var quarterArray = ((halfArray-1)/2)+1; 
var timeScaleIncrement = timeScale; //this is the linear increment, only applies to first 31 gradations

//EXPONENTIAL FUNCTION: 
//exponential for top speeds: 4.5e^-0.05366479 to get curve

//so from [0] - [61] we need it to go from min to max, if there are 122 gradations
//Math.exp(x)  = E^x(exponentC) 

var inc;
//from 0 until the median 
for(inc =1; inc<=31; inc++) {
	speedArray[inc] = NORMAL_SPEED/((((NORMAL_SPEED-minSpeed)/31)*inc)+minSpeed); //the variable NORMAL_SPEED should really be speed2 
}
//from 31-61 
for(inc=32; inc<62; inc++) {
	speedArray[inc] = NORMAL_SPEED/(minSpeed*Math.exp(exponentC*inc));  
}
//from 61-91 (simply fill the array backwards) 
var thirdInc = 60; 
for(inc=62; inc<91; inc++) {
	speedArray[inc] = speedArray[thirdInc];
	thirdInc--; 
}
//from 91 to end
var secondInc = 31;
for(inc=91; inc<122; inc++) {
	speedArray[inc] = speedArray[secondInc];
	secondInc--; 
}
speedArray[0] = speedArray[122] = NORMAL_SPEED/minSpeed; //min, we hard code these in
//speedArray[61] = 0.1; //max speed

//End of array creation
//:::::::::::99SAC:::::::::::::::::::\\


//:::::::::::99CAS:::::::::::::::::::\\

//For noncausal only, in causal events we simply just remove it or make it 0.0
var NC_DELAY = 0.6;

//you should add (and wil see that it i added) the var NC_DELAY to this
var totalCollisionTime = speed1+speed2+0.1; //time it takes for the first collision animation to complete, 0.1 is the delay for the first object

/* Don't change the hard coded left values, they're for page display based on the CSS*/ 

//arguments: TweenMax(object, time, animation)
function collide1() {
	setTimeout(function(){}, 100); 
    TweenMax.to(b1, speed1, {left:175, yoyo:false, ease:'linear'}); //ratio var = 340
}

//To make it non causal, we add NC_DELAY to the delay: variable
function collide2() {
    TweenMax.to(b2, speed2, {left:350, delay: speed1+0.005, yoyo:false,          
                             ease:'linear'}); //ratio var = 150
}

//This is the function that gets called in actuality. 
function initialCollide() {
    collide1(); 
    collide2(); 
}

//==============================================
//S::C2 object speed choose
//	these are the 5 objects continuously moving
//==============================================
var cb1 = $('#object1');
var cb2 = $('#object2');
var cb3 = $('#object3');
var cb4 = $('#object4');
var cb5 = $('#object5');
var d =  $('#dynamics'); //unused
var timeScale = 0.1; //ignore this, it isn't used
var originalSpeed = 1; //this is really just a place holder because the speeds get multiplied by the values in the speedArray
var speedGradations = originalSpeed/31; //this isn't used either

//The tbx variables are the actual animations, the cbx variables are the actual css objects. 
//the tbx variables get modified later for timeScaling. 
var tb1 = TweenMax.to(cb1, originalSpeed, {left:0,   repeat:-1, yoyo:false, ease:'linear'}); 
var tb2 = TweenMax.to(cb2, originalSpeed, {left:150, repeat:-1, yoyo:false, ease:'linear'});
var tb3 = TweenMax.to(cb3, originalSpeed, {left:300, repeat:-1, yoyo:false, ease:'linear'});
var tb4 = TweenMax.to(cb4, originalSpeed, {left:450, repeat:-1, yoyo:false, ease:'linear'});
var tb5 = TweenMax.to(cb5, originalSpeed, {left:600, repeat:-1, yoyo:false, ease:'linear'});

//:::::::::::99CAS:::::::::::::::::::\\

//--------------------------------------------

//This function is just to make sure the colors display properly. 
function rgbToHex(rgb){
    var i= 0, c, hex= '#',
    rgb= String(rgb).match(/\d+(\.\d+)?%?/g);
    while(i<3){
        c= rgb[i++];
        if(c.indexOf('%')!= -1){
            c= Math.round(parseFloat(c)*2.55);
        }
        c= (+c).toString(16);
        if(c.length== 1) c= '0'+c;
        hex+= c;
    }
    return hex;
}

//This is just a helper function for some variables
function pad(number, length) {
  var str = '' + number;
  while (str.length < length) {
    str = '0' + str;
  }
  return str;
}

//::RTAC, jump back to degs by doing the wheel code
//This function returns the coordinates and info for the wheel. 
function rotateAnnotationCropper(offsetSelector, xCoordinate, yCoordinate, cropper){
    var x = xCoordinate - offsetSelector.offset().left - offsetSelector.width()/2;
    var y = -1*(yCoordinate - offsetSelector.offset().top - offsetSelector.height()/2);
    var theta = Math.atan2(y,x)*(180/Math.PI);    

    var cssDegs = convertThetaToCssDegs(theta);

    var rotate = 'rotate(' +cssDegs + 'deg)';
    cropper.css({'-moz-transform': rotate, 'transform' : rotate, '-webkit-transform': rotate, '-ms-transform': rotate});
    $('body').on('mouseup', function(event){ $('body').unbind('mousemove')});

    output =  cssDegs + randomOffset;
    output = (output % 360) + 1;
    if (output < 0) {
		output = 360+output;// since angles go from 0 to 270 and then -90 back to 0
    }
	
	//S:: we need to change this so that it gives a specific speed 
    faceNum = Math.round(output/degreesPerFace);
	
	//console.log("Face num: " + faceNum); //S::testing/debugging
	//console.log("SpeedArray[" + faceNum + "] = " + speedArray[faceNum] ); 

	//S::animation | this is where change the animation rates with time scale
	//:::::::::::99CTS:::::::::::::::::::\\
	//=======================================
	// TWEEN EDITING: 
	// does this by changing the time scale. 
	// timescale(3) = 3x faster
	// tb is the tween variable given to box1-5 (tweenedBox) 
	//=======================================
	
	tb1.timeScale(speedArray[faceNum]); 
	tb2.timeScale(speedArray[faceNum]); 
	tb3.timeScale(speedArray[faceNum]); 
	tb4.timeScale(speedArray[faceNum]); 
	tb5.timeScale(speedArray[faceNum]); 
	
	//:::::::::::99CTS:::::::::::::::::::\\
	
    if (faceNum < 1) {
    	faceNum = 1;    
    }
    
	//S:: this changed the face array
    //element.src = images[faceNum-1].src;
    return [faceNum, cssDegs, output, xCoordinate, yCoordinate];

}

function convertThetaToCssDegs(theta){
	var cssDegs = 90 - theta;
	return cssDegs;
}
//S:: Deleted preload function


// % -------------------------------------------------------------------------
//   Begin main javascript
// %--------------------------------------------------------------------------
var screenWidth = window.screen.width, screenHeight = window.screen.height;
d3.select("#screenWidth").node().value  = screenWidth;
d3.select("#screenHeight").node().value  = screenHeight;
var probeFaceNum = "<?php echo $startingFace; ?>"; //S:: this is the previous chain number

var debugging = "<?php echo $debugging; ?>";
var noiseOn = "<?php echo $noiseOn; ?>";


//S:: don't need this stuff anymore, should delete it
probeFaceNumString = pad(probeFaceNum,3);

// Change probe face depending on whether we're using noisy or vanilla version

if (noiseOn == 1) {
	probeFaceURLParent = "https://googledrive.com/host/0B33v_lnvxysAYVFUYm5XV3pYTWs/male_wb_";; // for noisy faces - NEED TO ADD if anything
} else {
	probeFaceURLParent = "https://googledrive.com/host/0B33v_lnvxysAb2xXRGpXTHVqRWs/male_wb_";; // for regular faces
}

extension = ".jpg";
var probeFaceURL = probeFaceURLParent + probeFaceNumString + extension;
var images = new Array();

//S:: deprecated?
probeImage = new Image();
probeImage.src = probeFaceURL;

numFaces = images.length;

//S:: for number of speed gradations
degreesPerFace = 360/numSpeeds;
randomOffset = Math.round(Math.random()*360); //so the number of gradations we will have for movement will also come from this
d3.select("#randomOffset").node().value = randomOffset;

var radius = 290; // radius of the circle around the face
var countdown = document.getElementById("countdown");
var marker = document.getElementById("marker");
var button = document.getElementById("faceChoice");
var firstButton = document.getElementById("next");
marker.style.visibility = 'hidden';
button.style.visibility = 'hidden';
firstButton.style.visibility = 'hidden';
$('#start').css({'visibility': 'hidden'});
var firstButtonOn = 0;

// When images are all loaded, allow subjects to see the Start button. 
// We do it this way because if we just $(window).load instead of
// $(document).ready things will only work if subjects click start WHILE
// the images are loading up.


$(window).load(function(){ 
	$('#loading').css({'visibility': 'hidden'});
	$('#start').css({'visibility': 'visible'});
});

// Begin the experiment when subjects click and when the DOM tree is loaded.
document.getElementById('start').onclick = function() {
	d3.select("#faceChoiceInstructionsTime").node().value = d3.select("#totalPageTime").node().innerHTML;
	$('#start').css({'visibility': 'hidden'});
$(document).ready(function(){     

    function startTimer(){
    	if(seconds<0) {
    		clearInterval(timer);
    	} else {
    		seconds--;
    	}
    }

    // Initially randomize the starting position of the marker and therefore first face shown.
	//Most of the following things should be self explanatory. 
    var min = 1; //for timer
    var max = 5; 
    
    attentionCheckNumberMax = 2;
    attentionCheckNumberMin = 1;
    var attentionCheckNumber = Math.floor(Math.random() * (attentionCheckNumberMax - attentionCheckNumberMin + 1)) + attentionCheckNumberMin; // generate random number between min and max
    var randomFace = 0;
    var faceNum = 0;
    var explorationAmount = 20; // number of faces they should have to see first S:: changed
    var countdownDuration = max+4;
    var countdownStart = max; // how many seconds remaining when the countdown should start
    var seconds = countdownDuration;

    var counter = seconds;
    var timer = setInterval(function(){
    	
        if (counter<=countdownStart){
        	if (counter === attentionCheckNumber) {
				$('#countdown').css({'color': 'red'});
			} else {
				$('#countdown').css({'color': 'black'});
			}
        	d3.select('#countdown').text(seconds);
        } else{
        	d3.select('#countdown').text("PAY ATTENTION");
        }
        counter = counter-1;
        startTimer();
        
    },1000); 
    
	//S:: ACTIONS
    var obj=document.getElementsByTagName('html')[0];
    var w=obj.offsetWidth;
    var h=obj.offsetHeight;

    var l=Math.floor(Math.random()*w);
    var t=Math.floor(Math.random()*h);  

	//:::::::::::99EXC::::::::::::
	
	//S::Display initial collision 
	setTimeout(function () {
		countdown.style.visibility = 'hidden'; //removes countdown 
		$('#stage1').css('visibility', 'visible'); 
		setTimeout(function() {initialCollide();}, 1000); //pause 1s before collision

	}, (countdownDuration+2)*1000); 
	
    // Blank screen for 1 second
	
	//S:: this should remove the stage
    setTimeout(function () {
		$('#stage1').css('visibility', 'hidden'); 
    }, (countdownDuration+3)*1000+totalCollisionTime*1000+600); //note: the 600 is just an extra buffer
	
	//wait a little bit after the first collision
	setTimeout(function() {
		$('#stageS').css('visibility', 'visible'); 
		
		//The stuff below presents the wheel and the go around
		firstButton.style.visibility = 'visible'; 
		$("#next").css({'display':'block'});
		$("#goAround").css({'display':'none'});
		firstButtonOn = 1;
	}, (countdownDuration+4)*1000+totalCollisionTime*1000+600); 
	
	//:::::::::::99EXC::::::::::::
    
    // Start recording reaction time and displaying test faces.
    setTimeout(function () {
	
		//:::::::::::::99CWD::::::::::::::
		$("#goAround").css({'visibility':'visible'}); //"Go around the circle to move" text display
		
    	var totalTime = $('#totalTime');
    	var tarrT = 0;
    	var delayT = 10;
    	updateTotalTime = setInterval(function() {
    		tarrT = tarrT + delayT;
    		totalTime.text(tarrT);
    	}, delayT);
		
    	//S:: element.style.visibility = 'visible'; commented out
		//S:: put the kinetic stage here with the different things
		
    	marker.style.visibility = 'visible'; //makes the red/green thing visible
		
		//Degs is an array that uses the rotateAnnotationCropper returns and stores them.
		//search up ::RTAC to go there, jump back with the wheel code
    	degs = rotateAnnotationCropper($('#innerCircle').parent(), l, t, $('#marker'));
		
		//These d3 variables constantly get updated when you move around the wheel. They
		//are stored in a hidden HTML section (99HTV) to be accessed by the php later.
		//I'm pretty sure the x and y coordinates aren't used for anything special. 
		//This will give the initial info right when the page loads, but the rotateAnnotationCropper
		//inside of the mouseclick function will update. 
		
    	d3.select('#outputNumber').text(degs[0]); //this is the number of the face they picked
    	d3.select('#degNumber').text(degs[1]);
    	d3.select('#outputNumber2').text(degs[2]);
    	d3.select('#xcoordinate').text(degs[3]);
    	d3.select('#ycoordinate').text(degs[4]);
		d3.select('#absObj1').text(speed1); //S:: this is the speed of object1
		d3.select('#absObj2').text(speed2); //S:: this is the speed of object2
		
    	randomFace = degs[0]; //S:: added the speedArray(X) portion
		
		//Not important
		randomFaceRawSpeedV = speedArray[degs[0]]; //S:: to get raw speed for the multiplier
	
		//Not important
    	randomFaceAngle = degs[1];
		
    	d3.select('#randomFace').text(randomFace); //S:: added to the output, this means this is the first speed shown in the wheel
		//before they move their mouse
    	d3.select("#randomFaceAngle").node().value = randomFaceAngle;
    	var toggleMove = true;  			

		//Wheel cropper color changer from green to red and back
		$('#marker').on('click', function(event){ // works!
			toggleMove = !toggleMove;
			if (toggleMove) {$('#marker').css({'background': 'lime'});

			} else {$('#marker').css({'background': 'red'});};
		});

		//Wheel cropper color changer from green to red and back
		$('body').on('click',function(event){ // works for the first click, but due to overlap doesn't work after shut off.
			if (!$(event.target).closest('#marker').length) {
				toggleMove = false;
				$('#marker').css({'background': 'red'});
			};
		});
		$('html').mousemove(function(event){	

			//:::::::::99CWU:::::::::::::
			//These are the variables that actually respond to the mouse moving
			if (toggleMove){	

				//This rotation cropper is the active one
		   		degs = rotateAnnotationCropper($('#innerCircle').parent(), event.pageX,event.pageY, $('#marker'));
				
				//The speed "face" they picked
		   		d3.select('#outputNumber').text(degs[0]); 
				
				//The calculated speed ratio, which uses 0.9ms/timeScale as the speed of the second object,
				//so ratios are reported as speed 1 over speed 2
				d3.select('#speedRatio').text(speed1/((speed2/speedArray[degs[0]])));  //S::speed 1 over speed picked
				
				//This is the absolute speed picked for speed 2, so 0.9ms/timeScale
				d3.select("#absObj2").text(speed2/(speedArray[degs[0]]));
				 
				//If you remember this is just the speedArray value
				d3.select('#randomRawSpeed').text(randomFaceRawSpeedV);
				
				//These ones are less important, but if you want to know what they are exactly 
				//you can console.log them
		   		d3.select('#degNumber').text(degs[1]);
		   		d3.select('#outputNumber2').text(degs[2]);
		   		d3.select('#xcoordinate').text(degs[3]);
		   		d3.select('#ycoordinate').text(degs[4]);
		   		d3.select('#totalTime').text(totalTime);
				 
		   		d3.select('#attentionCheckNumberPara').text(attentionCheckNumber);
				
				//Just shows that degs[0] is the faceNum, not sure why it is so low in the code
				//but stephan had it that way
				
		   		faceNum=degs[0]; 
				//d3.select('#speedRatio').text(Math.round((faceNum / speed1)*100)/100); //S:: check to see that this works
				
				//This method originally made it so that you had to explore a bit for it to say "Next" but
				//we eliminated that. 
	   			if ((Math.abs(faceNum-randomFace)>explorationAmount)&&(firstButtonOn == 0) ){
					//S:: don't need anything here
					//moved to occur right after things show up
					//firstButton.style.visibility = 'visible'; changed
					//$("#next").css({'display':'block'});
					//$("#goAround").css({'display':'none'});
					//firstButtonOn = 1;
				};
			} 
			
		});     
		//:::::::::99CWU:::::::::::::

   	},(countdownDuration+4)*1000+totalCollisionTime*1000+600);
	//:::::::::::::99CWD::::::::::::::

	//Hides everything
	document.getElementById('next').onclick = function() {
  		clearInterval(updateTotalTime);
   		
   		firstButton.style.visibility = 'hidden';
   		$("#next").css({'display':'none'});
   		button.style.visibility = 'visible';

	}

// });
}); 
}


</script>


<?php
}
else if (($_SERVER['REQUEST_METHOD'] == 'GET')||($_SESSION['stage'] == 3)||(isset($_SESSION['Saving']))){

	//::::::::::::::::99EAP:::::::::::::::
	
	//If you remember, these are taken from the HTML variables way above
	$maxRT = 60; //if there reaction time is any longer, we exclude their data.
	$workerID = $_SESSION['workerID'];
	$race = $_SESSION['race'];
	$attentionCheckResponse = $_SESSION['attentionCheckResponse'];
	$attentionCheckNumber = $_SESSION['attentionCheckNumber'];
	$comments = $_SESSION['comments'];
	$surveyThoughts = $_SESSION['surveyThoughts'];
	$origRace = $race;
	$country = $_SESSION['country'];
	$gender = $_SESSION['gender'];
	$age = $_SESSION['age'];	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date("m.d.y");
	$stage =  (int) $_SESSION['stage'] + 1;	
	$startingFace = $_SESSION['startingFace'];
	$participant = $_SESSION['participant'];
	$chain = $_SESSION['chain'];
	$ip = $_SESSION['ip'];
	$randomFace = $_SESSION['randomFaceInput'];
	$randomFaceRawSpeed2 = $_SESSION['randomFaceRawSpeed']; 
	$object1Speed = $_SESSION['absO1'];
	$object2Speed = $_SESSION['absO2'];
	$randomOffset = $_SESSION['randomOffset'];
	$randomFaceAngle = $_SESSION['randomFaceAngle'];
	$chosenFaceAngle = $_SESSION['chosenFaceAngle'];
	$chosenFace = $_SESSION['finalFace'];
	$RT = $_SESSION['RT']/1000;
	$screenWidth = $_SESSION['screenWidth'];
	$screenHeight = $_SESSION['screenHeight'];
	//$browserCheckResponse = $whatHappened //S::reimplemented this
	//$browserCheckResponse = 1;
	$browserCheckResponse = $_SESSION['whatHappened'];
	$familyCSV = $_SESSION['familyCSV'];
	// $fh = $_SESSION['fh'];
	$browser = $_SERVER["HTTP_USER_AGENT"];
	$demographicsPageTime = $_SESSION['demographicsPageTime']/1000;
	$browserCheckPageTime = $_SESSION['browserCheckPageTime']/1000;
	$faceChoicePageTime = $_SESSION['faceChoicePageTime']/1000;
	$speedRatioChosen = $_SESSION['chosenRatio']; //S:: need to add to array
	$faceChoiceInstructionsTime = $_SESSION['faceChoiceInstructionsTime']/1000;

	$expStartTime = $_SESSION['expStartTime'];
	$expEndTime = date('H:i:s');
	
	$data = readCSV($familyCSV);

	$numFaces = 122;
	$prevTrialData = end($data);

	if ($chosenFace>($numFaces/2)) {
		$simpleChosenFace = $numFaces - ($chosenFace - 1);
	} else {
		$simpleChosenFace = $chosenFace;
	}

	if ($randomFace>($numFaces/2)) {
		$simpleRandomFace = $numFaces - ($randomFace - 1);
	} else {
		$simpleRandomFace = $randomFace;
	}
	//::::::::::::::::99EAP:::::::::::::::


	//% -------------------------------------------------------------------------
	// Generate confirmation code
	$confirmationCode="";
	srand();

	for($i = 0; $i < 9; $i++){
	       $num = rand(0,9);
	       $char = chr($num);
	       $confirmationCode = $confirmationCode.$num;
		}
	$confirmationCode = $confirmationCode.".cda.";
	for($i = 0; $i < 7; $i++){
		$num = rand(0,9);
		$char = chr($num);
		$confirmationCode = $confirmationCode.$num;
	}

	$confirmationCode = $confirmationCode.".1";	
	srand();
	// % -------------------------------------------------------------------------
	// Give confirmation code & debriefing. S:: change the description ehre
	echo "  <div id='debriefing' style='text-align:justify; margin: 0 auto; width: 60em;'>    
			The experiment is over. <b>Your confirmation number is " . $confirmationCode. "</b><br /><br />";
	echo "<h2>Debriefing:</h2>

			In this study, we were exploring biases in your memory for the speed
			of collision events. We are interested in whether your memory might be 
			biased systematically in certain directions. For example, you might remember 
			the second red square as going faster or slower than it really was.<br /><br />

			Thanks for participating, and if you have any further questions please contact the experimenter at jonathan.kominsky@yale.edu.<br /><br />";

	$excludedCSV = $filePrefix.'excluded_'.$race.$fileExtension;
	// Write updated data to file.
	if (($RT <= $maxRT)&&($attentionCheckResponse == $attentionCheckNumber)) {
		// Update data
		$currentData = array($workerID, $chain, $participant, $country, $ip, 
						     $date, $gender, $age, $startingFace, $randomFaceRawSpeed2, $object1Speed, $object2Speed,
							 $simpleChosenFace, /*S:: */$speedRatioChosen, $chosenFaceAngle, $RT, 
							 $browserCheckResponse, $browser, $confirmationCode, $comments, 
							 $surveyThoughts, $demographicsPageTime, $browserCheckPageTime, $faceChoicePageTime,
							 $faceChoiceInstructionsTime, $randomOffset,$randomFaceAngle, $expStartTime, $expEndTime,
							 $attentionCheckNumber, $attentionCheckResponse,$screenWidth, $screenHeight);
		$data[] = $currentData;
		$fh = fopen($familyCSV, 'w'); //should be putting it in correct familyCSV
		foreach ($data as $fields) {
			fputcsv($fh, $fields);
		}
		fclose($fh);


		
	} else {

		if (!file_exists($excludedCSV)) {
			$columnHeadings[] = 'Excluded_Reason';
			$data = array($columnHeadings);		

			$fh = fopen($excludedCSV, 'w');
			foreach ($data as $fields) {
				fputcsv($fh, $fields);
			}
			fclose($fh);
			chmod($excludedCSV, 0640);
		}
		else { // Let's just write what we've recorded to the file so far, making sure it keeps going...
			$data = readCSV($excludedCSV);
		}

		if ($RT > $maxRT) {
			
			if ($debugging) {
				echo "Your data will be excluded because your reaction time was over 60s.
					  You will still be compensated for your time.";
			}
			
			$excludedReason = 'RT';


		} elseif ($attentionCheckResponse !== $attentionCheckNumber){
			if ($debugging) {
				echo "Your data will be excluded because you failed to name the correct countdown number that turned red. 
					  You will still be compensated for your time.";
			}
			
			$excludedReason = 'attention check';

		}
		// Update data
		$currentData = array($workerID, $chain, $participant, $country, $ip, $date, $gender, $age, $startingFace, 
			$randomFaceRawSpeed2, $object1Speed, $object2Speed, $simpleChosenFace, /*S::*/$speedRatioChosen, $chosenFaceAngle, $RT, $browserCheckResponse, $browser, 
			$confirmationCode, $comments, $surveyThoughts, $demographicsPageTime, $browserCheckPageTime, 
			$faceChoicePageTime, $faceChoiceInstructionsTime, $randomOffset, $randomFaceAngle,
			$expStartTime, $expEndTime, $attentionCheckNumber, $attentionCheckResponse, $screenWidth, $screenHeight, 
			$excludedReason);	
		$data[] = $currentData;
		$fh = fopen($excludedCSV, 'w');
		foreach ($data as $fields) {
			fputcsv($fh, $fields);
		}
		fclose($fh);

	}
	


	// % -------------------------------------------------------------------------
	// remove the file from the locked file list
	// Test block

	$lockedFiles = readCSV($lockedFileList);
	$duration = 60*60; // number of minutes * 60 to get seconds //S:: might change this to get 30
	foreach ($lockedFiles as $index => $fields) {
		// if (in_array($familyCSV, $fields)) {
		// if ( (in_array($familyCSV, $fields))||(strtotime('now')-$fields[1]>$duration)) {
		if ( ( (in_array($familyCSV, $fields))||(strtotime('now')-$fields[1]>$duration)  ) && !(in_array('blank_line', $fields)) ) {
			unset($lockedFiles[$index]); // index remains unchanged, so we don't need to decrement count in theory
		}
	}

	$fl = fopen($lockedFileList, 'w+');
	foreach ($lockedFiles as $fields) {
		fputcsv($fl, $fields);
	}
	fclose($fl);


	if ($debugging){
		echo "The chosen face was " . $_SESSION['finalFace'] . "<br />";
		echo "The chosen ratio was " . $_SESSION['chosenRatio'] . "<br />"; //S:: 
		echo "This also corresponds to face number " . $simpleChosenFace . "<br />";
		reportData($data);	
	}
	
	echo "</div>";

}


?>
<!-- Main js script goes in a separate document -->
<?php
if ($debugging) {
	echo "<p> Current time on page: ";
	$time -> display_time("current_page"); // will display the time the user spent on the current page (dinamyc)
}
	
?>
	<script type="text/javascript">
	//Here is where we put the crucial information in the CSV file (reading the HTML) 
	
	//::::::::::::::::99CVA::::::::::::::::::::
	
	//This is a bit misleading since this script comes after the php, but it really is before it chronologically
	//This updates the CSV with the variables stored in the HTML
	
	/* Why this is weird is because you'll notice that we are matching two d3 selects. 
	It is pretty redundant, but it does't work unless you do it this way so I just did it
	how Stephan did. The right handed variables are from the HTML variables in the class 
	and the left handed variables are the ones not in the class. You can check this with the HTV. 
	*/ 
	
		//On submit means that after they press the submit button that these things will happen. 
		getSecs();
		d3.select("#demographicsForm")
		.on("submit", function() {
			d3.select("#demographicsPageTime").node().value = d3.select("#totalPageTime").node().innerHTML;
		});

		d3.select("#browserCheckForm")
		.on("submit", function() {
			d3.select("#browserCheckPageTime").node().value = d3.select("#totalPageTime").node().innerHTML;
		});

		d3.select("#faceChoice")
		.on("submit", function() {
			//These should be obvious by name
			d3.select("#finalFace").node().value = d3.select("#outputNumber").node().innerHTML;
			d3.select("#RT").node().value = d3.select("#totalTime").node().innerHTML;
			d3.select("#attentionCheckNumber").node().value = d3.select("#attentionCheckNumberPara").node().innerHTML;
			d3.select("#randomFaceInput").node().value = d3.select("#randomFace").node().innerHTML;
			d3.select("#chosenFaceAngle").node().value = d3.select("#degNumber").node().innerHTML;
			d3.select("#faceChoicePageTime").node().value = d3.select("#totalPageTime").node().innerHTML;
			d3.select("#chosenRatio").node().value = d3.select("#speedRatio").node().innerHTML; 
			d3.select("#randomFaceRawSpeed").node().value = d3.select("#randomRawSpeed").node().innerHTML; 
			d3.select("#absO1").node().value = d3.select("#absObj1").node().innerHTML;
			d3.select("#absO2").node().value = d3.select("#absObj2").node().innerHTML;

		});
	//::::::::::::::::99CVA::::::::::::::::::::
	</script>



</body>
</html>

