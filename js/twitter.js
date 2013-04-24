
//click on button to refresh API call
$(document).ready(function(){
	$('#refreshTwitter').click(function(){
		$.get("./twitter.php");
		return false;
	});
});