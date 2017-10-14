# SymfonyDayIt 2017

This repo is just a support for the presentation at https://docs.google.com/presentation/d/1EHUH942rj1miMPrKIRw_eFWDMHYKNzwvHYvRxwprWEs/edit?usp=sharing

It's composed by 3 implementation steps:

- branch step1 (A synchronous integration with a payment gateway)
- branch step2 (An asynchronous integration using RabbitMQ)
- branch step3 (An asynchronous integration with a backoff strategy for rate limits)

To start the app run `./restart.sh`
To stress the payment gateway and reach the rate limit run `./stress_payment.sh`
To test the payment gateway run `./pay`