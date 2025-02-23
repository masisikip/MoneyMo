<?php
include_once('./../includes/authenticate.php');

authenticate(0);
echo json_encode('Allowed');
