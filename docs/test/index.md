# Testing Symfony Application

[PHPUnit](https://phpunit.de) is the most popular testing framework in PHP world. 

Symfony has built-in PHPUnit integration and provides two base `TestCase` classes. 

* `KernelTestCase` provides a test-scoped Kernel to bootstrap a service container for test purpose.
* `WebTestCase` runs the application against the test environment configuration. You can create a HttpClient and use it to shake hands with the server side via HTTP protocol.

In the next sections, we will add testing codes to verify if the application work as expected.  