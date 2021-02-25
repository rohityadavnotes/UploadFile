<?php
/* Define your Server host name here. */
define('DATABASE_HOST_NAME', 	    'localhost');

/* Define your Database User Name here. */
define('DATABASE_USERNAME', 	    'id14853975_mobile');

/* Define your Database Password here. */
define('DATABASE_PASSWORD', 	    'ph[YKeA7n!))l^W=');

/* Define your MySQL Database Name here. */
define('DATABASE_NAME', 		    'id14853975_restapi');

/* Attempt to connect to MySQL database */
$connection = mysqli_connect(DATABASE_HOST_NAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
 
/*Check connection
if($connection === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
else
{
    echo "Connection Successfull...";
}*/
?>