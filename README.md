# Biconomy Open API


 \-  [**Getting Started**](#Getting-Started)

 \- [**Apply API Key**](#Apply-api-key)

 \- [**Interface Call Mode Description**](#Interface-call-mode-description)

 \- [**Server**](#Server)



\- [**REST API**](#rest-api)

 \- [**Access URL**](#Access-url)

 \- [**Request Interaction**](#Request-Interaction)

 \- [**Attention**](#Attention)

 \- [**Get Exchange Market Data**](#Get-Exchange-Market-Data)

 \- [**Get Depth Information**](#Get-Depth-Information)

 \- [**Get Recent Trades**](#Get-Recent-Trades)

 \- [**Get K-line Info**](#Get-K-line-Info)

 \- [**Get Pair Info**](#Get-Pair-Info)

 \- [**Signature Authentication**](#Signature-Authentication)

 \- [**Get User Assets**](#Get-User-Assets)

 \- [**Limit Trading**](#Limit-Trading)

 \- [**Market Trading**](#Market-Trading)

 \- [**Cancel an Order**](#Cancel-an-Order)

 \- [**Bulk Cancel Order**](#Bulk-Cancel-Order)

 \- [**Query Order Transaction Interface**](#Query-Order-Transaction-Interface)

 \- [**Query Unfilled Orders**](#Query-unfilled-orders)

 \- [**Query Details Of An Unfilled Order**](#Query-details-of-an-unfilled-order)

 \- [**Querying the Completed Order**](#Querying-the-Completed-Order)

 \- [**Query Details of a Completed Order**](#Query-Details-of-a-Completed-Order)



\- [**WebSocket Market Streams**](#WebSocket-Market-Streams)

 \- [**Depth Stream**](#Depth-Stream)

 \- [**Kline Stream**](#Kline-Stream)

 \- [**Deal Stream**](#Deal-Stream)

 \- [**Last Price Stream**](#Last-Price-Stream)

 \- [**24hour Market State Stream**](#24hour-Market-State-Stream)

 \- [**Today Market State Stream**](#Today-Market-State-Stream)





## Getting Started

**Welcome to the Biconomy documentation center. Biconomy provides an easy-to-use API interface, it allows traders operate in trading markets by using automated 3rd-party trading application.



### Apply API Key

In a signuped account of **[biconomy] (https://www.biconomy.com)**, user can create an API Key in [API Management], and get a set of randomly generated API Key and Secret after creation. The Key, used by in your applications' authentication to trading.



**Please do NOT disclose the API Key and Secret Key to protect your assets. It is recommended that users bind IP addresses for the API. Each key is bound to a maximum of 4 IPs, separated by commas.**



### Interface call mode description



- REST API


Provide market query, balance query, currency transaction, order management functions, users are recommended to use REST API for account balance query, currency transaction and order management


### server

- The biconomy server runs in Tokyo. In order to minimize the delay of API access, it is recommended to use a server with smooth communication with Tokyo.

## REST API

### Access URL

\- Main URL: [https://api.biconomy.com](https://api.biconomy.com) 


### Request interaction

#### Intro

REST API Provide market inquiry, balance inquiry, currency trading, order management functions

All requests are based on the Https protocol, and the content-type in the request header needs to be uniformly set to the form format:


\- **content-type:application/x-www-form-urlencoded**

#### error code

| Error code | Description                                   | Resaon                   |
| ---------- | --------------------------------------------- | ------------------------ |
| 0          | Success                                       |                          |
| 1          | Invalid parameter                             |                          |
| 2           | Internal error                                |                          |
| 3           | Service unavailable                           |                          |
| 4          | Method Not Found                              |                          |
| 5          | Service timeout                               |                          |
| 10         | Insufficient amount                           |                          |
| 11         | Number of transactions is too small           |                          |
| 12         | Insufficient depth                            |                          |
|            |                                               |                          |
| 10005      | Record Not Found                              | X-SITE-ID, Setting Error |
| 10022      | Failed real-name authentication               |                          |
| 10051      | User forbids trading                          |                          |
| 10056      | Less than minimum amount                      |                          |
| 10059      | The asset has not been opened for trading yet |                          |
| 10060      | This trading pair has not yet opened trading  |                          |
| 10062      | Inaccurate amount accuracy                    |                          |



### Attention

\-All interface requests (public and private interfaces) must add the X-SITE-ID field in the header of the Request request. The value of this field is "127". This field is not required for signature verification.
\-If security requires the use of SHA256 and the request URL contains "/private/", please read README_v2.md

## Market API public interface

### Get Exchange Market Data

Get /api/v1/tickers  

Frequency limit: 10 times / s



## example

```
Request:

GET /api/v1/tickers



Response:

{

  "ticker":[

​    {

​      "buy":"0.378",

​      "high":"0.39999995",

​      "last":"0.388",

​      "low":"0.374101",

​      "sell":"0.387",

​      "symbol":"BTC_USDT",

​      "vol":"3485328.1114718"

​    },

​    {

​      "buy":"1924",

​      "high":"1938.84",

​      "last":"1924",

​      "low":"1864.97",

​      "sell":"1926",

​      "symbol":"ETH_USDT",

​      "vol":"2948.19477569"

​    }

  ],

  "timestamp":"1535452275851"

}
```



### respond

```
timestamp: time

buy: BID

high: high price

last: last price

low: low price

sell: ASK

vol: volume (24h volume)
```

---



## Get Depth Information


Get /api/v1/depth 

Frequency limit: 10 times / s



### parameter

| Parameter |  required |  type  |  Description  |
| ------ | ------ | ----- | --------------- |
| symbol | true   | string | BTC_USDT |
| size   | true   | string | 1 ~ 100 |



### example

```
Request:

GET /api/v1/depth?symbol=BTC_USDT



Response:

{

  "asks":[

​    ["0.387","43189.58824906"]

  ],

  "bids":[

​    ["0.378","2078.91534391"]

  ]

}
```



### respond

```
asks : ask[price，amount]

bids : bid[price，amount]
```

---



### Get Recent Trades



Get /api/v1/trades  



Frequency limit: 10 times / s


### parameter

| Parameter |  required |  type  |  Description  |
| ------ | ------ | ----- | --------------- |
| symbol | true   | string | BTC_USDT |
| size   | true   | string | 1 ~ 100 |



### example



```
Request:

GET /api/v1/trades?symbol=BTC_USDT&size=1



Response:

[

  {

​    "amount":"500",

​    "price":"0.401",

​    "side":"sell",

​    "timestamp":"1535507624521"

  },

  {

​    "amount":"442",

​    "price":"0.401",

​    "side":"sell",

​    "timestamp":"1535507612055"

  }

]
```



### Response

```
amount : amount

price : price

side: buy/sell

timestamp: time
```

---



## Get K-line Info



Get /api/v1/kline  



Frequency limit: 10 times / s



### parameter

| Parameter | Description  |
| ------ | --------------- |
| symbol | BTC_USDT |
| type | 1min,5min,15min,30min,hour,day,week |
| size  | 1 ~ 100   |


### example


```
Request:

GET /api/v1/kline?symbol=BTC_USDT&type=1min&size=10



Response:

[

  [

​    1535508060000,

​    "0.401",

​    "0.401",

​    "0.401",

​    "0.401",

​    "0"

  ],

  [

​    1535508120000,

​    "0.401",

​    "0.401",

​    "0.401",

​    "0.401",

​    "0"

  ],

  [

​    1535508180000,

​    "0.401",

​    "0.401",

​    "0.401",

​    "0.401",

​    "0"

  ]

  ...

]
```



### Response


```
1532655300000: time 

2370.16: open

2380: high

2352: low

2367.37: close

17259.83: volume
```

---



## Get Pair Info



Get /api/v1/exchangeInfo



Frequency limit: 10 times / s



### example

```
Request:

GET /api/v1/exchangeInfo



Response:

[

  {

​    "baseAsset":"BTC",

​    "baseAssetPrecision":8,

​    "quoteAsset":"USDT",

​    "quoteAssetPrecision":8,

​    "status":"trading",

​    "symbol":"BTC_USDT"

  },

  {

​    "baseAsset":"ETH",

​    "baseAssetPrecision":8,

​    "quoteAsset":"USDT",

​    "quoteAssetPrecision":8,

​    "status":"trading",

​    "symbol":"ETH_USDT"

  }

]


```



### Response



```
baseAsset: baseAsset

baseAssetPrecision: baseAssetPrecision

quoteAsset: quoteAsset

quoteAssetPrecision: quoteAssetPrecision

status: status

symbol: pair
```



---

# Trading API private interface



- All private interface requests use the *POST* method, and the parameters are submitted in the *form-data*



## Signature Authentication



- All parameters must be verified by signature, and all parameters must be sorted according to the alphabet according to the parameter name



### example



```

para：



'market':'BTC_USDT',

'side':1,

'price':'50',

'amount':'0.02'



Parameter string:
amount=0.02&api_key=apiKey&market=BTC_USDT&price=50&side=1


Note: The secretKey must be generated to generate the MD5 signature. Add the secret_key to the generated string to generate the final string.


Final signature string
:amount=0.02&api_key=apiKey&market=BTC_USDT&price=50&side=1&secret_key=secretKey



MD5 signature：

Use 32bit MD5 encrypted string, the generated encrypted string must be capitalized
Then use the generated encrypted string as the value of sign parameter

Final HTTP request parameter example:
amount=0.02&api_key=apiKey&market=BTC_USDT&price=50&side=1&sign=MD5SignatureString
```



---

## Get User Assets


POST /api/v1/private/user 

Frequency limit: 20 times / s



### example


```

Request:

POST /api/v1/private/user



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "USDT": {

   "available": "10718.74453852",

   "freeze": "0.10999996",

   "other_freeze": "0",

   "recharge_status": 0,

   "trade_status": 1,

   "withdraw_fee": "0.1",

   "withdraw_max": "1000",

   "withdraw_min": "0.001",

   "withdraw_status": 1

  },

  "ETH": {

   "available": "395.196",

   "freeze": "0",

   "other_freeze": "0",

   "recharge_status": 1,

   "trade_status": 1,

   "withdraw_fee": "0.01",

   "withdraw_max": "100000",

   "withdraw_min": "0.001",

   "withdraw_status": 1

  },

  "BTC": {

   "available": "46.20370336",

   "freeze": "0",

   "other_freeze": "0",

   "recharge_status": 0,

   "trade_status": 1,

   "withdraw_fee": "0.1",

   "withdraw_max": "100000",

   "withdraw_min": "1",

   "withdraw_status": 1

  }

 }

}

```



### Response

```

available: available

freeze: freeze

other_freeze: other_freeze（withdraw freeze）

recharge_status: Recharge status. 0 is not rechargeable, 1 is rechargeable

withdraw_fee: withdraw_fee

withdraw_max: withdraw_max

withdraw_min: withdraw_min

withdraw_status: Withdrawal status 0 means no withdrawal, 1 means withdrawal

```


---

## Limit Trading


POST /api/v1/private/trade/limit  


Frequency limit: 20 times / s



### para



| para   | desc       |
| ------ | --------------- |
| market | BTC_USDT  |
| side  | 1 : ASK，2 : BID |
| amount | amount       |
| price | price       |


### example

```

Request:

POST /api/v1/private/trade/limit



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "amount": "1",

  "ctime": 1535537926.246487,

  "deal_fee": "0",

  "deal_money": "0",

  "deal_stock": "0",

  "id": 32865,

  "left": "1",

  "maker_fee": "0.001",

  "market": "BTC_USDT",

  "mtime": 1535537926.246487,

  "price": "10",

  "side": 2,

  "source": "web,1",

  "taker_fee": "0.001",

  "type": 1,

  "user": 670865

 }

}

```



### Response



```

amount: amount

ctime: creat time

deal_fee: deal fee

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

mtime: publish to market time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading type，1:limit，2:market

user: userid

```


---

## Market Trading



POST /api/v1/private/trade/market market trading


Frequency limit: 20 times / s




### para



| para  | desc       |
| ------ | --------------- |
| market | BTC_USDT |
| side  | 1 : ASK，2 : BID |
| amount | amount       |



### example



```

Request: 

POST /api/v1/private/trade/market



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "amount": "1",

  "ctime": 1535538409.189721,

  "deal_fee": "0.00019607843",

  "deal_money": "0.999999993",

  "deal_stock": "0.19607843",

  "id": 32868,

  "left": "7.0000000e-9",

  "maker_fee": "0",

  "market": "BTC_USDT",

  "mtime": 1535538409.189735,

  "price": "0",

  "side": 2,

  "source": "web,1",

  "taker_fee": "0.001",

  "type": 2,

  "user": 670865

 }

}

```



### Response


```

amount: amount

ctime: creat time

deal_fee: deal fee

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

mtime: publish to market time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading type，1:limit，2:market

user: userid

```

---

## Cancel an Order



POST /api/v1/private/trade/cancel 

Frequency limit: 20 times / s



### para



| para    | desc        |
| -------- | ---------- |
| market  | BTC_USDT |
| order_id | order id |



### example



```

Request: 

POST /api/v1/private/trade/cancel



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "amount": "1",

  "ctime": 1535538409.189721,

  "deal_fee": "0.00019607843",

  "deal_money": "0.999999993",

  "deal_stock": "0.19607843",

  "id": 32868,

  "left": "7.0000000e-9",

  "maker_fee": "0",

  "market": "BTC_USDT",

  "mtime": 1535538409.189735,

  "price": "0",

  "side": 2,

  "source": "web,1",

  "taker_fee": "0.001",

  "type": 2,

  "user": 670865

 }

}

```



### Response



```

amount: amount

ctime: creat time

deal_fee: deal fee

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

mtime: publish to market time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading type，1:limit，2:market

user: userid

```


---

## Bulk Cancel Order



POST /api/v1/private/trade/cancel_batch 
The number of orders cancelled in batches each time does not exceed 10.


Frequency limit: 20 times / S




### para



| para     | desc        |
| ---------- | --------- |
| orders_json | [{"market":"XX_USDT","order_id":1},{"market":"XX_USDT","order_id":2}]  |
| sign    | sign   |
| api_key  | api_key |



### example



```

Request:

POST /api/v1/private/trade/cancel_batch



Response:

{

 "code": 0,

 "message": "successful",

 "result": [

  {

   "market": "BTC_USDT",

   "order_id": 458815,

   "result": true

  },

  {

   "market": "BTC_USDT",

   "order_id": 458813,

   "result": true

  },

  {

   "market": "BTC_USDT",

   "order_id": 458812,

   "result": false

  }

 ]

}

```



### Response

```

market: market

order_id: order id

result: true:successful，false:fail)

```


------

## Query Order Transaction Interface



POST /api/v1/private/order/deals 


Frequency limit: 20 times / s


### para



| para   | desc     |
| -------- | ------ |
| order_id | order id |
| offset  | 0  |
| limit  | 1 ~ 100 |



### example

```

# Request 

POST /api/v1/private/order/deals

# Response

{

 "code": 0,

 "message": "successful",

 "result": {

  "limit": 20,

  "offset": 0,

  "records": [

   {

​    "amount": "1",

​    "deal": "19.96",

​    "deal_order_id": 32730,

​    "fee": "0.001",

​    "id": 25503,

​    "price": "19.96",

​    "role": 2,

​    "time": 1535437951.751402,

​    "user": 670865

   }

  ]

 }

}

```



### Response



```

limit: limit

offset: offset

records: records

amount: amount

deal: deal

deal_order_id: deal order id

fee: fee

id: deal id

price: price

role:，1:Maker,2:Taker

time: time

user: user id

```


---

## Query Unfilled Orders


POST /api/v1/private/order/pending  

Frequency limit: 20 times / s


### para



| para   | desc  |
| ------ | ---- |
| market | BTC_USDT  |
| offset | 0  |
| limit | 1 ~ 100 |



### example

```

# Request 

POST /api/v1/private/order/pending

# Response

{

 "code": 0,

 "message": "successful",

 "result": {

  "limit": 10,

  "offset": 0,

  "records": [

   {

​    "amount": "1",

​    "ctime": 1535544362.168106,

​    "deal_fee": "0",

​    "deal_money": "0",

​    "deal_stock": "0",

​    "id": 32871,

​    "left": "1",

​    "maker_fee": "0.001",

​    "market": "BTC_USDT",

​    "mtime": 1535544362.168106,

​    "price": "5.1",

​    "side": 2,

​    "source": "web,1",

​    "taker_fee": "0.001",

​    "type": 1,

​    "user": 670865

   }

  ],

  "total": 1

 }

}

```



### Response



```

amount: amount

ctime: create time

deal_fee: deal fee

deal_money: deal money

deal_stock:deal stock

id: id

left: left

maker_fee: maker fee

market: market

mtime: publish to market time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading time，1:limit，2:market

user: user id
```
------

## Query Details Of An Unfilled Order



POST /api/v1/private/order/pending/detail  

Frequency limit: 20 times / s


### parme



| para    | desc  |
| -------- | ---- |
| market  | BTC_USDT  |
| Order_id | order id |



### example

```

Request:

POST /api/v1/private/order/pending/detail



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "amount": "10",

  "ctime": 1565681852.879657,

  "deal_fee": "0",

  "deal_money": "0",

  "deal_stock": "0",

  "id": 1080,

  "left": "10",

  "maker_fee": "0",

  "market": "BTC_USDT",

  "mtime": 1565681852.879657,

  "price": "1",

  "side": 2,

  "source": "web,127",

  "taker_fee": "0",

  "type": 1,

  "user": 2

 }

}

```



### Response



```

amount: amount

ctime: create time 

deal_fee: deal fee

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

ftime: publish to market time

price: price

side: 1:ASK,2:BID

source: source

taker_fee: taker fee

type: trading type,1:limit,2:market

user: user id
```

---

##  Querying the Completed Order


POST /api/v1/private/order/finished 

Frequency limit: 500 times / s


### para



| para     | desc         |
| ---------- | -------------------- |
| market   | BTC_USDT          |
| start_time | start time  |
| end_time  | end time  |
| offset   | 0          |
| limit   | 1 ~ 100          |
| side    | 1:ASK 2:BID |



### example

```

Request: 

POST /api/v1/private/order/finished


Response:

{

 "code": 0,

 "message": "example",

 "result": {

  "limit": 2,

  "offset": 0,

  "records": [

   {

​    "amount": "1",

​    "ctime": 1535538409.189721,

​    "deal_fee": "0.00019607843",

​    "deal_money": "0.999999993",

​    "deal_stock": "0.19607843",

​    "ftime": 1535538409.189735,

​    "id": 32868,

​    "maker_fee": "0",

​    "market": "BTC_USDT",

​    "price": "0",

​    "side": 2,

​    "source": "web,1",

​    "taker_fee": "0.001",

​    "type": 2,

​    "user": 670865

   },

   {

​    "amount": "10",

​    "ctime": 1535538403.233823,

​    "deal_fee": "0.001109999955",

​    "deal_money": "1.109999955",

​    "deal_stock": "0.21764705",

​    "ftime": 1535538409.189735,

​    "id": 32867,

​    "maker_fee": "0.001",

​    "market": "BTC_USDT",

​    "price": "5.1",

​    "side": 1,

​    "source": "web,1",

​    "taker_fee": "0.001",

​    "type": 1,

​    "user": 670865

   }

  ]

 }

}

```

### Response


``` 
amount: amount

ctime: creat time

deal_fee: deal fee 

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

ftime: finish time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading type，1:limit，2:market

user: user id

```


------

## Query Details Of An Unfilled Order



POST /api/v1/private/order/finished/detail 


Frequency limit: 20 times / s




### para



| para    | desc  |
| -------- | ---- |
| order_id | order id |



### example



```

Request:

POST /api/v1/private/order/finished/detail



Response:

{

 "code": 0,

 "message": "successful",

 "result": {

  "amount": "10",

  "ctime": 1565681925.295415,

  "deal_fee": "0",

  "deal_money": "19.5",

  "deal_stock": "10",

  "ftime": 1565681925.295421,

  "id": 1081,

  "maker_fee": "0",

  "market": "BTC_USDT",

  "price": "2",

  "side": 2,

  "source": "web,127",

  "taker_fee": "0",

  "type": 1,

  "user": 2

 }

}

```



### Response

```
amount: amount

ctime: create time

deal_fee: deal fee

deal_money: deal money

deal_stock: deal stock

id: id

left: left

maker_fee: maker fee

market: market

ftime: finish time

price: price

side: 1:ASK，2:BID

source:source

taker_fee: taker fee

type: trading type，1:limit，2:market

user: user id
```







## WebSocket Market Streams

- The base endpoint is: **<u>wss://bei.biconomy.com/ws</u>**
- Streams can be subscribed in a single raw stream
- The format subscribing to wss is unified, including method, params and id, {method: "", params: [], id: 5719}, and the parameter of id should not be same in singal individual socket.
- client should send ping packet every 3 minutes, the format of the ping packet is {"method":"server.ping","params":[],"id":5160}, the format of the response packet is {"error": null, "result": "pong", "id": 5160}





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

