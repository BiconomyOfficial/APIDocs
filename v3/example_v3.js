/**
 * Biconomy V3 Open API — Node.js example
 */

const crypto = require('crypto');

const BASE_URL = 'https://api.biconomy.com';

class BiconomyV3 {
  constructor(apiKey, secretKey, { baseUrl = BASE_URL, recvWindow = 5000 } = {}) {
    this.apiKey = apiKey;
    this.secretKey = secretKey;
    this.baseUrl = baseUrl.replace(/\/+$/, '');
    this.recvWindow = recvWindow;
  }

  // ---------- signing ----------

  _now() { return Date.now(); }

  _sign(payload) {
    return crypto.createHmac('sha256', this.secretKey).update(payload).digest('hex');
  }

  /** GET/DELETE payload: sort keys alphabetically, raw values, then append &timestamp= */
  _buildQueryPayload(params, ts) {
    const entries = Object.entries(params || {})
      .filter(([, v]) => v !== undefined && v !== null)
      .sort(([a], [b]) => (a < b ? -1 : a > b ? 1 : 0));
    if (!entries.length) return `timestamp=${ts}`;
    const body = entries.map(([k, v]) => `${k}=${v}`).join('&');
    return `${body}&timestamp=${ts}`;
  }

  /** POST payload: raw body bytes + &timestamp= */
  _buildBodyPayload(rawBody, ts) {
    return rawBody ? `${rawBody}&timestamp=${ts}` : `timestamp=${ts}`;
  }

  _authHeaders(sign, ts) {
    return {
      'X-API-KEY': this.apiKey,
      'X-API-SIGN': sign,
      'X-API-TIMESTAMP': String(ts),
      'X-API-RECV-WINDOW': String(this.recvWindow),
    };
  }

  // ---------- low-level ----------

  async _request(method, url, { headers = {}, body } = {}) {
    const res = await fetch(url, { method, headers, body });
    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch { throw new Error(`HTTP ${res.status}: ${text}`); }
    return json;
  }

  _buildQueryString(params) {
    const entries = Object.entries(params || {}).filter(([, v]) => v !== undefined && v !== null);
    if (!entries.length) return '';
    const qs = entries
      .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
      .join('&');
    return `?${qs}`;
  }

  async _publicGet(path, params) {
    return this._request('GET', this.baseUrl + path + this._buildQueryString(params));
  }

  async _signedGet(path, params) {
    const ts = this._now();
    const payload = this._buildQueryPayload(params, ts);
    const sign = this._sign(payload);
    return this._request('GET', this.baseUrl + path + this._buildQueryString(params), {
      headers: this._authHeaders(sign, ts),
    });
  }

  async _signedPost(path, body) {
    // IMPORTANT: the raw body bytes must equal the bytes we sign.
    const raw = body ? JSON.stringify(body) : '';
    const ts = this._now();
    const payload = this._buildBodyPayload(raw, ts);
    const sign = this._sign(payload);
    return this._request('POST', this.baseUrl + path, {
      headers: { ...this._authHeaders(sign, ts), 'Content-Type': 'application/json' },
      body: raw,
    });
  }

  // ---------- public ----------

  ping()        { return this._publicGet('/api/v3/ping'); }
  serverTime()  { return this._publicGet('/api/v3/time'); }
  symbols()     { return this._publicGet('/api/v3/symbols'); }
  tickers()     { return this._publicGet('/api/v3/tickers'); }
  depth(symbol, limit = 100)  { return this._publicGet('/api/v3/depth',  { symbol, limit }); }
  trades(symbol, limit = 100) { return this._publicGet('/api/v3/trades', { symbol, limit }); }
  klines(symbol, interval, startMs, endMs, limit = 100) {
    return this._publicGet('/api/v3/klines', {
      symbol, interval, startTime: startMs, endTime: endMs, limit,
    });
  }

  // ---------- private ----------

  assets() { return this._signedGet('/api/v3/account/assets'); }

  openOrders({ symbol, page = 1, pageSize = 10 } = {}) {
    return this._signedGet('/api/v3/trade/openOrders', { symbol, page, pageSize });
  }

  historyOrders({ symbol, startMs, endMs, page = 1, pageSize = 10 } = {}) {
    return this._signedGet('/api/v3/trade/historyOrders', {
      symbol, startTime: startMs, endTime: endMs, page, pageSize,
    });
  }

  myTrades({ symbol, startMs, endMs, page = 1, pageSize = 10 } = {}) {
    return this._signedGet('/api/v3/trade/myTrades', {
      symbol, startTime: startMs, endTime: endMs, page, pageSize,
    });
  }

  orderTrades(orderId, page = 1, pageSize = 10) {
    return this._signedGet('/api/v3/trade/orderTrades', { orderId, page, pageSize });
  }

  /**
   * @param {'BUY'|'SELL'} side
   * @param {'LIMIT'|'MARKET'} type
   * @param {string} amount  for MARKET BUY: quote-asset amount; otherwise: base-asset amount
   * @param {string} [price] required for LIMIT
   */
  placeOrder(symbol, side, type, amount, price) {
    const body = { symbol, side, type, amount };
    if (type === 'LIMIT') body.price = price;
    return this._signedPost('/api/v3/trade/order', body);
  }

  batchOrders(symbol, orders) {
    return this._signedPost('/api/v3/trade/batchOrders', { symbol, orders });
  }

  cancelOrder(symbol, orderId) {
    const body = { symbol };
    if (orderId) body.orderId = orderId;
    return this._signedPost('/api/v3/trade/cancelOrder', body);
  }

  cancelBatchOrders(symbol, orderIds) {
    return this._signedPost('/api/v3/trade/cancelBatchOrders', { symbol, orderIds });
  }
}


// ---------- demo ----------

async function main() {
  const API_KEY = 'YOUR_API_KEY';
  const SECRET_KEY = 'YOUR_SECRET_KEY';

  const client = new BiconomyV3(API_KEY, SECRET_KEY);

  // public
  console.log('ping:',  await client.ping());
  console.log('time:',  await client.serverTime());
  console.log('depth:', await client.depth('BTC_USDT', 5));

  // private (replace with real credentials)
  // console.log('assets:', await client.assets());
  // console.log('open:',   await client.openOrders({ symbol: 'BTC_USDT' }));
  // console.log('place limit:',           await client.placeOrder('BTC_USDT','BUY','LIMIT','0.001','21000'));
  // console.log('place market buy 1USDT:', await client.placeOrder('BTC_USDT','BUY','MARKET','1'));
  // console.log('cancel one:',             await client.cancelOrder('BTC_USDT', 32865));
  // console.log('cancel batch:',           await client.cancelBatchOrders('BTC_USDT', [32865, 32866]));
}

if (require.main === module) {
  main().catch(err => { console.error(err); process.exit(1); });
}

module.exports = { BiconomyV3 };
