<?php
require_once  'Covid19Data.php';

echo 'From Wikipedia:<br>';
echo json_encode(retrieve_covid_data_wiki('ghana'));

echo 'From Worldometers:<br>';
echo json_encode(retrieve_covid_data_worldometers('ghana'));