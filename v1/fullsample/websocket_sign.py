# -*- coding: utf-8 -*-
import hmac
import hashlib

api_key = "bb2b7800-925c-48bb-b7d8-2159fad91eda"
secret_key = "81d479a9-23f7-4e3b-9746-afa0bd6378b8"
timestamp = "1719028320000"

message = f"api_key={api_key}&secret_key={secret_key}&timestamp={timestamp}"
hmac_key = secret_key.encode()
message = message.encode()

signature = hmac.new(hmac_key, message, hashlib.sha256).hexdigest()
print(signature)
