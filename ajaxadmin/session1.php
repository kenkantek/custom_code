<?php

session_start();

echo 'Session ID '.session_id().'<br/>';

echo 'Session before set<br/>';

var_dump($_SESSION);

$_SESSION['data'] = 'example data set to session';

echo '<br/>Session after set<br/>';

var_dump($_SESSION);