//GLOBAL VARIABLES
var url = "word-search.txt";
var $container = $('#question3');

//CONSTRUCTOR FUNCTIONS
function Letter (x, y, ID) {
	this.xcoord = x;
	this.ycoord = y;
	this.ID = ID;
}

function Word (word){
	this.word = word;
	this.reversed = word.reverse();
}

//GLOBAL OBJECTS
var Letters = {};	//This will be used as a dictionary for all the Letter objects.

//Make sure Ajax does not cache in case word-search.txt context changes.
$.ajaxSetup({
	cache: false
});

//When document is ready, execute
$(document).ready(function(){

	letsAjax(url);

	//Uses Ajax to read and manipulate word-search.txt
	function letsAjax(url){
		if (url.match('^http')) {
			var errormsg = 'AJAX cannot load external content';
			$container.html(errormsg);
		}
		else {
			$.ajax({
				url: url,
				data: String,
				timeout: 5000,
				success: function(data){
					//remove Loading...
					$('#question3 p').remove();

					//split word grid from list of words
					var wordSearch = data.split("\n\n");
					//split grid into rows
					var rows = wordSearch[0].split("\n");
					//split list into individual words
					var words = wordSearch[1].split("\n");
					var listTitle = words.splice(0,1);
					
					//add each word to the DOM separately, adding extra <div>'s
					$('#wordList').prepend("<div id ='listTitle'>"+listTitle+"</div>");
					for (var i = 0; i < words.length; i++){
						$('#wordList ul').append("<li class='word'>"+words[i]+"</li>");
					}

					//get rid of spaces in words for searching purposes and make new Word object
					for (var i = 0; i < words.length; i++) {
						words[i] = words[i].split(' ').join('');
						//convert to uppercase for consistency
						words[i] = words[i].toUpperCase();
					}

					//get rid of tabs or spaces in rows
					for (var i = 0; i < rows.length; i++) {
						rows[i] = rows[i].split("\t").join('');
					}

					//add each letter to the DOM separately, adding extra <div> tags for each new row
					//iterate through each row
					for (var y = 0; y < rows.length; y++) {
						//convert to upper case letters
						rows[y] = rows[y].toUpperCase();
						
						//add row tag
						$('#wordSearch').append("<div class='row'>");
						
						//iterate through each letter
						for (var x = 0; x < rows[y].length; x++) {
							var currentLetter = rows[y][x];
							var ID = 'letter-'+x+'-'+y;

							//create new Letter object and add it Letters dictionary
							//if key already exists, append object to that key, otherwise create key and object in array to it.
							if (currentLetter in Letters) {
								Letters[currentLetter].push(new Letter(x, y, ID));
							}
							else {
								Letters[currentLetter] = [new Letter(x, y, ID)];
							}
							//add letter to DOM with unique ID
							$('#wordSearch').append("<div class='letter' id="+ID+">"+currentLetter+"</div>");
						}
						$('#wordSearch').append('</div>');
					}

					//Find word 
					// var result = searchGrid("HEALTH");
					//Search for word HEALTH
					var result = searchGrid("BAZOOKA");
					alert(result);

					function highlightWord(word){
						$('#'+Letters[firstLetter][i].ID).addClass('highlight');
					}

					function searchGrid(currentWord){
						var firstLetter = currentWord[0];
						var xorigins =	[];
						var yorigins = [];
						var wordLen = currentWord.length;
						var currentString;

						//get possible coordinates
						for (var i=0; i<Letters[firstLetter].length; i++){
							xorigins[i] = Letters[firstLetter][i].xcoord;
							yorigins[i] = Letters[firstLetter][i].ycoord;
						}

						//search all coordinates in 8 directions using rows[y][x]
						for (var i=0; i<xorigins.length; i++){
							//EAST
							currentString = rows[yorigins[i]].substring(xorigins[i], xorigins[i] + wordLen);
							if ( currentString === currentWord){
								return true;
							}

							//SOUTH
							// currentString = [];
							// for (var n = 0; n<wordLen; i++){
							// 	currentString += rows[]
							// }
						}

					}
				},
				error: function(req, error) {
					if (error === 'error'){
						error = req.statusText;
					}
					var errormsg = 'There was a communication error: ' + error;
					$container.html(errormsg);
				},
				beforeSend: function(data) {
					$container.prepend('<p>Loading...</p>');
				}
			});
		}
	}

});

