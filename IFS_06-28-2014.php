<?php
// RACE - STARTING HALFWAY BETWEEN MIDPOINT AND BLACK, 61 FACES
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
// 05-09-2014: for age, starting at the extremes. Right now starting at the old extreme. Also using female faces
// 06-06-2014: For happy, started at neutral at first, now starting 10 away from the extremes 
// 06-15-2014: Just doing happy to neutral (61 faces).
// 06-28-2014: Starting blacker for race

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
	<script type="text/javascript" src="http://mbostock.github.com/d3/d3.js?1.29.1"></script>
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type='text/javascript' src="necessary_functions.js"></script>
	<!-- CSS styling goes in a separate document -->
	<link href="iterativeFaces.css" rel="stylesheet" type="text/css">
	
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

if($chrome) {die('Cannot use Chrome! Please use Firefox or Internet Explorer instead!');}


//% -------------------------------------------------------------------------
// Stop mobile users from accessing the website
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
die('Cannot use mobile devices! Please use a laptop or desktop computer.');

//% -------------------------------------------------------------------------
// Extract worker ID and HIT info from URL.

// $hitId        = $_REQUEST["hitId"];
// $assignmentId = $_REQUEST["assignmentId"];
// $workerId     = $_REQUEST["workerId"];

// % -------------------------------------------------------------------------
// Declare constant variables
$noiseOn = 0;
$debugging = 0;
$debuggingErrors = 0;
$chainLength = 10; // number of individuals in a single chain, used to be 30.
$defaultStartingFace = 46; // midway between neutral and black.
$filePrefix = 'SR01_LM_SB_data_'; // the n means neutral
$fileExtension = '.csv';
$lockedFileList = 'SR01_LM_SB_files_in_use.csv';
$workerIDList = 'past_participant_list.csv';

$columnHeadings = array('Worker_ID', 'Chain_Number', 'Participant_Number', 'Nationality', 'IP', 'Date', 'Gender', 
						'Age', 'Race', 'First_Face', 'Random_face', 'Chosen_Face', 'Chosen_Face_Angle', 'RT', 
						'Browser_Check_Response', 'Browser_Name', 'Confirmation_Code', 'Comments', 'What_Tested', 
						'Demographics_Page_Time', 'Browser_Check_Page_Time','Face_Page_Time', 
						'Face_Instructions_Time', 'Random_Offset', 'Random_Face_Angle', 'Exp_Start_Time',
						 'Exp_End_Time', 'Attention_Check_Number', 'Attention_Check_Response', 
						 'Screen_Width', 'Screen_Height');
$startingData = array('dummy', 1, '0', 'Afghanistan', '127.0.0.1', 'today', 'male', '18', 
					  'race', $defaultStartingFace, $defaultStartingFace, $defaultStartingFace, 0, 1, 
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
$raceColumn = 9-1;
$startingFaceColumn = 10-1;
$randomFaceColumn = 11-1; // since we have to count from 0
$chosenFaceColumn = 12-1; // since we have to count from 0
$chosenFaceAngleColumn = 13-1; // since we have to count from 0
$RTColumn = 14-1;
$browserCheckColumn = 15-1;
$browserColumn = 16-1;
$confirmationCodeColumn = 17-1;
$commentsColumn = 18-1;
$surveyThoughtsColumn = 19-1;

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


	// $hitId        = $_REQUEST['hitId'];
	// $assignmentId = $_REQUEST['assignmentId'];
	// $workerID     = $_REQUEST['workerId'];
	
	// echo "Hit ID: $hitId\n";
	// echo "Ass ID: $assignmentId\n";
	// echo "Worker ID: $workerId\n";


	// display form
	?>
	<div type="hidden" class="output">
		<p id="totalPageTime">  </p>
	</div>

	<center><div id="consentPreamble" class="instructions">
		<h3>In order for us to conduct this test online, we need to include the standard consent form below.</h3>
	</div></center>
	<div id="consentForm" class="instructions consent-box">
		<center><h1> Informed Consent Form</h1></center>
		<p id="consentInstructions">
		This research project concerns psychological processes. The experiment will ask you to 
		answer simple questions and/or perform simple tasks by interacting with a Web survey. 
		Your data will be pooled with those of others, and your responses will be completely 
		anonymous. However, we will keep the data obtained for all subjects indefinitely, for 
		possible use in other scientific publications. <br /> <br />
		 
		The experiment will take only a few minutes and carries no risks.  <br /> <br />
		 
		Due to the nature of psychology experiments, we cannot explain the precise purpose of 
		the experiment until after the session is over. However, afterwards the experimenter will 
		be happy to answer any questions you might have about the procedure. You will receive 
		the reward specified on the Mechanical-Turk HIT for participating. Your participation is 
		completely voluntary. You are free to decline to participate to end participation at any 
		time for any reason, or to refuse to answer any individual question without penalty or loss 
		of compensation.  <br /> <br />
		 
		If you have any questions or concerns regarding this experiment, you may contact us here 
		at the lab. If you have general questions about your rights as a research participant, you 
		may contact the Yale University Human Subjects Committee. This study is exempt
		under 45 CFR 46.101(b)(2).  <br /> <br />
		 
		 
		Yale Univ. Human Subjects Com.<br />   
		55 College St. (P.O. Box 208010)<br />
		New Haven, CT 06520-8010<br />   
		203-785-4688<br />   
		human.subjects@yale.edu <br /> 
		<br />  
		<br />
		 
		<b>By answering the demographic questions and clicking the "Consent/Next" button below, 
		you acknowledge that you have read and understood the above and agree to participate.</b>
		<br /> <br />   
		 
		You can also contact the experimenter if you have any questions:<br />  
		Stefan Uddenberg, Graduate Student <br />  
		Yale University <br /> 
		stefan.uddenberg@yale.edu </p>
	</div>

	 <br /> <br />

	<div class="survey">
		
		<form name="demographicsForm" id="demographicsForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

			<label id="whatWorkerID">What is your M-TURK Worker ID? (e.g. A1BGDXZ95IQ3W) </label>
				<input type="text" name="workerID" id="workerID" value = "<?php echo $workerID ; ?>" size="30" style="height:1em;">
			
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

			<label id="whatAge">What is your age?</label>
				<select name="age" id="age">
				 <?php 
				 	$minAge = 18; // 18-19 yr olds not allowed in this study
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
		fputcsv($fl,array('A1BGDXZ95IQ3W')); 
		fclose($fl);
		chmod($workerIDList, 0640);
	}
	$excludedWorkerIDs = readCSV($workerIDList);
	$properID = validate_worker_id($workerID);
	// Stop them if they didn't input a proper URL

	// $min_length = 12;
	// $max_length = 15;
	// $bad_regex = '/\W/';

	// $workerIDPattern = '[A][';
	// if(~preg_match($workerIDPattern, $workerID)){
	// if   ( (stristr($workerID, 'http:')) || (stristr($workerID, '@')) || (stristr($workerID, 'https:'))
	// 	|| (stristr($workerID, 'www.')) || (stristr($workerID, '.net')) || stristr($workerID, '.') ) {
	if (!$properID) {
		
		die('
			<div id="notEligibleURL" class="instructions" style="color:red"><center><h2> Oops! It looks like you pasted
			something that is not a Worker ID into the Worker ID box. Your worker ID is a series of random
			numbers and letters starting with A (e.g. A1BGDXZ95IQ3W). It is not the study URL,
			nor is it your email address! To try again with your real worker ID, please re-enter the study 
			URL into your address bar, as the refresh button likely will not work at this point.
			If you believe you have received this message in
			error, please contact the experimenter at stefan.uddenberg@yale.edu. Otherwise, please return the HIT. </h2> </center>
			</div>
		');

	} 

	foreach ($excludedWorkerIDs as $fields) {
		if ( in_array_case_insensitive($workerID, $fields) || (empty($workerID)) ) {
			die('
			<div id="notEligible" class="instructions" style="color:red"><center><h2> You are not eligible 
			to take the test as you have already done it (or you left the worker ID question blank)! If you believe you have received this message in
			error, please contact the experimenter at stefan.uddenberg@yale.edu. Otherwise, please return the HIT. </h2> </center>
			</div>
			');
		}
	}	

	// since we got to this point, add the worker ID to the list of excluded ones.
	$excludedWorkerIDs[] = array($workerID);

	$fw = fopen($workerIDList, 'w');
	foreach ($excludedWorkerIDs as $fields) {
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
		at least once, and click on the rectangle at the edge of the ring to see its possible colors. Any transitions as you 
		move your mouse should appear smooth.
		<br /> <br />
		<b>Note that failure to answer all three questions below correctly
		in this section will lock you out of the survey and will therefore require 
		that you return the HIT.</b>
		<br /> <br />
		Now, please answer the questions below.  </p>
	<div id ="dynamic-container">
		<div id ="marker"></div>
		<div id ="innerCircle"> <!-- <p id="outputNumber"> <?php echo $outputNumber ?>  </p> -->
			<div class="circle" id="testCircle"> </div>
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
							<option value="The shape changed size">The shape changed size</option>
							<option value="The shape changed color">The shape changed color</option>
							<option value="The shape changed from one shape to another">The shape changed from one shape to another</option>
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
<script type='text/javascript' src='iterativeFaces_browserCheck.js'></script>

<?php


}
else if (($_SERVER['REQUEST_METHOD'] == 'GET')|| (! isset($_SESSION['Saving']))) {

	// Check if browser check was correct
	$rectangleColors = $_SESSION['rectangleColors'];
	$shape = $_SESSION['shape'];
	$whatHappened = $_SESSION['whatHappened'];
	$correctColors = 'red/green';
	$correctShape = 'Circle';
	$correctEvent = 'The shape changed size';

	if ( !( ($rectangleColors == $correctColors) && ($shape == $correctShape) && ($whatHappened == $correctEvent) )   ) {
		die('
			<div id="failedBrowserCheck" class="instructions" style="color:red"><center><h2> You are not eligible 
			to complete this HIT as you chose the wrong answers for the browser compatibility check. 
			This means that your browser did not render this initial test accurately,
			and would fail to render the real test as well.
			If you believe you have received this message in error, please contact 
			the experimenter at stefan.uddenberg@yale.edu. 
			Otherwise, please return the HIT. </h2> </center>
			</div>
		');
	}



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
	// Pare down race info
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
	

	// Choose relevant CSV file - shouldn't need this at all since everyone is fair game.
	// Choose relevant CSV file
	switch ($race) { // should be okay to start counting from 1 here since we are talking about the value here.
// case 1:
		case "White":
		$csvRace = 'white';
		break;

		// case 2:
		case "Black":
		case "African":
		case "black":
		$csvRace = 'black';
		break;

		// case 3:
		case "Asian":
		$csvRace = 'asian';
		break;

		// case 4:
		case "Native":
		$csvRace = 'native';
		break;

		// case 5:
		case "Multiracial":
		$csvRace = 'multiracial';
		break;

		// case 6:
		case "Indian":
		$csvRace = 'indian';
		break;

		// case 7:
		case "Hispanic":
		$csvRace = 'hispanic';
		break;

		default:
		$csvRace = $origRace;
		break;
	}

	$familyCSV = $filePrefix . $csvRace . '_01'. $fileExtension;
	
	// Create output file if it doesn't exist
	if (!file_exists($familyCSV)) {
		$chain = 1;
		$participant = 1;
		$startingFace = $defaultStartingFace;
		$data = array($columnHeadings);	
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

			// Create familyCSV if it doesn't exist
			if (!file_exists($familyCSV)) {
				$chain = 1;
				$participant = 1;
				$startingFace = $defaultStartingFace;
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
	while (empty($startingFace)) {
		$currentIndex--;
		$prevTrialData = $data[$currentIndex];
		$startingFace = $prevTrialData[$chosenFaceColumn];
	}

	$chain = $prevTrialData[$chainColumn];
	$participant = $prevTrialData[$paritipantColumn] + 1;
	if ($participant > $chainLength) {
		$participant = 1;
		$chain = $chain+1;
		$startingFace = $defaultStartingFace;
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

$_SESSION['startingFace'] = $startingFace;
$_SESSION['chain'] = $chain;
$_SESSION['participant'] = $participant;
$_SESSION['familyCSV'] = $familyCSV;
$_SESSION['csvRace'] = $csvRace;
?>
<center> <div id="container">
	<p id="instructions1" style="text-align:justify; margin: 0 auto; width: 60em;">

	Thanks for participating in our quick survey! Before we continue, please make your window as large as you can.<br /><br />

	We're going to show you a single face very quickly.  Your job is to remember the face that you saw as best you can, and then we're going to give you a memory test.<br /><br />

	Because the face will be presented very quickly, it's important that you are paying close attention.  So when you start, you'll first see the words "Pay attention", followed by a countdown from 5 to 0.  During the countdown, one of the numbers will be colored red.  You'll have to remember which number that was, because we'll ask you after the survey is over.  Once the countdown reaches 0, the face will appear.<br /><br />

	After the face disappears, you're going to have to reproduce it, using the mouse: A new face will appear in the center of a circle, and as you move your cursor around the circle, the face will change in various ways.  Your job is to move the cursor around until you think the face depicted in the circle is the same one that you saw initially.  At that point, you can click the mouse to stop the face from changing, and then hit the "Next" button to move on.  (If you accidently click the mouse before you've made up your mind, you can just click on the marker to start the face changing again.  This part of the survey will end as soon as you click "Next".)
		</p><br />
	<p id="instructions2">Please make sure you can see the entire circle in your browser window, then press the "Start" button when you are ready to begin.</p>

	<p id="READ_THIS" class="instructions" style="color:red"><center><h2>MAKE SURE TO READ THE INSTRUCTIONS ABOVE BEFORE CLICKING "START"!</h2></center>
	
	<p id="loading">Loading...</p>
	<div class="buttonHolder"><button id='start'>START</button></div>
	<div id ="dynamic-container">
		<div id ="marker"></div>
		<div id ="innerCircle"> <!-- <p id="outputNumber"> <?php echo $outputNumber ?>  </p> -->
			<img id="face" /> 
			<div id = "countdown"> <!--<?php echo $countdown ?> --></div> </div>
		</div>                
	</div>   </center> 
	<center><div><p id="goAround"> Go around the whole circle to move on. </p></div>
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

		<input type='hidden' name='stage' value='<?php echo $stage + 1; ?>'/>
		<input type='hidden' name='chosenFaceAngle' id="chosenFaceAngle" />
		<input type='hidden' name='RT' id="RT" />
		<input type='hidden' name='attentionCheckNumber' id="attentionCheckNumber" />
		<input type='hidden' name='randomFaceInput' id="randomFaceInput" />
		<input type='hidden' name='finalFace' id="finalFace" />
		<input type='hidden' name='faceChoicePageTime' id="faceChoicePageTime" />
		<input type='hidden' name='faceChoiceInstructionsTime' id="faceChoiceInstructionsTime" />
		<input type='hidden' name='randomOffset' id="randomOffset" />
		<input type='hidden' name='randomFaceAngle' id="randomFaceAngle" />
		<input type='hidden' name='screenWidth' id="screenWidth" />
		<input type='hidden' name='screenHeight' id="screenHeight" />
		<div type="hidden" class="output">
			<p id="outputNumber"> </p> 
			<p id="degNumber"> </p>
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

		
	</form>

	<script type='text/javascript'>

// % -------------------------------------------------------------------------
// Necessary functions
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


function pad(number, length) {
  var str = '' + number;
  while (str.length < length) {
    str = '0' + str;
  }
  return str;
}

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
    faceNum = Math.round(output/degreesPerFace);
    if (faceNum < 1) {
    	faceNum = 1;    
    }
    
    element.src = images[faceNum-1].src;
    return [faceNum, cssDegs, output, xCoordinate, yCoordinate];

}

function convertThetaToCssDegs(theta){
	var cssDegs = 90 - theta;
	return cssDegs;
}

function preload(){
	for (i = 0; i < preload.arguments.length; i++) {
		images[i] = new Image();
		images[i].src = preload.arguments[i];
	}    
}

// % -------------------------------------------------------------------------
// Begin main javascript
var screenWidth = window.screen.width, screenHeight = window.screen.height;
d3.select("#screenWidth").node().value  = screenWidth;
d3.select("#screenHeight").node().value  = screenHeight;
var probeFaceNum = "<?php echo $startingFace; ?>";
var debugging = "<?php echo $debugging; ?>";
var noiseOn = "<?php echo $noiseOn; ?>";

probeFaceNumString = pad(probeFaceNum,3);

// Change probe face depending on whether we're using noisy or vanilla version
if (noiseOn == 1) {
	probeFaceURLParent = "https://googledrive.com/host/0B33v_lnvxysAUm9qUFJqd2hYaUk/male_wb_";; // for noisy faces - NEED TO ADD if anything
} else {
	probeFaceURLParent = "https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_";; // for regular faces
}

extension = ".jpg";
var probeFaceURL = probeFaceURLParent + probeFaceNumString + extension;
var images = new Array();

preload(
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_001.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_002.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_003.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_004.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_005.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_006.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_007.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_008.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_009.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_010.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_011.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_012.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_013.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_014.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_015.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_016.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_017.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_018.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_019.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_020.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_021.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_022.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_023.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_024.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_025.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_026.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_027.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_028.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_029.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_030.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_031.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_032.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_033.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_034.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_035.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_036.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_037.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_038.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_039.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_040.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_041.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_042.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_043.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_044.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_045.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_046.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_047.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_048.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_049.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_050.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_051.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_052.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_053.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_054.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_055.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_056.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_057.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_058.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_059.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_060.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_061.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_061.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_060.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_059.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_058.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_057.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_056.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_055.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_054.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_053.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_052.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_051.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_050.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_049.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_048.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_047.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_046.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_045.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_044.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_043.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_042.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_041.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_040.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_039.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_038.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_037.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_036.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_035.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_034.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_033.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_032.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_031.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_030.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_029.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_028.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_027.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_026.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_025.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_024.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_023.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_022.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_021.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_020.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_019.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_018.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_017.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_016.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_015.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_014.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_013.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_012.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_011.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_010.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_009.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_008.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_007.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_006.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_005.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_004.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_003.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_002.jpg",
	"https://googledrive.com/host/0B33v_lnvxysARGFrOUxNaXJOeFU/male_wb_001.jpg"
	);

probeImage = new Image();
probeImage.src = probeFaceURL;


numFaces = images.length;
degreesPerFace = 360/numFaces;
randomOffset = Math.round(Math.random()*360);
d3.select("#randomOffset").node().value = randomOffset;
var element=document.getElementById('face');
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
    var min = 1;
    var max = 5;
    
    attentionCheckNumberMax = 2;
    attentionCheckNumberMin = 1;
    var attentionCheckNumber = Math.floor(Math.random() * (attentionCheckNumberMax - attentionCheckNumberMin + 1)) + attentionCheckNumberMin; // generate random number between min and max
    var randomFace = 0;
    var faceNum = 0;
    var explorationAmount = 50; // number of faces they should have to see first
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
    
    var obj=document.getElementsByTagName('html')[0];
    var w=obj.offsetWidth;
    var h=obj.offsetHeight;

    var l=Math.floor(Math.random()*w);
    var t=Math.floor(Math.random()*h);  

    // Display probe face
    setTimeout(function () {
    	element.src = probeImage.src;
    }, (countdownDuration+2)*1000);

    // Blank screen for 1 second
    setTimeout(function () {
    	element.style.visibility = 'hidden';
    	countdown.style.visibility = 'hidden';
    }, (countdownDuration+3)*1000);
    
    // Start recording reaction time and displaying test faces.
    setTimeout(function () {
    	var totalTime = $('#totalTime');
    	var tarrT = 0;
    	var delayT = 10;
    	updateTotalTime = setInterval(function() {
    		tarrT = tarrT + delayT;
    		totalTime.text(tarrT);
    	}, delayT);
    	element.style.visibility = 'visible';
    	marker.style.visibility = 'visible';
    	degs = rotateAnnotationCropper($('#innerCircle').parent(), l, t, $('#marker'));
    	d3.select('#outputNumber').text(degs[0]);
    	d3.select('#degNumber').text(degs[1]);
    	d3.select('#outputNumber2').text(degs[2]);
    	d3.select('#xcoordinate').text(degs[3]);
    	d3.select('#ycoordinate').text(degs[4]);
    	randomFace = degs[0];
    	randomFaceAngle = degs[1];
    	d3.select('#randomFace').text(randomFace);  
    	d3.select("#randomFaceAngle").node().value = randomFaceAngle;
    	var toggleMove = true;  			

		$('#marker').on('click', function(event){ // works!
			toggleMove = !toggleMove;
			if (toggleMove) {$('#marker').css({'background': 'lime'});

			} else {$('#marker').css({'background': 'red'});};
		});

		$('body').on('click',function(event){ // works for the first click, but due to overlap doesn't work after shut off.
			if (!$(event.target).closest('#marker').length) {
				toggleMove = false;
				$('#marker').css({'background': 'red'});
			};
		});
		$('html').mousemove(function(event){		
			if (toggleMove){	   			
		   		degs = rotateAnnotationCropper($('#innerCircle').parent(), event.pageX,event.pageY, $('#marker'));    
		   		d3.select('#outputNumber').text(degs[0]);
		   		d3.select('#degNumber').text(degs[1]);
		   		d3.select('#outputNumber2').text(degs[2]);
		   		d3.select('#xcoordinate').text(degs[3]);
		   		d3.select('#ycoordinate').text(degs[4]);
		   		d3.select('#totalTime').text(totalTime);
		   		d3.select('#attentionCheckNumberPara').text(attentionCheckNumber);
		   		faceNum=degs[0];
	   			if ((Math.abs(faceNum-randomFace)>explorationAmount)&&(firstButtonOn == 0) ){
					firstButton.style.visibility = 'visible';
					$("#next").css({'display':'block'});
					$("#goAround").css({'display':'none'});
					firstButtonOn = 1;
				};
			} 
			
   	});     
   	

   	},(countdownDuration+4)*1000);


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
	$randomOffset = $_SESSION['randomOffset'];
	$randomFaceAngle = $_SESSION['randomFaceAngle'];
	$chosenFaceAngle = $_SESSION['chosenFaceAngle'];
	$chosenFace = $_SESSION['finalFace'];
	$RT = $_SESSION['RT']/1000;
	$screenWidth = $_SESSION['screenWidth'];
	$screenHeight = $_SESSION['screenHeight'];
	// $browserCheckResponse = $_SESSION['whatHappenedResponse'];
	$browserCheckResponse = 1;
	$familyCSV = $_SESSION['familyCSV'];
	$csvRace = $_SESSION['csvRace'];
	// $fh = $_SESSION['fh'];
	$browser = $_SERVER["HTTP_USER_AGENT"];
	$demographicsPageTime = $_SESSION['demographicsPageTime']/1000;
	$browserCheckPageTime = $_SESSION['browserCheckPageTime']/1000;
	$faceChoicePageTime = $_SESSION['faceChoicePageTime']/1000;
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

	//% -------------------------------------------------------------------------
	// Generate confirmation code
	$confirmationCode="";
	srand();

	for($i = 0; $i < 9; $i++){
	       $num = rand(0,9);
	       $char = chr($num);
	       $confirmationCode = $confirmationCode.$num;
		}
	$confirmationCode = $confirmationCode.".sbr.";
	for($i = 0; $i < 7; $i++){
		$num = rand(0,9);
		$char = chr($num);
		$confirmationCode = $confirmationCode.$num;
	}

	$confirmationCode = $confirmationCode.".a";	
	srand();
	// % -------------------------------------------------------------------------
	// Give confirmation code & debriefing.
	echo "  <div id='debriefing' style='text-align:justify; margin: 0 auto; width: 60em;'>    
			The experiment is over. <b>Your confirmation number is " . $confirmationCode. "</b><br /><br />";
	echo "<h2>Debriefing:</h2>

			In this study, we were exploring biases in your memory of faces. 
			We are interested in whether facial memory might be biased systematically in 
			certain directions. For example, you might remember the face as being whiter 
			or blacker than it was depending on your own racial identity.<br /><br />

			Thanks for participating, and if you have any further questions please contact the experimenter at stefan.uddenberg@yale.edu.<br /><br />";

	$excludedCSV = $filePrefix.'excluded_'.$race.$fileExtension;
	// Write updated data to file.
	if (($RT <= $maxRT)&&($attentionCheckResponse == $attentionCheckNumber)) {
		// Update data
		$currentData = array($workerID, $chain, $participant, $country, $ip, 
						     $date, $gender, $age, $race, $startingFace, $simpleRandomFace,
							 $simpleChosenFace, $chosenFaceAngle, $RT, 
							 $browserCheckResponse, $browser, $confirmationCode, $comments, 
							 $surveyThoughts, $demographicsPageTime, $browserCheckPageTime, $faceChoicePageTime,
							 $faceChoiceInstructionsTime, $randomOffset,$randomFaceAngle, $expStartTime, $expEndTime,
							 $attentionCheckNumber, $attentionCheckResponse,$screenWidth, $screenHeight);
		$data[] = $currentData;
		$fh = fopen($familyCSV, 'w');
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
		$currentData = array($workerID, $chain, $participant, $country, $ip, $date, $gender, $age, $race, $startingFace, 
			$simpleRandomFace, $simpleChosenFace, $chosenFaceAngle, $RT, $browserCheckResponse, $browser, 
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
	$duration = 60*60; // number of minutes * 60 to get seconds
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
			d3.select("#finalFace").node().value = d3.select("#outputNumber").node().innerHTML;
			d3.select("#RT").node().value = d3.select("#totalTime").node().innerHTML;
			d3.select("#attentionCheckNumber").node().value = d3.select("#attentionCheckNumberPara").node().innerHTML;
			d3.select("#randomFaceInput").node().value = d3.select("#randomFace").node().innerHTML;
			d3.select("#chosenFaceAngle").node().value = d3.select("#degNumber").node().innerHTML;
			d3.select("#faceChoicePageTime").node().value = d3.select("#totalPageTime").node().innerHTML;
		});
	</script>



</body>
</html>

