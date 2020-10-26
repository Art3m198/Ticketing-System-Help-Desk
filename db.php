<?php 
require 'libs/rb.php';
R::setup( 'mysql:host=127.0.0.1;dbname=ticket','ticket', '12345' ); 

if ( !R::testconnection() )
{
		exit ('Connection To The Database Failed');
}

session_start();