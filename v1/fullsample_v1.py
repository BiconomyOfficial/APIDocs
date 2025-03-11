import time
from enum import Enum
import requests
from urllib3 import encode_multipart_formdata
import hashlib

class RequestMethod(Enum):
    POST = "POST"
    GET = "GET"

class YourClassNameHere:
    def __init__(self, api_key, secret, host, try_counts=3, timeout=10, proxies=None):
        self.api_key = api_key
        self.secret = secret
        self.host = host
        self.try_counts = try_counts
        self.timeout = timeout
        self.proxies = proxies
        self.logger = None  # You might want to initialize your logger here

    def get_md5_32(self, source: str) -> str:
        """
        :param source: the source string
        :return: md5 string
        """
        md5 = hashlib.md5()
        md5.update(source.encode(encoding='utf-8'))
        return md5.hexdigest()
    
    def get_pendingorder_bico(self, market: str, offset: str, limit: str):
        """
        :param market: BTC_USDT
        :param orderid: 1
        :return:
        """
        path = "/api/v1/private/order/pending"
        params = {
            "api_key": self.api_key,
            "market": market,
            "offset": offset,
            "limit": limit
        }
        
        sign_string = self.build_parameters(params) + "&secret_key=" + self.secret
        print(sign_string)
        md5_sign = str.upper(self.get_md5_32(sign_string))
        params["sign"] = md5_sign

        return self.post_bico(path=path, param_dict=params)
    
    def cancel_allorder_bico(self, market: str):
        """
        :param market: BTC_USDT
        :param orderid: 1
        :return:
        """
        path = "/api/v1/private/trade/cancelallorder"
        params = {
            "api_key": self.api_key,
            "market": market
        }
        
        sign_string = self.build_parameters(params) + "&secret_key=" + self.secret
        print(sign_string)
        md5_sign = str.upper(self.get_md5_32(sign_string))
        params["sign"] = md5_sign

        return self.post_bico(path=path, param_dict=params)

    def place_order_bico(self, market: str, amount: str = "0", side: str = "1", price: str = "0"):
        """
        :param market: BTC_USDT
        :param amount: 10.0
        :param side: 1 ask, 2 bid
        :param price: 0.003 usdt
        :return:
        """
        path = "/api/v1/private/trade/market"
        params = {
            "amount": amount,
            "api_key": self.api_key,
            "market": market
        }
        if price != "0":
            params["price"] = price
            path = "/api/v1/private/trade/limit"
        params["side"] = side
        
        sign_string = self.build_parameters(params) + "&secret_key=" + self.secret
        print(sign_string)
        md5_sign = str.upper(self.get_md5_32(sign_string))
        params["sign"] = md5_sign

        return self.post_bico(path=path, param_dict=params)

    def get_user_assets(self):
        """
        :return: assets json
        """
        path = '/api/v1/private/user'
        params = {"api_key": self.api_key,
                  "secret_key": self.secret
                  }
        params_string = self.build_parameters(params)

        params_sign = {
            "api_key": self.api_key,
            "sign": str.upper(self.get_md5_32(params_string))
        }
        print(params_sign)
        return self.request(RequestMethod.POST, path, params_sign)

    def request(self, req_method: RequestMethod, path: str, requery_dict=None, verify=False):
        url = self.host + path
        if verify:
            query_str = self.build_parameters(requery_dict)
            url += '?' + query_str
        elif requery_dict:
            url += '?' + self.build_parameters(requery_dict)
        headers = {"X-SITE-ID": "127"}

        for i in range(0, self.try_counts):
            try:
                response = requests.request(req_method.value, url=url, headers=headers, timeout=self.timeout,
                                            proxies=self.proxies)
                if response.status_code == 200:
                    return response.json()
                else:
                    self.logger.info(f"{response.status_code}-{response.reason}")
            except Exception as error:
                self.logger.info(f"requests:{path}, error: {repr(error)}")
                time.sleep(1)
        return None

    def post_bico(self, path: str, param_dict=None):
        """
        :param path: the url
        :param param_dict: the params
        :return:
        """
        url = self.host + path
        headers = {
            "X-SITE-ID": "127",
            "Content-Type": "multipart/form-data"
        }
        for i in range(0, self.try_counts):
            try:
                encode_dict = encode_multipart_formdata(param_dict)
                data = encode_dict[0]
                headers['Content-Type'] = encode_dict[1]
                response = requests.post(url=url, headers=headers, data=data)
                if response.status_code == 200:
                    return response.json()
                else:
                    self.logger.info(f"{response.status_code}-{response.reason}")
            except Exception as error:
                self.logger.info(f"requests:{path}, error: {repr(error)}")
                time.sleep(1)
        return None

    def build_parameters(self, params: dict):
        keys = list(params.keys())
        keys.sort()
        return '&'.join([f"{key}={params[key]}" for key in keys])

# Example usage:
if __name__ == "__main__":
    api_key = "test_api_key_20240501"
    secret = "test_secret_20240501"
    host = "https://api.biconomy.com"
    
    # Initialize your class with your API credentials
    your_instance = YourClassNameHere(api_key, secret, host)
    
    # Example usage of place_order_bico method
    market = "SUI_USDT"
    amount = "0.1"
    side = "2"  # 1 for ask=sell, 2 for bid=buy
    price = "200.2"
    response = your_instance.place_order_bico(market, amount, side, price)
    # response = your_instance.place_order_bico(market, amount, 2, price)
    print("Place order response:", response)
    # response = your_instance.cancel_allorder_bico(market)
    # print("cancel_allorder order response:", response)
    # response = your_instance.get_pendingorder_bico(market, "0", "100")
    # print("Pending order response:", response)
    
    # Example usage of get_user_assets method
    # assets_response = your_instance.get_user_assets()
    # print("User assets response:", assets_response)
