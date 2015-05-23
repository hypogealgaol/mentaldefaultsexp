<!DOCTYPE html>
<html>
<head>
	<title>Mechanical Turk HIT</title>
	
</head>
<body>


<script>
// Take the user to a random URL, selected from the pool below 
var links = new Array();

links[0]="http://perceptionexperiments.net/SR01_AFS/workerID_check.php";
links[1]="http://perceptionexperiments.net/SR04/browserCompatibilityCheck.php";

function randomizeURL(linkArray){
window.location=linkArray[Math.floor(Math.random()*linkArray.length)]
}

randomizeURL(links);
</script>

</body>
</html>
