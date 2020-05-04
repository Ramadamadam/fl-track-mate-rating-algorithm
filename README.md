# fl-trackmate-rating-algorithm


## First of all, be aware of the concept "Runner"
A "Runner", or a "Race Runner", is a combination of horse and race it runs in.

This concept is used in algorithm documents and in the code. Must know what it means.

## The algorithm

See [here](doc/new-code-flow/00.new-flow.puml)

Note: need to install [PlantUML Visualizer Chrome Plugin](https://chrome.google.com/webstore/detail/plantuml-visualizer/ffaloebcmkogfdkemcekamlmfkkmgkcf?hl=en) to see the diagram

## Testing guide 

Jian suggests you create a link on every race card. So on the race card page you can click a link to see the horses' ruf ratings 
 
The link is like, 

```
http://host-name/test.php?track_name=Chelmsford%20City&race_date=2019-03-15&race_time=16:40:00&race_dates_interval=6%20months
``` 

* 3 parameters to define a race, and 1 parameter for the period
* Jian will let you know the host name via chat
* A problem is that the system runs in Jian's computer and exposed to you via [ngrok](https://ngrok.com/) 
  * It may not be very stable.
  * Better run it on your own local machine (See below)

## Run this system
* make sure your php is >= 7.4
* (optional) create a [database](sql/jian-create-test-db.sql) and load all the data, if you don't have your own database
* Download this github repo as a zip file, extract it, or git clone it
* Change the db name and credentials in RufRatingDataAccess.php 
* Run it 
```
cd fl-track-mate-rating-algorithm/php
composer install  #or install php-ds in your own way
php -S localhost:8000
```
* Visit http://localhost:8000/test.php


## Integrate this project's code into race card system

Note: **Don't do the integration until testing is all done**, which may last a few days. Otherwise when you want any change in this project,
* it will be very difficult for Jian to do it in your code
* Jian can also do it here, let you do the integration again. The integration will be difficult

Steps:

* Copy all code under php/Tracemate/RufRatingRewrite to your system, with directory structure and namespaces unchanged
* Deal with RufRatingDataAccess.php
  * It's using PDO.  You can rewrite it with $wpdb
  * Or you can just update the db credentials there and keep using this file. Up to you.
* Call ```function get_ruf_ratings_for_race_next_day()``` inside ruf_rating.php . Please read the phpdoc of this function.