# fl-trackmate-rating-algorithm


## First of all, be aware of the concept "Runner"
A "Runner", or a "Race Runner", is a combination of horse and race it runs in.

This concept is used in this document and in the code. Must know what it means.

## About the code
It is an exact translation of the java code. 
 
## Integrate this project's code into race card system

* Install "php-ds" to your php. See https://www.php.net/manual/en/ds.installation.php
* Copy all code under php/Trackmate/RufRatingRewrite to your system, with directory structure and namespaces unchanged. All other code doesn't matter.
* Implement function RufRatingsEngine.isCompatibleRaceType()
* Deal with RufRatingDataAccess.php
  * It's using PDO.  You can rewrite it with $wpdb
  * Or you can just update the db credentials there and keep using this file. Up to you.
* Call it 
  * Call RufRatingsEngine.processRacesForUpcomingDay()   
    * The input:  a target date, and a period for calculation (e.g. 100 days, 6 months etc)
    * The output:  The ruf ratings for all the history runners of every horse in the system
    * test.php can serve as an example 
  * It takes time to run so better do it as a nightly job, like the java code
    * Run it
    * Calculate avg/worst/best for every horse by yourself, and save the results into your database
    * On your race card page, find the saved ruf rating records for the horses on your race card page.  