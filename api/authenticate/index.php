<?php
include_once('../includes/authenticate.php');
$headers = getallheaders();
authenticate(user_type:(int) $headers['x-type'], hashed:$headers['x-hashed']);
