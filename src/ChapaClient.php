<?php

namespace MDMasudSikdar\Chapa;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ChapaClient class provides a client for interacting with the Chapa API.
 */
class ChapaClient
{
    // API version constant
    const API_VERSION = 'v1';

    // Base URL, secret key, and default headers
    protected string $baseUrl;
    protected string $secretKey;
    private array $headers;

    // Guzzle HTTP client instance
    protected Client $httpClient;

    /**
     * ChapaClient constructor.
     *
     * @throws \RuntimeException if Chapa secret key is not configured.
     */
    public function __construct()
    {
        // Load configuration values
        $this->baseUrl = 'https://api.chapa.co/' . self::API_VERSION;
        $this->secretKey = config('chapa.secret_key', '');

        // Check if the secret key is configured
        if (empty($this->secretKey)) {
            throw new \RuntimeException("Chapa secret key is not configured.");
        }

        // Initialize Guzzle HTTP client
        $client = new Client();
        $this->setHttpClient($client);

        // Set default headers
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * Set the HTTP client for the API client.
     *
     * @param Client $client The Guzzle HTTP client instance.
     * @return ChapaClient Returns the modified instance of Chapa.
     */
    public function setHttpClient(Client $client): static
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Send an HTTP request to the specified URL.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param string $method The HTTP method for the request (default is 'POST').
     * @param array $body The request body.
     * @param array $appendData Additional data to append to the response.
     * @return array The decoded response data.
     *
     * @throws ClientExceptionInterface if the HTTP request fails.
     */
    public function sendRequest(string $endpoint, string $method = 'POST', array $body = [], array $appendData = []): array
    {
        try {
            $url = $this->baseUrl . $endpoint;

            // Determine if the body should be included in the request
            $hasBody = !empty($body);
            $bodyContent = $hasBody ? json_encode($body) : null;

            // Create a Guzzle HTTP request
            $request = new Request($method, $url, $this->headers, $bodyContent);

            // Send the HTTP request
            $response = $this->httpClient->sendRequest($request);

            // Handle the HTTP response
            $responseData = $this->handleResponse($response);

            // Append key-value pairs from $appendData to the response array
            return array_merge($responseData, $appendData);
        } catch (RequestException $e) {
            // Log or handle the error in a more detailed way
            return ['status' => false, 'message' => 'Request failed', 'error_details' => $e->getMessage()];
        }
    }

    /**
     * Initialize a Chapa transaction.
     *
     * @param array $body The request body for transaction initialization.
     * @return array The decoded response data.
     *
     * @throws ClientExceptionInterface if the HTTP request fails.
     *
     * @link https://developer.chapa.co/docs/accept-payments/ Chapa API Documentation - Transaction Initialization
     */
    public function transactionInitialize(array $body): array
    {
        // Define allowed fields
        $allowedFields = [
            'amount', 'currency', 'email', 'first_name', 'last_name', 'phone_number',
            'tx_ref', 'callback_url', 'return_url', 'customization'
        ];

        // Validate required fields and refuse other values
        foreach ($body as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                throw new \InvalidArgumentException("Invalid field provided: $field");
            }
        }

        // Validate required fields
        $requiredFields = ['amount', 'currency'];

        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        // Generate unique tx_ref if missing
        $txRefPrefix = config('chapa.tx_ref_prefix', '');
        if (!isset($body['tx_ref'])) {
            $uniqueId = uniqid();
            $body['tx_ref'] = $txRefPrefix . $uniqueId;
        } else {
            // Prefix existing tx_ref if config('chapa.tx_ref_prefix') is not empty
            $body['tx_ref'] = $txRefPrefix . $body['tx_ref'];
        }

        // Validate email if present
        if (isset($body['email'])) {
            if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("Invalid email address");
            }
        }

        // Validate currency values
        $allowedCurrencies = ['ETB', 'USD'];

        if (!in_array($body['currency'], $allowedCurrencies)) {
            throw new \InvalidArgumentException("Invalid currency. Allowed values are: " . implode(', ', $allowedCurrencies));
        }

        // Validate customization array
        if (isset($body['customization']) && is_array($body['customization'])) {
            foreach ($body['customization'] as $key => $value) {
                // Ensure each key in customization array is valid
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
                    throw new \InvalidArgumentException("Invalid key in customization array: $key");
                }
            }
        }

        // Continue with sending the request if validation passes
        return $this->sendRequest("/transaction/initialize", "POST", $body, ['tx_ref' => $body['tx_ref']]);
    }

    /**
     * Verify a Chapa transaction.
     *
     * @param string $tx_ref The transaction reference.
     * @return array The decoded response data.
     *
     * @throws ClientExceptionInterface if the HTTP request fails.
     *
     * @link https://developer.chapa.co/docs/verify-payments/ Chapa API Documentation - Transaction Verification
     */
    public function transactionVerify(string $tx_ref): array
    {
        // Validate $tx_ref
        if (empty($tx_ref)) {
            throw new \InvalidArgumentException("Invalid or empty transaction reference (tx_ref).");
        }

        // Continue with sending the request if validation passes
        return $this->sendRequest("/transaction/verify/" . $tx_ref, "GET");
    }

    /**
     * Handle the HTTP response.
     *
     * @param ResponseInterface $response The HTTP response.
     * @return array The decoded response data.
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $contentType = $response->getHeaderLine("Content-Type");
        $data = [];

        // Decode JSON response if Content-Type is application/json
        if (str_starts_with($contentType, "application/json")) {
            $data = json_decode((string) $response->getBody(), true);
        }

        return $data;
    }
}
