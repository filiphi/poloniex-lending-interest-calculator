<?php
  require_once('./interestCalculator.php');
  date_default_timezone_set('UTC');

  $apiKey = $argv[1];
  $apiSecret = $argv[2];
  $startDate = $argv[3];
  $endDate = $argv[4];
  $currency = $argv[5];

  // Initiate the calculator class
  $interestCalculator = new interestCalculator($apiKey, $apiSecret, $startDate, $endDate, $currency);

  // Calculate interest income
  $startTime = time();
  $income = $interestCalculator->getInterestRevenue();
  $endTime = time();
  $jobLength = $endTime - $startTime;

  print PHP_EOL . 'Congrats - you have earned: $' . $income . PHP_EOL;
  print 'Job took: ' . $jobLength . ' seconds';
?>
