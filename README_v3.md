# Biconomy Open API V3


 \-  [**Getting Started**](#Getting-Started)

 \- [**Apply API Key**](#Apply-api-key)

 \- [**Interface Call Mode Description**](#Interface-call-mode-description)

 \- [**Server**](#Server)



\- [**REST API**](#rest-api)

 \- [**Access URL**](#Access-url)

 \- [**Request Interaction**](#Request-Interaction)

 \- [**Error Codes**](#Error-Codes)

 \- [**Attention**](#Attention)

 \- [**Ping**](#Ping)

 \- [**Server Time**](#Server-Time)

 \- [**Get Symbols**](#Get-Symbols)

 \- [**Get Tickers**](#Get-Tickers)

 \- [**Get Depth Information**](#Get-Depth-Information)

 \- [**Get Recent Trades**](#Get-Recent-Trades)

 \- [**Get K-line Info**](#Get-K-line-Info)

 \- [**Signature Authentication**](#Signature-Authentication)

 \- [**API Key Permissions**](#API-Key-Permissions)

 \- [**Get User Assets**](#Get-User-Assets)

 \- [**Place Order**](#Place-Order)

 \- [**Batch Place Orders**](#Batch-Place-Orders)

 \- [**Cancel an Order**](#Cancel-an-Order)

 \- [**Bulk Cancel Orders by Ids**](#Bulk-Cancel-Orders-by-Ids)

 \- [**Query Open Orders**](#Query-Open-Orders)

 \- [**Query History Orders**](#Query-History-Orders)

 \- [**Query My Trades**](#Query-My-Trades)

 \- [**Query Order Trades**](#Query-Order-Trades)



\- [**WebSocket Market Streams**](#WebSocket-Market-Streams)

 \- [**Depth Stream**](#Depth-Stream)

 \- [**Kline Stream**](#Kline-Stream)

 \- [**Deal Stream**](#Deal-Stream)

 \- [**Last Price Stream**](#Last-Price-Stream)

 \- [**24hour Market State Stream**](#24hour-Market-State-Stream)

 \- [**Today Market State Stream**](#Today-Market-State-Stream)





## Getting Started

**Welcome to the Biconomy V3 Open API documentation. V3 is the new generation of the Biconomy open API, providing an easy-to-use, Binance-style HMAC-SHA256 signed REST interface for traders to operate in spot trading markets through automated 3rd-party trading applications.**



### Apply API Key

In a signed-up account of **[biconomy] (https://www.biconomy.com)**, the user can create an API Key in [API Management], and obtain a randomly generated `apiKey` and `secretKey` after creation. The Key is used by your application's authentication when trading.



**Please do NOT disclose the API Key and Secret Key to protect your assets. It is recommended that users bind IP addresses for the API. Each key is bound to a maximum of 4 IPs, separated by commas. Each API key has fine-grained permission scopes (asset/spot/transfer/withdrawal × read/write). Only request the scopes your application needs.**



### Interface call mode description



- REST API


Provide market query, balance query, currency transaction and order management functions. Users are recommended to use the REST API for account balance query, currency transaction and order management.


### server

- The biconomy server runs in Tokyo. In order to minimize the delay of API access, it is recommended to use a server with smooth communication with Tokyo.

## REST API

### Access URL

\- Main URL: [https://api.biconomy.com](https://api.biconomy.com)


### Request interaction

#### Intro

REST API V3 provides market inquiry, balance inquiry, currency trading and order management functions.

All requests are based on the HTTPS protocol. Request body for `POST` interfaces is **JSON**:


\- **content-type: application/json**

Public endpoints take their parameters from the query string. Private endpoints follow the rules below:

\- `GET` / `DELETE` private endpoints: parameters in the query string
\- `POST` private endpoints: parameters in the JSON body

#### Unified Response

All responses use the unified envelope below:

```
{
  "code": 0,
  "message": "Success",
  "data": <any>
}
```

`code = 0` means success. A non-zero `code` indicates a business error. The HTTP status code may be `200 / 401 / 403 / 429`; clients should always rely on `code` for business-level success/failure.

#### Error Codes

| Error code | Description                                       |
| ---------- | ------------------------------------------------- |
| 0          | Success                                           |
| 400        | Bad Request                                       |
| 401        | Unauthorized                                      |
| 401001     | Missing required headers                          |
| 401002     | Invalid timestamp                                 |
| 401003     | Invalid recv_window                               |
| 401004     | recv_window exceeds maximum                       |
| 401005     | Timestamp is ahead of server time                 |
| 401006     | Request has expired                               |
| 401007     | Invalid API key                                   |
| 401008     | Signature verification failed                     |
| 403        | Forbidden                                         |
| 403001     | API key is disabled                               |
| 403002     | API key has expired                               |
| 403003     | IP not in whitelist                               |
| 403004     | Permission denied                                 |
| 429        | Too Many Requests                                 |
| 500        | Internal Server Error                             |
| 400001     | Invalid side                                      |
| 400002     | Invalid order type                                |
| 400003     | Symbol not found                                  |
| 400004     | Invalid amount                                    |
| 400005     | Invalid price                                     |
| 400006     | Batch order count exceeds maximum (20)            |
| 400007     | Invalid interval                                  |
| 400008     | Batch cancel count exceeds maximum (20)           |
| 400009     | Invalid symbol                                    |
| 400100     | Insufficient balance                              |
| 400101     | Amount too small                                  |
| 400102     | No enough liquidity                               |
| 400103     | Order not found                                   |
| 400104     | User not match                                    |
| 400105     | Symbol trading is disabled                        |
| 400106     | Base asset trading is disabled                    |
| 400107     | Quote asset trading is disabled                   |
| 400108     | Price precision exceeds limit                     |
| 400109     | Amount precision exceeds limit                    |
| 400110     | Price below minimum tick size                     |
| 400111     | Amount below minimum                              |
| 400112     | Price out of allowed range                        |
| 400113     | Identity verification required                    |
| 400114     | User trading is forbidden                         |
| 400115     | Trading is forbidden in your country              |
| 400116     | Symbol trading has not started                    |
| 400117     | Symbol trading has ended                          |
| 400118     | Symbol not allowed for this API key               |



### Attention

\- All private interface requests must add the signature headers `X-API-KEY`, `X-API-SIGN`, `X-API-TIMESTAMP` (and optionally `X-API-RECV-WINDOW`) to the request header. See [Signature Authentication](#Signature-Authentication) below.
\- Public interfaces are rate-limited by IP, private interfaces are rate-limited by user.

## Market API public interface

### Ping

GET /api/v3/ping

Frequency limit: 5 req/s per IP



#### example

```
Request:

GET /api/v3/ping



Response:

{
  "code": 0,
  "message": "Success",
  "data": null
}
```

---



### Server Time

GET /api/v3/time

Frequency limit: 5 req/s per IP



#### example

```
Request:

GET /api/v3/time



Response:

{
  "code": 0,
  "message": "Success",
  "data": {
    "serverTime": 1747100000000
  }
}
```

#### respond

```
serverTime: server time in milliseconds
```

---



### Get Symbols

GET /api/v3/symbols

Frequency limit: 20 req/s per IP



#### example

```
Request:

GET /api/v3/symbols



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "symbol": "BTC_USDT",
      "baseAsset": "BTC",
      "quoteAsset": "USDT",
      "pricePrecision": 2,
      "quantityPrecision": 6,
      "status": 1,
      "tickSize": "0.01",
      "minQuantity": "0.0001",
      "minQuantityBase": "0.0001",
      "minQuantityQuote": "1",
      "limitTakerFee": "0.001",
      "limitMakerFee": "0.001",
      "marketTakerFee": "0.001"
    }
  ]
}
```

#### respond

```
symbol: trading pair, e.g. BTC_USDT

baseAsset: base asset

quoteAsset: quote asset

pricePrecision: decimal places of price

quantityPrecision: decimal places of quantity

status: symbol status

tickSize: minimum price step

minQuantity: minimum order quantity

minQuantityBase: minimum order quantity (base asset)

minQuantityQuote: minimum order quantity (quote asset)

limitTakerFee: limit-order taker fee rate

limitMakerFee: limit-order maker fee rate

marketTakerFee: market-order taker fee rate
```

---



### Get Tickers

GET /api/v3/tickers

Frequency limit: 20 req/s per IP



#### example

```
Request:

GET /api/v3/tickers



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "symbol": "BTC_USDT",
      "last": "21446.34",
      "high": "21500.00",
      "low": "21400.00",
      "vol": "123.456",
      "deal": "2645432.12",
      "change": "0.0023",
      "buy": "21446.00",
      "sell": "21446.50"
    }
  ]
}
```

#### respond

```
symbol: trading pair

last: last price

high: 24h highest price

low: 24h lowest price

vol: 24h base-asset volume

deal: 24h quote-asset volume

change: 24h price change ratio

buy: best bid price

sell: best ask price
```

---



### Get Depth Information

GET /api/v3/depth

Frequency limit: 20 req/s per IP



#### parameter

| Parameter | required | type   | Description           |
| --------- | -------- | ------ | --------------------- |
| symbol    | true     | string | BTC_USDT              |
| limit     | false    | int    | default 100, max 200  |



#### example

```
Request:

GET /api/v3/depth?symbol=BTC_USDT&limit=5



Response:

{
  "code": 0,
  "message": "Success",
  "data": {
    "asks": [
      ["21446.50","0.12"],
      ["21446.55","0.30"]
    ],
    "bids": [
      ["21446.00","0.45"],
      ["21445.90","0.10"]
    ]
  }
}
```

#### respond

```
asks: ask[price, amount]

bids: bid[price, amount]
```

---



### Get Recent Trades

GET /api/v3/trades

Frequency limit: 20 req/s per IP


#### parameter

| Parameter | required | type   | Description           |
| --------- | -------- | ------ | --------------------- |
| symbol    | true     | string | BTC_USDT              |
| limit     | false    | int    | default 100, max 500  |



#### example

```
Request:

GET /api/v3/trades?symbol=BTC_USDT&limit=2



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "id": 848000900,
      "time": 1660924893170,
      "price": "21446.35",
      "amount": "0.2",
      "type": "BUY"
    },
    {
      "id": 848000899,
      "time": 1660924893169,
      "price": "21446.34",
      "amount": "0.03",
      "type": "BUY"
    }
  ]
}
```

#### Response

```
id: trade id

time: trade timestamp in milliseconds

price: trade price

amount: trade base-asset amount

type: BUY / SELL (taker side)
```

---



### Get K-line Info

GET /api/v3/klines

Frequency limit: 10 req/s per IP



#### parameter

| Parameter | required | type   | Description                                                                  |
| --------- | -------- | ------ | ---------------------------------------------------------------------------- |
| symbol    | true     | string | BTC_USDT                                                                     |
| interval  | true     | string | 1s, 1m, 3m, 5m, 15m, 30m, 1h, 4h, 8h, 12h, 1d, 1w, 1M                        |
| startTime | true     | int64  | start time, in milliseconds                                                  |
| endTime   | true     | int64  | end time, in milliseconds                                                    |
| limit     | false    | int    | default 100, max 500                                                         |


#### example

```
Request:

GET /api/v3/klines?symbol=BTC_USDT&interval=1m&startTime=1660924800000&endTime=1660925400000&limit=2



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    [
      1660924800000,
      "21444.54",
      "21447.87",
      "21437.21",
      "21445.84",
      1660924860000,
      "21.74",
      "466178.26"
    ],
    [
      1660924860000,
      "21445.84",
      "21452.00",
      "21443.10",
      "21450.10",
      1660924920000,
      "18.90",
      "405291.55"
    ]
  ]
}
```

#### Response

Each kline is an array. Field order:

```
[0] openTime  : kline open time in milliseconds
[1] open      : open price
[2] high      : high price
[3] low       : low price
[4] close     : close price
[5] closeTime : kline close time in milliseconds
[6] baseVol   : base-asset volume
[7] quoteVol  : quote-asset volume
```

---


# Trading API private interface



- All private interface requests must be authenticated by signature; rate limits are applied **per user**.



## Signature Authentication



- All private requests must include the following HTTP headers:

| Header              | Required | Description                                            |
| ------------------- | -------- | ------------------------------------------------------ |
| `X-API-KEY`         | true     | Your API key                                           |
| `X-API-SIGN`        | true     | HMAC-SHA256 signature (hex, lowercase)                 |
| `X-API-TIMESTAMP`   | true     | Request timestamp in milliseconds                      |
| `X-API-RECV-WINDOW` | false    | Receive window in ms. Default 5000, max 60000          |

- Timestamp must satisfy: `serverTime - X-API-TIMESTAMP <= recvWindow` and `X-API-TIMESTAMP - serverTime <= 1000`.
- The signature is `HMAC_SHA256(secretKey, payload)`, hex-encoded, lowercase.

### Building the payload

For `GET` / `DELETE` requests:

1. Take all query parameters.
2. Sort the keys in ascending alphabetical order.
3. Concatenate them as `key=value&key=value...` using the raw values (no URL-encoding inside the signed string).
4. Append `&timestamp=<X-API-TIMESTAMP>` at the end. If there are no query params, the payload is simply `timestamp=<X-API-TIMESTAMP>`.

For `POST` requests:

1. Use the raw JSON body bytes exactly as they are sent.
2. Append `&timestamp=<X-API-TIMESTAMP>`. If the body is empty, the payload is `timestamp=<X-API-TIMESTAMP>`.

> Note: the raw body bytes are signed, so the body sent in the HTTP request must match the bytes used to compute the signature byte-for-byte (same field order, same whitespace, same escapes).



### example



```
secretKey = "mySecretKey"

# GET example
Query string : symbol=BTC_USDT&limit=10
Timestamp    : 1747100000000

Sorted payload : limit=10&symbol=BTC_USDT&timestamp=1747100000000
Signature     : HMAC_SHA256(secretKey, payload)
                = "9f5b8c1d..."   (hex, lowercase)

Headers:
X-API-KEY: <apiKey>
X-API-SIGN: 9f5b8c1d...
X-API-TIMESTAMP: 1747100000000


# POST example
Body         : {"symbol":"BTC_USDT","side":"BUY","type":"LIMIT","amount":"0.001","price":"21000"}
Timestamp    : 1747100000000

Payload      : {"symbol":"BTC_USDT","side":"BUY","type":"LIMIT","amount":"0.001","price":"21000"}&timestamp=1747100000000
Signature    : HMAC_SHA256(secretKey, payload)
```



---


## API Key Permissions

Every API key carries a `permissions` JSON object with the following scopes:

| Scope            | Required for                                              |
| ---------------- | --------------------------------------------------------- |
| `asset.read`     | `/api/v3/account/assets`                                  |
| `spot.read`      | `/api/v3/trade/openOrders`, `/historyOrders`, `/myTrades`, `/orderTrades` |
| `spot.write`     | `/api/v3/trade/order`, `/batchOrders`, `/cancelOrder`, `/cancelBatchOrders` |
| `transfer.read`  | reserved                                                  |
| `transfer.write` | reserved                                                  |
| `withdrawal.read`  | reserved                                                |
| `withdrawal.write` | reserved                                                |

If the API key is bound to a specific symbol whitelist, trading endpoints will reject orders on symbols outside the whitelist with `400118 Symbol not allowed for this API key`.


---


## Get User Assets


GET /api/v3/account/assets

Permission: `asset.read`

Frequency limit: 10 req/s per user



### example


```

Request:

GET /api/v3/account/assets



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "coin": "USDT",
      "available": "10718.74453852",
      "freeze": "0.10999996"
    },
    {
      "coin": "BTC",
      "available": "46.20370336",
      "freeze": "0"
    }
  ]
}

```



### Response

```

coin: asset code

available: available balance

freeze: frozen balance

```


---


## Place Order


POST /api/v3/trade/order

Permission: `spot.write`

Frequency limit: 10 req/s per user



### parameter



| Parameter | required | type   | description                                                                                     |
| --------- | -------- | ------ | ----------------------------------------------------------------------------------------------- |
| symbol    | true     | string | BTC_USDT                                                                                        |
| side      | true     | string | `BUY` / `SELL`                                                                                  |
| type      | true     | string | `LIMIT` / `MARKET`                                                                              |
| amount    | true     | string | **For `MARKET BUY` this is the quote-asset amount (e.g. USDT spent); for all other cases (`MARKET SELL` / `LIMIT BUY` / `LIMIT SELL`) it is the base-asset amount.** |
| price     | required for LIMIT | string | limit price                                                                            |


### example

```

Request:

POST /api/v3/trade/order

Body:
{
  "symbol": "BTC_USDT",
  "side": "BUY",
  "type": "LIMIT",
  "amount": "0.001",
  "price": "21000"
}



Response:

{
  "code": 0,
  "message": "Success",
  "data": {
    "id": 32865,
    "symbol": "BTC_USDT",
    "type": "LIMIT",
    "side": "BUY",
    "status": "NEW",
    "price": "21000",
    "amount": "0.001",
    "left": "0.001",
    "filledAmount": "0",
    "filledTotal": "0",
    "fee": "0",
    "createTime": 1747100000000,
    "updateTime": 1747100000000
  }
}

```



### Response



```

id: order id

symbol: trading pair

type: LIMIT / MARKET

side: BUY / SELL

status: NEW / PARTIALLY_FILLED / FILLED / CANCELLED

price: order price

amount: order amount (for `MARKET BUY`, this is the quote-asset amount you submitted; otherwise it is the base-asset amount)

left: remaining unfilled amount (same unit as `amount`)

filledAmount: filled base-asset amount

filledTotal: filled quote-asset amount

fee: total trade fee

createTime: order create time, in milliseconds

updateTime: order update time, in milliseconds

```


---


## Batch Place Orders


POST /api/v3/trade/batchOrders

Permission: `spot.write`

Frequency limit: 5 req/s per user

Up to 20 sub-orders per request. Only `LIMIT` orders are supported in batch.



### parameter

| Parameter | required | type   | description                                          |
| --------- | -------- | ------ | ---------------------------------------------------- |
| symbol    | true     | string | BTC_USDT                                             |
| orders    | true     | array  | Sub-orders, see below. Length 1 ~ 20.                |

Each sub-order:

| Parameter | required | type   | description                  |
| --------- | -------- | ------ | ---------------------------- |
| side      | true     | string | `BUY` / `SELL`               |
| amount    | true     | string | base-asset amount            |
| price     | true     | string | limit price                  |


### example

```

Request:

POST /api/v3/trade/batchOrders

Body:
{
  "symbol": "BTC_USDT",
  "orders": [
    {"side":"BUY","amount":"0.001","price":"21000"},
    {"side":"SELL","amount":"0.001","price":"22000"}
  ]
}



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "code": 0,
      "message": "Success",
      "order": {
        "id": 32865,
        "symbol": "BTC_USDT",
        "type": "LIMIT",
        "side": "BUY",
        "status": "NEW",
        "price": "21000",
        "amount": "0.001",
        "left": "0.001",
        "filledAmount": "0",
        "filledTotal": "0",
        "fee": "0",
        "createTime": 1747100000000,
        "updateTime": 1747100000000
      }
    },
    {
      "code": 400100,
      "message": "Insufficient balance"
    }
  ]
}

```


### Response

The `data` array preserves the order of the request `orders` array. Each entry has its own `code`, `message` and (on success) `order` field — the entire request returns success even if some sub-orders fail.


---


## Cancel an Order


POST /api/v3/trade/cancelOrder

Permission: `spot.write`

Frequency limit: 10 req/s per user when `orderId` is provided (cancel a single order); 5 req/s per user when `orderId` is omitted (cancel all orders on the symbol)



### parameter



| Parameter | required | type   | description                                                              |
| --------- | -------- | ------ | ------------------------------------------------------------------------ |
| symbol    | true     | string | BTC_USDT                                                                 |
| orderId   | false    | int64  | If set, cancel a single order. If omitted, cancel **all** orders on `symbol`. |



### example


Cancel a single order:

```

Request:

POST /api/v3/trade/cancelOrder

Body:
{
  "symbol": "BTC_USDT",
  "orderId": 32865
}



Response:

{
  "code": 0,
  "message": "Success",
  "data": {
    "id": 32865,
    "symbol": "BTC_USDT",
    "type": "LIMIT",
    "side": "BUY",
    "status": "CANCELLED",
    "price": "21000",
    "amount": "0.001",
    "left": "0.001",
    "filledAmount": "0",
    "filledTotal": "0",
    "fee": "0",
    "createTime": 1747100000000,
    "updateTime": 1747100000100
  }
}

```

Cancel all orders on a symbol (omit `orderId`):

```

Request:

POST /api/v3/trade/cancelOrder

Body:
{
  "symbol": "BTC_USDT"
}



Response:

{
  "code": 0,
  "message": "Success",
  "data": null
}

```


### Response

When a single order is cancelled, the same shape as [Place Order](#Place-Order) is returned (`status` will be `CANCELLED`). When cancelling all orders on a symbol, `data` is `null`.


---


## Bulk Cancel Orders by Ids


POST /api/v3/trade/cancelBatchOrders

Permission: `spot.write`

Frequency limit: 5 req/s per user

Up to 20 order ids per request.



### parameter



| Parameter | required | type    | description              |
| --------- | -------- | ------- | ------------------------ |
| symbol    | true     | string  | BTC_USDT                 |
| orderIds  | true     | int64[] | List of order ids to cancel, length 1 ~ 20 |



### example

```

Request:

POST /api/v3/trade/cancelBatchOrders

Body:
{
  "symbol": "BTC_USDT",
  "orderIds": [32865, 32866, 32867]
}



Response:

{
  "code": 0,
  "message": "Success",
  "data": [32865, 32866]
}

```


### Response

`data` is the list of order ids that were successfully cancelled. Orders not present in the list either did not exist or could not be cancelled (already filled / not belonging to the user).


------


## Query Open Orders


GET /api/v3/trade/openOrders

Permission: `spot.read`

Frequency limit: 10 req/s per user



### parameter



| Parameter | required | type   | description       |
| --------- | -------- | ------ | ----------------- |
| symbol    | false    | string | BTC_USDT          |
| page      | false    | int    | default 1         |
| pageSize  | false    | int    | default 10, max 100 |



### example

```

Request:

GET /api/v3/trade/openOrders?symbol=BTC_USDT&page=1&pageSize=10



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "id": 32871,
      "symbol": "BTC_USDT",
      "type": "LIMIT",
      "side": "BUY",
      "status": "NEW",
      "price": "5.1",
      "amount": "1",
      "left": "1",
      "filledAmount": "0",
      "filledTotal": "0",
      "fee": "0",
      "createTime": 1535544362168,
      "updateTime": 1535544362168
    }
  ]
}

```



### Response



```

id: order id

symbol: trading pair

type: LIMIT (market orders never sit in open orders)

side: BUY / SELL

status: NEW / PARTIALLY_FILLED

price: order price

amount: order amount

left: remaining unfilled amount

filledAmount: filled base-asset amount

filledTotal: filled quote-asset amount

fee: total trade fee so far

createTime: order create time, in milliseconds

updateTime: order update time, in milliseconds

```


------


## Query History Orders


GET /api/v3/trade/historyOrders

Permission: `spot.read`

Frequency limit: 10 req/s per user



### parameter



| Parameter | required | type   | description                          |
| --------- | -------- | ------ | ------------------------------------ |
| symbol    | false    | string | BTC_USDT                             |
| startTime | false    | int64  | start time, milliseconds             |
| endTime   | false    | int64  | end time, milliseconds               |
| page      | false    | int    | default 1                            |
| pageSize  | false    | int    | default 10, max 100                  |



### example

```

Request:

GET /api/v3/trade/historyOrders?symbol=BTC_USDT&startTime=1660924800000&endTime=1660928400000&page=1&pageSize=10



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "id": 32868,
      "symbol": "BTC_USDT",
      "type": "MARKET",
      "side": "BUY",
      "status": "FILLED",
      "price": "0",
      "amount": "1",
      "filledAmount": "0.19607843",
      "filledTotal": "0.999999993",
      "fee": "0.00019607843",
      "createTime": 1535538409189,
      "finishTime": 1535538409189
    }
  ]
}

```



### Response

```

id: order id

symbol: trading pair

type: LIMIT / MARKET

side: BUY / SELL

status: FILLED / CANCELLED

price: order price (0 for market orders)

amount: order amount (for `MARKET BUY`, this is the quote-asset amount; otherwise the base-asset amount)

filledAmount: filled base-asset amount

filledTotal: filled quote-asset amount

fee: total trade fee

createTime: order create time, milliseconds

finishTime: order finish time, milliseconds

```


------


## Query My Trades


GET /api/v3/trade/myTrades

Permission: `spot.read`

Frequency limit: 10 req/s per user



### parameter



| Parameter | required | type   | description                          |
| --------- | -------- | ------ | ------------------------------------ |
| symbol    | false    | string | BTC_USDT                             |
| startTime | false    | int64  | start time, milliseconds             |
| endTime   | false    | int64  | end time, milliseconds               |
| page      | false    | int    | default 1                            |
| pageSize  | false    | int    | default 10, max 100                  |



### example

```

Request:

GET /api/v3/trade/myTrades?symbol=BTC_USDT&page=1&pageSize=10



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "id": 25503,
      "symbol": "BTC_USDT",
      "type": "LIMIT",
      "side": "BUY",
      "role": "TAKER",
      "price": "19.96",
      "amount": "1",
      "total": "19.96",
      "fee": "0.001",
      "orderId": 32730,
      "time": 1535437951751
    }
  ]
}

```



### Response

```

id: trade id

symbol: trading pair

type: LIMIT / MARKET

side: BUY / SELL

role: TAKER / MAKER

price: trade price

amount: trade base-asset amount

total: trade quote-asset amount

fee: trade fee

orderId: order id

time: trade time, milliseconds

```


------


## Query Order Trades


GET /api/v3/trade/orderTrades

Permission: `spot.read`

Frequency limit: 10 req/s per user

Query the trades that belong to a single order.



### parameter



| Parameter | required | type   | description           |
| --------- | -------- | ------ | --------------------- |
| orderId   | true     | int64  | order id              |
| page      | false    | int    | default 1             |
| pageSize  | false    | int    | default 10, max 100   |



### example

```

Request:

GET /api/v3/trade/orderTrades?orderId=32730&page=1&pageSize=10



Response:

{
  "code": 0,
  "message": "Success",
  "data": [
    {
      "id": 25503,
      "role": "TAKER",
      "price": "19.96",
      "amount": "1",
      "total": "19.96",
      "fee": "0.001",
      "orderId": 32730,
      "time": 1535437951751
    }
  ]
}

```



### Response

```

id: trade id

role: TAKER / MAKER

price: trade price

amount: trade base-asset amount

total: trade quote-asset amount

fee: trade fee

orderId: order id

time: trade time, milliseconds

```



## WebSocket Market Streams

- The base endpoint is: **<u>wss://bei.biconomy.com/ws</u>**
- Streams can be subscribed in a single raw stream
- The format subscribing to wss is unified, including method, params and id, {method: "", params: [], id: 5719}, and the parameter of id should not be same in singal individual socket.
- client should send ping packet every 30 seconds, the format of the ping packet is {"method":"server.ping","params":[],"id":5160}, the format of the response packet is {"error": null, "result": "pong", "id": 5160}




### Depth Stream



#### Subscribe Depth Stream

```
Parameters:

​	method: depth.subscribe

​	params:

​		symbol -- BTC_USDT

​		depth -- depth length, currently supported are in the lists, [5, 10, 15, 20, 30, 50, 100]

​		precision -- price precision, currently supported precisions are in the lists, [0, 0.1, 0.01, 0.001, 0.0001, 0.00001, 0.000001, 0.0000001, 0.00000001]
```



```
Example:

​	request:

​		{"method":"depth.subscribe","params":["BTC_USDT",50,"0.01"],"id":2066}

​	response:

​		{"error": null, "result": {"status": "success"}, "id": 2066}
```



#### Unsubscribe Depth Stream

```
Parameters:

​	method: depth.unsubscribe

​	params: []
```



```
Example:

​	request:

​		{"method":"depth.unsubscribe","params":[],"id":2067}

​	response:

​		{"error": null, "result": {"status": "success"}, "id": 2067}
```



#### Depth Push Event

```
Parameters:

​	method: depth.update

​	params:

​		is_full: true or false

​		asks: ask order lists

​		bids: bid order lists

​	id: null
```



```
Example Event:

​	{"method": "depth.update", "params": [true, {"asks": [["21505.92", "0.02"], ["21505.93", "0.09"]...], "bids ": [["21505.21", "0.01"], ["21505.2", "0.22"]...]}, "BTC_USDT"], "id": null}

​	{"method": "depth.update", "params": [false, {"bids": [["21505.93", "0.03"]]}, "BTC_USDT"], "id": null}
```



### Kline Stream



#### Subscribe Kline Stream

```
Parameters:

​	method: kline.subscribe

​	params:

​		symbol -- BTC_USDT

​		interval -- the time interval of kline in seconds, such as 15 minutes set to 900, and 1 day set to 86400
```



```
Example:

​	Request:

​		{"method":"kline.subscribe","params":["BTC_USDT",900],"id":2068}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2068}
```



#### Unsubscribe Kline Stream

```
Parameters:

​	method: kline.unsubscribe

​	params: []
```



```
Example:

​	Request:

​		{"method":"kline.unsubscribe","params":[],"id":2069}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2069}
```



#### Kline Push Event

```
Parameters:

​	method: kline.update

​	params:

​		The fields in params are: timestamp, opening price, closing price, highest price, lowest price, volume, deal, pair name

​	id: null
```



```
Example Event:

​	{"method": "kline.update", "params": [[1660924800, "21444.54", "21445.84", "21447.87", "21437.21", "21.74", "466178.2626", "BTC_USDT"]], " id": null}
```



### Deal Stream



#### Subscribe Deal Stream

```
Parameters:

​	method: deals.subscribe

​	params:

​		symbol -- BTC_USDT
```



```
Example:

​	Request:

​		{"method":"deals.subscribe","params":["BTC_USDT"],"id":2070}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2070}
```



#### Unsubscribe Deal Stream

```
Parameters:

​	method: deals.unsubscribe

​	params: []
```



```
Example:

​	Request:

​		{"method":"deals.unsubscribe","params":[],"id":2071}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2071}
```



#### Deal Push Event

```
Parameters:

​	method: deals.update

​	params:

​		The fields in params are: pair name, amount, timestamp, transaction ID, transaction type, price

​	id: null
```



```
Example Event:

​	{"method": "deals.update", "params": ["BTC_USDT", [{"amount": "0.2", "time": 1660924893.1702139, "id": 848000900, "type": "buy", "price": "21446.35"}, {"amount": "0.03", "time": 1660924893.1698799, "id": 848000899, "type": "buy", "price": "21446.34"}]], "id": null}

​	{"method": "deals.update", "params": ["BTC_USDT", [{"amount": "0.02", "time": 1660924901.0485499, "id": 848002029, "type": "sell", "price": "21445.84"}]], "id": null}
```



### Last Price Stream



#### Subscribe Last Price Stream

```
Parameters:

​	method: price.subscribe

​	params:

​		symbols -- list of the pair, ["BTC_USDT","ETH_USDT"...]
```



```
Example:

​	Request:

​		{"method":"price.subscribe","params":["BTC_USDT","ETH_USDT"],"id":2072}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2072}
```



#### Unsubscribe Last Price Stream

```
Parameters:

​	method: price.unsubscribe

​	params: []
```



```
Example:

​	Request:

​		{"method":"price.unsubscribe","params":[],"id":2073}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2073}
```



#### Last Price Push Event

```
Parameters:

​	method: price.update

​	params:

​		The fields in params are: pair name, last price

​	id: null
```



```
Example Event:

​	{"method": "price.update", "params": ["BTC_USDT", "21446.34"], "id": null}
```



### 24hour Market State Stream



#### Subscribe 24hour Market State Stream

```
Parameters:

​	method: state.subscribe

​	params:

​		symbols -- list of the pair, ["BTC_USDT","ETH_USDT"...]
```



```
Example:

​	Request:

​		{"method":"state.subscribe","params":["BTC_USDT","ETH_USDT"],"id":2074}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2074}
```



#### Unsubscribe 24hour Market State Stream

```
Parameters:

​	method: state.unsubscribe

​	params: []
```



```
Example:

​	Request:

​		{"method":"state.unsubscribe","params":[],"id":2075}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2075}
```



#### 24hour Market State Push Event

```
Parameters:

​	method: state.update

​	params:

​		The fields in params are: pair name, period, last price, opening price, closing price, highest price, lowest price, volume, deal

​	id: null
```



```
Example Event:

​	{"method": "state.update", "params": ["BTC_USDT", {"period": 86400, "last":"23728.7", "open": "23901.05", "close": "23902.11", "high": "24411.64", "low": "23727.7", "volume": "25664.43", "deal": "614923080.4154"}], "id": null}
```



### Today Market State Stream



#### Subscribe Today Market State Stream

```
Parameters:

​	method: today.subscribe

​	params:

​		symbols -- list of the pair, ["BTC_USDT","ETH_USDT"...]
```



```
Example:

​	Request:

​		{"method":"today.subscribe","params":["BTC_USDT","ETH_USDT"],"id":2076}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2076}
```



#### Unsubscribe Today Market State Stream

```
Parameters:

​	method: today.unsubscribe

​	params: []
```



```
Example:

​	Request:

​		{"method":"today.unsubscribe","params":[],"id":2077}

​	Response:

​		{"error": null, "result": {"status": "success"}, "id": 2077}
```



#### Today Market State Push Event

```
Parameters:

​	method: today.update

​	params:

​		The fields in params are: pair name, lowest price, deal, opening price, latest price, highest price, volume

​	id: null
```



```
Example Event:

​	{"method": "today.update", "params": ["BTC_USDT", {"low": "23727.7", "deal": "614923080.4154", "open": "23901.05", "last": "23769.64", "high": "24411.64", "volume": "25664.43"}], "id": null}
```
