<?php
require_once 'Covid19Data.php';
$covid_data = new Covid19Data();
echo 'From Wikipedia:<br>';
echo json_encode($covid_data->retrieve_covid_data_wiki('ghana'));

echo '<br>';

echo 'From Worldometers:<br>';
echo json_encode($covid_data->retrieve_covid_data_worldometers('ghana'));