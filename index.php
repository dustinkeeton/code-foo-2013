<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Code-Foo 2013 Application - Dustin Keeton</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link href="css/no-theme/jquery-ui-1.10.2.custom.min.css" rel="stylesheet"> <!-- jquery ui theme  -->
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div id="container"> 
            <div id="title"><h1>Dustin Keeton - Code-Foo 2013 Application</h1></div>
            <div class="question">
                <p class="prompt"><h4>Create a 2-5 minute video introducing yourself and showing your passion for IGN and the Code-Foo program.</h4></p>
                <video id ="video" controls>
                  <source src="video/video.m4v" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div> 
            <hr>
            <div class="question">
                <div>
                    <p class="prompt"><h4>How many gamers are in the San Francisco Bay Area? Describe each step in your thought process.</h4></p>
                    <p>
                        - Assumed Information -<br>
                        Gamer - Someone who plays games somewhat consistently.<br><br>  

                        - Gathered Data -<br>
                        Gamer Population in US in 2011: 211,500,000 (<a href="https://www.npd.com/wps/portal/npd/us/news/press-releases/pr_120905/" target="_blank">NPD</a>)<br>
                        Population in the US in 2010: 308,745,538 (<a href="http://en.wikipedia.org/wiki/2010_United_States_Census" target="_blank">US Census</a>)<br>
                        Population in the US in 2012: 313,933,954 (<a href="http://www.census.gov/popclock/" target="_blank">Census Pop Clock</a>)<br>
                        Population in the US in 2013: 315,737,810 (<a href="http://www.census.gov/popclock/" target="_blank">Census Pop Clock</a>)<br>
                        Population of San Francisco in 2010: 805,235 (<a href="http://factfinder2.census.gov/faces/tableservices/jsf/pages/productview.xhtml?pid=DEC_10_DP_DPDP1" target="_blank">US Census</a>)<br>
                        Percentage of Gamers who had purchased or had planned to purchase one or more games in 2012: 46% (<a href="http://www.theesa.com/facts/pdfs/ESA_EF_2012.pdf" target="_blank">ESA</a>)<br><br>
                        
                        - Thought Process -<br>
                        We know the population of San Francisco was 805,235 people in 2010. If we assume the population has grown at the same rate as the 
                        US population from 2010 - 2013 (2.26%) then the current population of San Francico is 807,055.<br><br>

                        We know the 'gamer' population in the US in 2011 was 211,500,000. NPD stated it as being a 5% decrease since 2010. Since we
                        have no other data if we assume the population decreased at the same rate in 2012 (and let's say for the sake of argument that this
                        decrease does not happen until December 31st of each year), then the current population of gamers is 196,500,000.<br><br>

                        196,500,000 is 62.24% of the total US population. If we assume that this relationship is consistent throughout the US
                        then the current population of gamers in San Francisco can be said to be 62.24% of 807,055 or 502,311 people.<br><br>

                        - Conclusion -<br> 
                        This data is very rough. The numbers are taken from different sources and different years and assumptions have been made about
                        population trends and the consistency of the pool of participants between each source. Additionally, all numbers were rounded to
                        the nearest hundredth during calculations and then to the nearest whole number to describe population.<br><br>

                        Also, the term 'gamer' is very loose. Judging by the ESA's statement that only 46% of gamers had or had planned to buy more
                        than ZERO games in 2012 I might argue that only those 46% are actual gamers. This of course opens a whole can of worms
                        about income and so-forth. I suppose you could still be a gamer if you've been playing Ocarina of Time everyday for the past 15 years.<br><br>

                        So...<br><br>

                        Given the numbers and assumptions, the total number of gamers in San Francisco is 502,311.
                        I would rather say that the number is 46% of that, or 231,063.<br><br>

                        San Francisco is a tech-centric city with many game development studios according to 
                        <a href="http://www.gamedevmap.com/index.php?query=francisco&Submit=Search" target="_blank">gamedevmap</a>. The percentage of
                        gamers in San Francisco is likely to be higher than that of the American population as a whole. Thus the above answer
                        of 502,311 gamers, or 231,063 under the stricter definition, is most likely a lower bound on the actual number. However, without 
                        data it is difficult to determine a more accurate value. If it is required to have only one answer, then I suppose 
                        I will go with 502,311 to be in keeping with popular census groups.
                    </p>
                </div>
            </div>
            <hr>
            <div class="question" >
                <p class="prompt"><h4>Write a program to find the given words from the following word search.</h4></p>
                <div id ="wordSearch"></div>
                <div id ="wordList">
                </div>
            </div>
            <div class="button" href="#">CLEAR</div>
            <hr>
            <div class="question">
                <p class="prompt"><h4>Create a responsive layout using media queries. Must support iPad, iPhone, and common resolutions for
                    desktops. Nest your entire application in this responsive interface.</h4></p>
                <div>You're lookin' at it.</div>
            </div>
            <hr>
            <div class="question">
                <p class="prompt"><h4>Using the Twitter API, pull and display the last 40 tweets from the 'ign' account. Use <a href="http://dev.twitter.com" 
                    target="_blank">dev.twitter.com</a> for reference</h4></p>
                <!-- Twitter REST API -->
                <?php include('twitter.php'); ?>
           
            </div>
        </div>
        
       
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/wordsearch.js"></script>
        <script>window.jQuery.ui || document.write('<script src="js/libs/jquery-ui-1.10.2.custom.min.js"><\/script>')</script>
    </body>
</html>
