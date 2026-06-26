<?php

require 'mail.php';

$result = envoyerMail(
    'tonadresse@gmail.com',
    'NJARATIANA',
    'Pieces automobiles',
    date('Y-m-d H:i:s')
);

echo $result['message'];
