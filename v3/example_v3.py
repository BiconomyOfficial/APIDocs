"""
Biconomy V3 Open API — Python example
"""

import hmac
import hashlib
import json
import time
from typing import Any, Dict, Optional

import requests


BASE_URL = "https://api.biconomy.com"


class BiconomyV3:
    def __init__(self, api_key: str, secret_key: str, base_url: str = BASE_URL, recv_window: int = 5000):
        self.api_key = api_key
        self.secret_key = secret_key.encode()
        self.base_url = base_url.rstrip("/")
        self.recv_window = recv_window
        self.session = requests.Session()

    # ---------- signing ----------

    @staticmethod
    def _now_ms() -> int:
        return int(time.time() * 1000)

    def _sign(self, payload: str) -> str:
        return hmac.new(self.secret_key, payload.encode(), hashlib.sha256).hexdigest()

    def _build_query_payload(self, params: Dict[str, Any], timestamp: int) -> str:
        # Sort keys alphabetically, raw values, then append &timestamp=
        if not params:
            return f"timestamp={timestamp}"
        items = [(k, params[k]) for k in sorted(params.keys()) if params[k] is not None]
        body = "&".join(f"{k}={v}" for k, v in items)
        return f"{body}&timestamp={timestamp}"

    def _build_body_payload(self, body: str, timestamp: int) -> str:
        if not body:
            return f"timestamp={timestamp}"
        return f"{body}&timestamp={timestamp}"

    def _auth_headers(self, sign: str, timestamp: int) -> Dict[str, str]:
        return {
            "X-API-KEY": self.api_key,
            "X-API-SIGN": sign,
            "X-API-TIMESTAMP": str(timestamp),
            "X-API-RECV-WINDOW": str(self.recv_window),
        }

    # ---------- low-level request ----------

    def _public_get(self, path: str, params: Optional[Dict[str, Any]] = None) -> Any:
        resp = self.session.get(self.base_url + path, params=params, timeout=10)
        resp.raise_for_status()
        return resp.json()

    def _signed_get(self, path: str, params: Optional[Dict[str, Any]] = None) -> Any:
        params = {k: v for k, v in (params or {}).items() if v is not None}
        ts = self._now_ms()
        payload = self._build_query_payload(params, ts)
        sign = self._sign(payload)
        resp = self.session.get(
            self.base_url + path, params=params, headers=self._auth_headers(sign, ts), timeout=10
        )
        resp.raise_for_status()
        return resp.json()

    def _signed_post(self, path: str, body: Optional[Dict[str, Any]] = None) -> Any:
        # IMPORTANT: the raw body bytes are signed, so we must send the same bytes.
        raw = json.dumps(body, separators=(",", ":"), ensure_ascii=False) if body else ""
        ts = self._now_ms()
        payload = self._build_body_payload(raw, ts)
        sign = self._sign(payload)
        headers = self._auth_headers(sign, ts)
        headers["Content-Type"] = "application/json"
        resp = self.session.post(self.base_url + path, data=raw.encode(), headers=headers, timeout=10)
        resp.raise_for_status()
        return resp.json()

    # ---------- public ----------

    def ping(self):
        return self._public_get("/api/v3/ping")

    def server_time(self):
        return self._public_get("/api/v3/time")

    def symbols(self):
        return self._public_get("/api/v3/symbols")

    def tickers(self):
        return self._public_get("/api/v3/tickers")

    def depth(self, symbol: str, limit: int = 100):
        return self._public_get("/api/v3/depth", {"symbol": symbol, "limit": limit})

    def trades(self, symbol: str, limit: int = 100):
        return self._public_get("/api/v3/trades", {"symbol": symbol, "limit": limit})

    def klines(self, symbol: str, interval: str, start_ms: int, end_ms: int, limit: int = 100):
        return self._public_get(
            "/api/v3/klines",
            {"symbol": symbol, "interval": interval, "startTime": start_ms, "endTime": end_ms, "limit": limit},
        )

    # ---------- private ----------

    def assets(self):
        return self._signed_get("/api/v3/account/assets")

    def open_orders(self, symbol: Optional[str] = None, page: int = 1, page_size: int = 10):
        return self._signed_get(
            "/api/v3/trade/openOrders", {"symbol": symbol, "page": page, "pageSize": page_size}
        )

    def history_orders(self, symbol: Optional[str] = None, start_ms: Optional[int] = None,
                       end_ms: Optional[int] = None, page: int = 1, page_size: int = 10):
        return self._signed_get(
            "/api/v3/trade/historyOrders",
            {"symbol": symbol, "startTime": start_ms, "endTime": end_ms, "page": page, "pageSize": page_size},
        )

    def my_trades(self, symbol: Optional[str] = None, start_ms: Optional[int] = None,
                  end_ms: Optional[int] = None, page: int = 1, page_size: int = 10):
        return self._signed_get(
            "/api/v3/trade/myTrades",
            {"symbol": symbol, "startTime": start_ms, "endTime": end_ms, "page": page, "pageSize": page_size},
        )

    def order_trades(self, order_id: int, page: int = 1, page_size: int = 10):
        return self._signed_get(
            "/api/v3/trade/orderTrades", {"orderId": order_id, "page": page, "pageSize": page_size}
        )

    def place_order(self, symbol: str, side: str, order_type: str, amount: str, price: Optional[str] = None):
        """
        side: BUY / SELL
        order_type: LIMIT / MARKET
        amount: for MARKET BUY -> quote-asset amount (e.g. USDT spent);
                otherwise        -> base-asset amount
        price: required for LIMIT
        """
        body = {"symbol": symbol, "side": side, "type": order_type, "amount": amount}
        if order_type == "LIMIT":
            body["price"] = price
        return self._signed_post("/api/v3/trade/order", body)

    def batch_orders(self, symbol: str, orders: list):
        # orders: [{"side":"BUY","amount":"0.001","price":"21000"}, ...]
        return self._signed_post("/api/v3/trade/batchOrders", {"symbol": symbol, "orders": orders})

    def cancel_order(self, symbol: str, order_id: Optional[int] = None):
        body: Dict[str, Any] = {"symbol": symbol}
        if order_id:
            body["orderId"] = order_id
        return self._signed_post("/api/v3/trade/cancelOrder", body)

    def cancel_batch_orders(self, symbol: str, order_ids: list):
        return self._signed_post(
            "/api/v3/trade/cancelBatchOrders", {"symbol": symbol, "orderIds": order_ids}
        )


# ---------- demo ----------

if __name__ == "__main__":
    API_KEY = "YOUR_API_KEY"
    SECRET_KEY = "YOUR_SECRET_KEY"

    client = BiconomyV3(API_KEY, SECRET_KEY)

    # public
    print("ping:", client.ping())
    print("time:", client.server_time())
    print("depth:", client.depth("BTC_USDT", limit=5))

    # private (replace with real credentials)
    # print("assets:", client.assets())
    # print("open orders:", client.open_orders("BTC_USDT"))
    # print("place limit:", client.place_order("BTC_USDT", "BUY", "LIMIT", "0.001", "21000"))
    # print("place market buy (1 USDT):", client.place_order("BTC_USDT", "BUY", "MARKET", "1"))
    # print("cancel one:", client.cancel_order("BTC_USDT", order_id=32865))
    # print("cancel batch:", client.cancel_batch_orders("BTC_USDT", [32865, 32866]))
