<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('somx') ?? [
            'endpoints' => [
                'verify' => 'https://pay.somxchange.com/merchant/api/verify',
                'transaction' => 'https://pay.somxchange.com/merchant/api/transaction-info',
                'transaction-verify' => 'https://pay.somxchange.com/merchant/api/verify-transaction',
            ],
            'credentials' => [
                'client_id' => env('SOMX_CLIENT_ID'),
                'client_secret' => env('SOMX_CLIENT_SECRET'),
            ],
        ];
    }

    public function getAccessToken()
    {
        try {
            $response = Http::withOptions([
                'verify' => true,
                'timeout' => 20,
                'connect_timeout' => 10,
            ])->asForm()->post($this->config['endpoints']['verify'], [
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
                    throw new \Exception('Failed to decode nested JSON: ' . json_last_error_msg());
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
            $this->validateTransactionPayload($transactionData);
            $token = $this->getAccessToken();

            // Verify the transaction endpoint URL
            $endpoint = $this->config['endpoints']['transaction'];
            if (! filter_var($endpoint, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid transaction endpoint URL: {$endpoint}");
            }

            Log::info('Payment API Request (transaction)', [
                'endpoint' => $endpoint,
                'auth' => 'Bearer ' . substr((string) $token, 0, 6) . '…',
                'payload' => $transactionData,
            ]);
            $response = Http::withOptions([
                'verify' => true,
                'timeout' => 20,
                'connect_timeout' => 10,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($endpoint, $transactionData);

            Log::info('Payment API Response (transaction)', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);
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
                throw new \Exception('Payment failed: ' . ($errorData['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            Log::info('Transaction Response Parsed', ['data' => $responseData]);

            $data = $responseData['data'] ?? $responseData;
            $approved = is_array($data) ? ($data['approvedUrl'] ?? $data['approved_url'] ?? null) : null;
            $txnId = is_array($data) ? ($data['transactionId'] ?? $data['transaction_id'] ?? null) : null;
            $code = is_array($data) ? ($data['responseCode'] ?? $data['response_code'] ?? null) : null;
            $status = is_array($data) ? ($data['status'] ?? $data['transactionStatus'] ?? $data['state'] ?? null) : null;
            Log::info('Transaction Parsed Fields', [
                'approved_url' => $approved,
                'transaction_id' => $txnId,
                'response_code' => $code,
                'status' => $status,
            ]);

            return [
                'approved_url' => $approved,
                'transaction_id' => $txnId,
                'raw' => $responseData,
            ];
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

            Log::info('Payment API Request (verify)', [
                'endpoint' => $endpoint,
                'auth' => 'Bearer ' . substr((string) $token, 0, 6) . '…',
                'transaction_id' => $transactionId,
            ]);
            $response = Http::withOptions([
                'verify' => true,
                'timeout' => 20,
                'connect_timeout' => 10,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($endpoint . '/' . $transactionId);

            Log::info('Payment API Response (verify)', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);
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
                throw new \Exception('Payment failed: ' . ($errorData['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            Log::info('Transaction Verify Parsed', ['data' => $responseData]);
            $code = data_get($responseData, 'data.responseCode', data_get($responseData, 'responseCode', data_get($responseData, 'response_code')));
            $status = strtolower((string) data_get($responseData, 'data.status', data_get($responseData, 'status')));
            Log::info('Transaction Verify Fields', [
                'transaction_id' => $transactionId,
                'response_code' => $code,
                'status' => $status,
            ]);

            return $responseData['data'] ?? null;
        } catch (\Exception $e) {
            Log::error('Transaction Error', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint ?? 'not-set',
            ]);
            throw $e;
        }
    }

    private function validateTransactionPayload(array $payload): void
    {
        $phone = (string) ($payload['phone'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);
        $currency = (string) ($payload['currency'] ?? '');
        $success = (string) ($payload['successUrl'] ?? '');
        $cancel = (string) ($payload['cancelUrl'] ?? '');
        $order = (array) ($payload['order_info'] ?? []);
        if (! preg_match('/^\\+[1-9]\\d{1,14}$/', $phone)) {
            throw new \Exception('Invalid phone format, must be E.164');
        }
        if ($amount < 0.01) {
            throw new \Exception('Invalid amount, must be at least 0.01');
        }
        if ($currency !== 'USD') {
            throw new \Exception('Unsupported currency');
        }
        if (! filter_var($success, FILTER_VALIDATE_URL) || ! filter_var($cancel, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid callback URLs');
        }
        if (! isset($order['item_name']) || ! isset($order['order_no'])) {
            throw new \Exception('Missing order info');
        }
    }
}
