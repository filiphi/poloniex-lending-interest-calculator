# How much have you made?

Calculate actual interest in USD when lending cryptocurrencies at Poloniex.

At the Poloniex exchange you can lend out cryptocurrencies to margin traders (https://poloniex.com/lending#BTC).

At some point - for example when declaring taxes - you will want to know exactly how many dollars you've made while lending. This tool allows you to do so.

Validated with:

* validate with time intervals of max 1 month
* validated with LTC and BTC

## How it works

1. Gets your earnings from the private api
2. Filters on currency
3. Fetches a usdt to currency trade from the public api
4. Gets usd value of earnings by multiplying trade value with earnings

## Using

1. Get your API key and secret from Poloniex.
2. Run `php main.php $api_key $api_secret $startTime $endTime $cryptocurrency`

## Caveats

There is something strange with the poloniex API endpoint, so run for max one month at a time.

## Links

PoloniexApi client borrowed and evolved from https://pastebin.com/iuezwGRZ (actually the only reason I wrote this in php).
