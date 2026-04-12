<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['submit_plan'] = true;
$_POST['full_name'] = 'Test Form Form Submission';
$_POST['phone'] = '123123123';
$_POST['interests'] = ['Heritage', 'Temple'];
$_POST['travel_mode'] = 'Car';

require_once 'suggestor.php';
echo "Ran form simulation\n";
