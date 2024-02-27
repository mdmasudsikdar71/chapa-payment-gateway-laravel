<?php

namespace MDMasudSikdar\Chapa\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MDMasudSikdar\Chapa\ChapaClient;
use PHPUnit\Framework\TestCase;

class ChapaClientTest extends TestCase
{
    /**
     * @var MockHandler
     */
    private MockHandler $mockHandler;

    /**
     * Set up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize a mock handler for HTTP client
        $this->mockHandler = new MockHandler();
    }

    /**
     * Test ChapaClient transactionInitialize method.
     */
    public function testTransactionInitialize()
    {
        // Arrange
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => true, 'message' => 'Transaction initialized'])));

        // Create ChapaClient with mock HTTP client
        $httpClient = new Client(['handler' => HandlerStack::create($this->mockHandler)]);
        $chapaClient = new ChapaClient();
        $chapaClient->setHttpClient($httpClient);

        // Act
        $response = $chapaClient->transactionInitialize([
            'amount' => 100,
            'currency' => 'USD',
        ]);

        // Assert
        $this->assertTrue($response['status']);
        $this->assertEquals('Transaction initialized', $response['message']);
    }

    /**
     * Test ChapaClient transactionVerify method.
     */
    public function testTransactionVerify(): void
    {
        // Arrange
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => true, 'message' => 'Transaction verified'])));

        // Create ChapaClient with mock HTTP client
        $httpClient = new Client(['handler' => HandlerStack::create($this->mockHandler)]);
        $chapaClient = new ChapaClient();
        $chapaClient->setHttpClient($httpClient);

        // Act
        $response = $chapaClient->transactionVerify('sample_tx_ref');

        // Assert
        $this->assertTrue($response['status']);
        $this->assertEquals('Transaction verified', $response['message']);
    }

    /**
     * Tear down the test case.
     */
    protected function tearDown(): void
    {
        // Clean up resources, if any
        parent::tearDown();
    }
}
