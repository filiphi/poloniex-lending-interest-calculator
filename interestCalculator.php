<?php
  require_once('poloniexApi.php');
  date_default_timezone_set('UTC');

  class interestCalculator {
    protected $startDate;
    protected $endDate;
    protected $client;
    protected $throttleTime;
    protected $throttleAttempts;
    protected $currency;

    public function __construct($apiKey, $apiSecret, $startDate, $endDate, $currency) {
      $this->poloniexClient = new poloniex($apiKey, $apiSecret);
      $this->startDate = strtotime($startDate);
      $this->endDate = strtotime($endDate);
      $this->interestRevenue = floatval(0);
      $this->currency = $currency;
    }

    public function getInterestRevenue() {
      // Returns the interest revenue in dollars
      $trades = $this->getLendingHistory();
      print 'number of trades: ' . count($trades) . PHP_EOL;
      if (is_array($trades)) {
        foreach ($trades as $key => $value) {
          if ($value['currency'] !== $this->currency) {
            continue;
          }
          $this->calculateInterestValue($value);
        }
      }
      return $this->interestRevenue;
    }

    private function calculateInterestValue($loan) {
      print '-------- New Loan ---------' . PHP_EOL;
      print 'Loan closed at ' . $loan['close'] . PHP_EOL;
      $trade = $this->getTrade($loan['close'], 60);

      print 'trade closed at: ' . $trade['rate'] . PHP_EOL;
      print 'interest earned: ' . $loan['earned'] . PHP_EOL;

      $profit = floatval($loan['earned']) * floatval($trade['rate']);

      print 'profit is: ' . $profit . PHP_EOL;

      $this->interestRevenue = $this->interestRevenue + $profit;
    }

    private function getLendingHistory() {
      return $this->poloniexClient->returnLendingHistory($this->startDate, $this->endDate);
    }

    private function getTrade($time, $interval, $limit = 0) {
      if (empty($this->throttleStart)) {
        $this->throttleStart = microtime(true);
      }
      if (empty($this->throttleAttempts)) {
        $this->throttleAttempts = 0;
      }

      if (((microtime(true) - $this->throttleStart) < 1) && $this->throttleAttempts > 4) {
        print 'risk zone - sleep. Attempts: ' . $this->throttleAttempts . PHP_EOL;
        sleep(1);
      }

      if (((microtime(true) - $this->throttleStart) > 1)) {
        print 'threshold passed' . PHP_EOL;
        $this->throttleStart = microtime();
        $this->throttleAttempts = 0;
      }

      if ($limit > 4) {
        print 'no trades found, limit passed' . PHP_EOL;
        exit();
      }

      $unixTimeStamp = strtotime($time);
      $result = $this->poloniexClient->get_historic_trade_history('usdt_'. $this->currency, $unixTimeStamp - $interval, $unixTimeStamp + $interval);

      $this->throttleAttempts = $this->throttleAttempts + 1;

      if (is_array($result) && (count($result) > 0)) {
        return (array_shift($result));
      } else {
        print 'no trade found in interval, trying again' . PHP_EOL;
        $this->getTrade($time, $interval * 2, $limit + 1);
      }
    }
  }
?>
