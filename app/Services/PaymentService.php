<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $config;

    public function __construct()
    {

        $this->config = config('somx');
    }

    public function getAccessToken()
    {
        try {
            $response = Http::asForm()->post($this->config['endpoints']['verify'], [
                'client_id' => $this->config['credentials']['client_id'],
                'client_secret' => $this->config['credentials']['client_secret'],
            ]);

            if ($response->failed()) {
                Log::error('Payment API Auth Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Payment API authentication failed');
            }

            $json = $response->json();
            Log::info('Raw Auth Response', ['response' => $json]);

            $data = $json['data'] ?? null;

            if (is_string($data)) {
                $decodedData = json_decode($data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to decode nested JSON: '.json_last_error_msg());
                }
                $data = $decodedData;
            }

            if (isset($data['access_token'])) {
                return $data['access_token'];
            }

            if (isset($data['data']['access_token'])) {
                return $data['data']['access_token'];
            }

            throw new \Exception('Access token not found in response');
        } catch (\Exception $e) {
            Log::error('Payment Service Error (getAccessToken)', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createTransaction(array $transactionData)
    {
        try {
            $token = $this->getAccessToken();

            // Verify the transaction endpoint URL
            $endpoint = $this->config['endpoints']['transaction'];
            if (! filter_var($endpoint, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid transaction endpoint URL: {$endpoint}");
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ])->post($endpoint, $transactionData);

            if ($response->status() === 404) {
                Log::error('Transaction endpoint not found', [
                    'endpoint' => $endpoint,
                    'status' => 404,
                ]);
                throw new \Exception('Payment gateway endpoint not found (404)');
            }

            // Handle other errors
            if ($response->failed()) {
                $errorData = $response->json() ?? ['body' => $response->body()];
                Log::error('Transaction Creation Failed', array_merge(
                    ['status' => $response->status()],
                    $errorData
                ));
                throw new \Exception('Payment failed: '.($errorData['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            Log::info('Transaction Response', ['data' => $responseData]);

            return $responseData['data']['approvedUrl'] ?? $responseData['data'] ?? null;
        } catch (\Exception $e) {
            Log::error('Transaction Error', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint ?? 'not-set',
                'payload' => $transactionData,
            ]);
            throw $e;
        }
    }

    public function verifyTransaction($transactionId)
    {
        try {
            $token = $this->getAccessToken();

            // Verify the transaction endpoint URL
            $endpoint = $this->config['endpoints']['transaction-verify'];
            if (! filter_var($endpoint, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid transaction endpoint URL: {$endpoint}");
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ])->get($endpoint.'/'.$transactionId);

            if ($response->status() === 404) {
                Log::error('Transaction endpoint not found', [
                    'endpoint' => $endpoint,
                    'status' => 404,
                ]);
                throw new \Exception('Payment gateway endpoint not found (404)');
            }

            // Handle other errors
            if ($response->failed()) {
                $errorData = $response->json() ?? ['body' => $response->body()];
                Log::error('Transaction Creation Failed', array_merge(
                    ['status' => $response->status()],
                    $errorData
                ));
                throw new \Exception('Payment failed: '.($errorData['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            Log::info('Transaction Response', ['data' => $responseData]);

            return $responseData['data'] ?? null;
        } catch (\Exception $e) {
            Log::error('Transaction Error', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint ?? 'not-set',
            ]);
            throw $e;
        }
    }
}
