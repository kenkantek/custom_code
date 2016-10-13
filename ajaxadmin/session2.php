<?php

session_start();

echo 'Session ID '.session_id().'<br/>';

echo 'Session data is<br/>';

var_dump($_SESSION);