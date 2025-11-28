<?php

class RequestMethod {
    const POST = "POST";
    const GET = "GET";
}

class YourClassNameHere {
    private $api_key;
    private $secret;
    private $host;
    private $try_counts;
    private $timeout;
    private $proxies;

    public function __construct($api_key, $secret, $host, $try_counts = 3, $timeout = 10, $proxies = null) {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->host = $host;
        $this->try_counts = $try_counts;
        $this->timeout = $timeout;
        $this->proxies = $proxies;
    }

    private function get_hmac_sha256($source) {
        return hash_hmac('sha256', $source, $this->secret);
    }

    private function get_md5_32($source) {
        return strtoupper(md5($source));
    }

    private function place_order_bico($market, $amount = "0", $side = "1", $price = "0") {
        $path = "/api/v2/private/trade/market";
        $params = [
            "amount" => $amount,
            "api_key" => $this->api_key,
            "market" => $market
        ];

        if ($price != "0") {
            $params["price"] = $price;
            $path = "/api/v2/private/trade/limit";
        }
        $params["side"] = $side;

        $sign_string = $this->build_parameters($params) . "&secret_key=" . $this->secret;
        $md5_sign = strtoupper($this->get_hmac_sha256($sign_string));
        $params["sign"] = $md5_sign;

        return $this->post_bico($path, $params);
    }

    // 其他方法如 create_user_withdraw, cancel_user_withdraw, get_user_assets, order_pending, get_user_withdraw_list, get_user_withdraw_addresss 类似于 place_order_bico

    private function request($req_method, $path, $requery_dict = null, $verify = false) {
        $url = $this->host . $path;
        if ($verify) {
            $query_str = $this->build_parameters($requery_dict);
            $url .= '?' . $query_str;
        } elseif ($requery_dict) {
            $url .= '?' . $this->build_parameters($requery_dict);
        }

        $headers = ["X-SITE-ID: 127"];

        for ($i = 0; $i < $this->try_counts; $i++) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                if ($req_method == RequestMethod::POST) {
                    curl_setopt($ch, CURLOPT_POST, true);
                    if (is_array($requery_dict)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requery_dict));
                    }
                }

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($http_code == 200) {
                    return json_decode($response, true);
                } else {
                    error_log("HTTP Code: {$http_code} - Reason: " . curl_error($ch));
                }

                curl_close($ch);
            } catch (Exception $e) {
                error_log("Request to {$path}, error: " . $e->getMessage());
                sleep(1);
            }
        }

        return null;
    }

    private function post_bico($path, $param_dict) {
        $url = $this->host . $path;
        $headers = [
            "X-SITE-ID: 127",
            "Content-Type: multipart/form-data"
        ];

        for ($i = 0; $i < $this->try_counts; $i++) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param_dict));

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($http_code == 200) {
                    return json_decode($response, true);
                } else {
                    error_log("HTTP Code: {$http_code} - Reason: " . curl_error($ch));
                }

                curl_close($ch);
            } catch (Exception $e) {
                error_log("Request to {$path}, error: " . $e->getMessage());
                sleep(1);
            }
        }

        return null;
    }

    private function build_parameters($params) {
        ksort($params);
        return http_build_query($params, '', '&');
    }
}

// Example usage:
$api_key = "test_api_key_20240501";
$secret = "test_secret_20240501";
$host = "https://api.biconomy.com";

// Initialize your class with your API credentials
$your_instance = new YourClassNameHere($api_key, $secret, $host);

// Example usage of place_order_bico method
$market = "SKID_USDT";
$amount = "0.7";
$side = "1";  // 1 for ask, 2 for bid buy
$price = "0.021";  // 0 is market order
$response = $your_instance->place_order_bico($market, $amount, $side, $price);
print_r($response);

// Example usage of get_user_assets method
// $assets_response = $your_instance->get_user_assets();
// print_r($assets_response);

?>
