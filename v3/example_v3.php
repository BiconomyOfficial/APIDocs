<?php
/**
 * Biconomy V3 Open API — PHP example
 */

class BiconomyV3
{
    private string $apiKey;
    private string $secretKey;
    private string $baseUrl;
    private int $recvWindow;

    public function __construct(string $apiKey, string $secretKey, string $baseUrl = 'https://api.biconomy.com', int $recvWindow = 5000)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->recvWindow = $recvWindow;
    }

    // ---------- signing ----------

    private function nowMs(): int
    {
        return (int) floor(microtime(true) * 1000);
    }

    private function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secretKey);
    }

    /**
     * Build sign payload for GET/DELETE: sort keys alphabetically, raw values, append &timestamp=
     */
    private function buildQueryPayload(array $params, int $ts): string
    {
        $params = array_filter($params, fn($v) => $v !== null);
        if (!$params) {
            return 'timestamp=' . $ts;
        }
        ksort($params);
        $parts = [];
        foreach ($params as $k => $v) {
            // Use raw values, NOT urlencoded — the signed string is built that way on the server.
            $parts[] = $k . '=' . $v;
        }
        return implode('&', $parts) . '&timestamp=' . $ts;
    }

    /**
     * Build sign payload for POST: raw body + &timestamp=
     */
    private function buildBodyPayload(string $body, int $ts): string
    {
        if ($body === '') {
            return 'timestamp=' . $ts;
        }
        return $body . '&timestamp=' . $ts;
    }

    private function authHeaders(string $sign, int $ts): array
    {
        return [
            'X-API-KEY: ' . $this->apiKey,
            'X-API-SIGN: ' . $sign,
            'X-API-TIMESTAMP: ' . $ts,
            'X-API-RECV-WINDOW: ' . $this->recvWindow,
        ];
    }

    // ---------- low-level ----------

    private function httpRequest(string $method, string $url, array $headers = [], string $body = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($body !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $resp = curl_exec($ch);
        if ($resp === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('curl error: ' . $err);
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded = json_decode($resp, true);
        if ($decoded === null) {
            throw new \RuntimeException("HTTP $status: $resp");
        }
        return $decoded;
    }

    private function publicGet(string $path, array $params = [])
    {
        $url = $this->baseUrl . $path;
        $params = array_filter($params, fn($v) => $v !== null);
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $this->httpRequest('GET', $url);
    }

    private function signedGet(string $path, array $params = [])
    {
        $params = array_filter($params, fn($v) => $v !== null);
        $ts = $this->nowMs();
        $payload = $this->buildQueryPayload($params, $ts);
        $sign = $this->sign($payload);
        $url = $this->baseUrl . $path;
        if ($params) {
            // Note: server sorts keys & uses raw values when verifying. http_build_query is fine for transport.
            $url .= '?' . http_build_query($params);
        }
        return $this->httpRequest('GET', $url, $this->authHeaders($sign, $ts));
    }

    private function signedPost(string $path, ?array $body = null)
    {
        // IMPORTANT: send the exact same bytes that were signed.
        $raw = $body ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        $ts = $this->nowMs();
        $payload = $this->buildBodyPayload($raw, $ts);
        $sign = $this->sign($payload);
        $headers = $this->authHeaders($sign, $ts);
        $headers[] = 'Content-Type: application/json';
        return $this->httpRequest('POST', $this->baseUrl . $path, $headers, $raw);
    }

    // ---------- public ----------

    public function ping() { return $this->publicGet('/api/v3/ping'); }
    public function serverTime() { return $this->publicGet('/api/v3/time'); }
    public function symbols() { return $this->publicGet('/api/v3/symbols'); }
    public function tickers() { return $this->publicGet('/api/v3/tickers'); }
    public function depth(string $symbol, int $limit = 100)
    {
        return $this->publicGet('/api/v3/depth', ['symbol' => $symbol, 'limit' => $limit]);
    }
    public function trades(string $symbol, int $limit = 100)
    {
        return $this->publicGet('/api/v3/trades', ['symbol' => $symbol, 'limit' => $limit]);
    }
    public function klines(string $symbol, string $interval, int $startMs, int $endMs, int $limit = 100)
    {
        return $this->publicGet('/api/v3/klines', [
            'symbol' => $symbol, 'interval' => $interval,
            'startTime' => $startMs, 'endTime' => $endMs, 'limit' => $limit,
        ]);
    }

    // ---------- private ----------

    public function assets() { return $this->signedGet('/api/v3/account/assets'); }

    public function openOrders(?string $symbol = null, int $page = 1, int $pageSize = 10)
    {
        return $this->signedGet('/api/v3/trade/openOrders', [
            'symbol' => $symbol, 'page' => $page, 'pageSize' => $pageSize,
        ]);
    }

    public function historyOrders(?string $symbol = null, ?int $startMs = null, ?int $endMs = null, int $page = 1, int $pageSize = 10)
    {
        return $this->signedGet('/api/v3/trade/historyOrders', [
            'symbol' => $symbol, 'startTime' => $startMs, 'endTime' => $endMs,
            'page' => $page, 'pageSize' => $pageSize,
        ]);
    }

    public function myTrades(?string $symbol = null, ?int $startMs = null, ?int $endMs = null, int $page = 1, int $pageSize = 10)
    {
        return $this->signedGet('/api/v3/trade/myTrades', [
            'symbol' => $symbol, 'startTime' => $startMs, 'endTime' => $endMs,
            'page' => $page, 'pageSize' => $pageSize,
        ]);
    }

    public function orderTrades(int $orderId, int $page = 1, int $pageSize = 10)
    {
        return $this->signedGet('/api/v3/trade/orderTrades', [
            'orderId' => $orderId, 'page' => $page, 'pageSize' => $pageSize,
        ]);
    }

    /**
     * @param string $side  BUY / SELL
     * @param string $type  LIMIT / MARKET
     * @param string $amount  for MARKET BUY -> quote-asset amount; otherwise -> base-asset amount
     * @param string|null $price  required for LIMIT
     */
    public function placeOrder(string $symbol, string $side, string $type, string $amount, ?string $price = null)
    {
        $body = ['symbol' => $symbol, 'side' => $side, 'type' => $type, 'amount' => $amount];
        if ($type === 'LIMIT') {
            $body['price'] = $price;
        }
        return $this->signedPost('/api/v3/trade/order', $body);
    }

    public function batchOrders(string $symbol, array $orders)
    {
        return $this->signedPost('/api/v3/trade/batchOrders', ['symbol' => $symbol, 'orders' => $orders]);
    }

    public function cancelOrder(string $symbol, ?int $orderId = null)
    {
        $body = ['symbol' => $symbol];
        if ($orderId) {
            $body['orderId'] = $orderId;
        }
        return $this->signedPost('/api/v3/trade/cancelOrder', $body);
    }

    public function cancelBatchOrders(string $symbol, array $orderIds)
    {
        return $this->signedPost('/api/v3/trade/cancelBatchOrders', [
            'symbol' => $symbol, 'orderIds' => $orderIds,
        ]);
    }
}


// ---------- demo ----------

$apiKey = 'YOUR_API_KEY';
$secretKey = 'YOUR_SECRET_KEY';

$client = new BiconomyV3($apiKey, $secretKey);

// public
echo "ping: " . json_encode($client->ping()) . PHP_EOL;
echo "time: " . json_encode($client->serverTime()) . PHP_EOL;
echo "depth: " . json_encode($client->depth('BTC_USDT', 5)) . PHP_EOL;

// private (replace with real credentials)
// echo "assets: " . json_encode($client->assets()) . PHP_EOL;
// echo "open: " . json_encode($client->openOrders('BTC_USDT')) . PHP_EOL;
// echo "place limit: " . json_encode($client->placeOrder('BTC_USDT','BUY','LIMIT','0.001','21000')) . PHP_EOL;
// echo "place market buy (1 USDT): " . json_encode($client->placeOrder('BTC_USDT','BUY','MARKET','1')) . PHP_EOL;
// echo "cancel one: " . json_encode($client->cancelOrder('BTC_USDT', 32865)) . PHP_EOL;
// echo "cancel batch: " . json_encode($client->cancelBatchOrders('BTC_USDT', [32865, 32866])) . PHP_EOL;
