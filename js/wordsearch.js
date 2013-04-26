//GLOBAL VARIABLES
var url = "word-search.txt";
var $container = $('#question3');

//GLOBAL ARRAYS
var directions = ["east", "southeast", "south", "southwest", "west", "northwest", "north", "northeast"];

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

//When document is ready, execute
$(document).ready(function(){
	//Make sure Ajax does not cache in case word-search.txt context changes.
	$.ajaxSetup({
		cache: false
	});

	letsAjax(url);

	//Uses Ajax to read and manipulate word-search.txt
	//
	//Only works with specific formatting that file is provided in. 
	//Grid must come first and there must be tabs in between the letters
	//followed by 2 new lines and a line that is the word list's title
	//and finally the word list
	//
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
					$('#wordList').prepend("<div id ='listTitle'>"+listTitle+"</div><ul>");
					for (var i = 0; i < words.length; i++){
						$('#wordList ul').append("<li class='word'>"+words[i]+"</li>");
					}
					$('#wordList').append("</ul>");

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

					//find max grid values
					var xmax = rows[0].length;
					var ymax = rows.length;

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
							var ID = x+'-'+y;

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
					$('.word').click(function(){
						$(this).addClass('wordClicked');
						var result = searchGrid($(this).text().toUpperCase().split(" ").join(""));
						highlightWord(result);
					});

					$('.button').click(function(){
						$('.wordClicked').removeClass('wordClicked');
						$('.highlight').removeClass('highlight');
						return false;
					});

					function searchGrid(currentWord){
						var firstLetter = currentWord[0];
						var xorigins =	[];
						var yorigins = [];
						var wordLen = currentWord.length;
						var dx = 0;
						var dy = 0;

						//get possible origin coordinates for word 
						for (var i=0; i<Letters[firstLetter].length; i++){
							xorigins[i] = Letters[firstLetter][i].xcoord;
							yorigins[i] = Letters[firstLetter][i].ycoord;
						}

						//checks in all directions
						for (var k=0; k<directions.length; k++) {
							var direction = directions[k];

							switch (direction) {
								case "south":
									dx = 0;
									dy = 1;
									break;
								
								case "west":
									dx = -1;
									dy = 0;
									break;

								case "north":
									dx = 0;
									dy = -1;
									break;

								case "northeast":
									dx = 1;
									dy = -1;
									break;

								case "southeast":
									dx = 1;
									dy = 1;
									break;

								case "southwest":
									dx = -1;
									dy = 1;
									break;

								case "northwest":
									dx = -1;
									dy = -1;
									break;

								case "east":
									dx = 1;
									dy = 0;
									break;
							}

							
							for (var i=0; i<xorigins.length; i++){
								
								//(Re)initialize 
								var currentString = "";
								var currentLetters = [];
								var x = 0;
								var y = 0;

								for (var n=0; n<wordLen; n++){	
									currentString += rows[yorigins[i]+y][xorigins[i]+x];
									currentLetters.push($('#'+(xorigins[i]+x)+'-'+(yorigins[i]+y)));
									x += dx;
									y += dy;

									//checks to see if next iteration will be out of row index
									if ((yorigins[i]+y) >= ymax || (yorigins[i]+y) < 0 || (xorigins[i]+x)>= xmax || (xorigins[i])+x<0){
										break;
									}
								}
								
								if (currentString === currentWord){
									return currentLetters;
								}
							}
						}
						alert("Word was not found.");
					}

					//highlights found words
					function highlightWord(currentLetters){
						for (var i=0; i<currentLetters.length; i++){
							$(currentLetters[i]).addClass('highlight');
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

