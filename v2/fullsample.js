const axios = require('axios');
const crypto = require('crypto');
const querystring = require('querystring');

// Enum for HTTP request methods
class RequestMethod {
    static POST = 'POST';
    static GET = 'GET';
}

// Main class definition for API interaction
class YourClassNameHere {
    constructor(apiKey, secret, host, tryCounts = 3, timeout = 1000, proxy = null) {
        this.apiKey = apiKey;
        this.secret = secret;
        this.host = host;
        this.tryCounts = tryCounts;
        this.timeout = timeout;
        this.proxy = proxy;
        // Placeholder for logger initialization (depends on your logging system)
        this.logger = null;
    }

    getMd5_32(source) {
        const hash = crypto.createHash('md5');
        hash.update(source, 'utf8');
        return hash.digest('hex').toUpperCase();
    }

    placeOrderBico(market, amount = '0', side = '1', price = '0') {
        let path = '/api/v1/private/trade/market';
        let params = {
            amount,
            api_key: this.apiKey,
            market
        };
        if (price !== '0') {
            params.price = price;
            path = '/api/v1/private/trade/limit';
        }
        params.side = side;

        const signString = this.buildParameters(params) + `&secret_key=${this.secret}`;
        console.log(signString);
        const md5Sign = this.getMd5_32(signString);
        params.sign = md5Sign;

        return this.postBico(path, params);
    }

    getUserAssets() {
        const path = '/api/v1/private/user';
        const signString = "api_key=" + this.apiKey + `&secret_key=${this.secret}`;
        const params = {
            api_key: this.apiKey,
            sign: this.getMd5_32(signString)
        };

        return this.request(RequestMethod.POST, path, params);
    }
    
    // Perform an HTTP request with specified method, path, data, and verification flag
    async request(method, path, requestData = null, verify = false) {
        let url = `${this.host}${path}`;
        let config = {
            timeout: this.timeout,
            proxy: this.proxy,
            headers: {
                'X-SITE-ID': '127',
                // Set Content-Type to application/x-www-form-urlencoded
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        };

        if (method === RequestMethod.GET && requestData) {
            url += `?${querystring.stringify(requestData)}`;
        } else if (requestData) {
            // For POST method, convert requestData to x-www-form-urlencoded format
            if (method === RequestMethod.POST) {
                // Convert requestData to query string and append to URL
                url += `?${querystring.stringify(requestData)}`;
                config.data = querystring.stringify(requestData);
            }
        }

        console.log(config.data);
        for (let i = 0; i < this.tryCounts; i++) {
            try {
                const response = await axios[method.toLowerCase()](url, config);
                if (response.status === 200) {
                    return response.data;
                } else {
                    console.log(`${response.status}-${response.statusText}`);
                }
            } catch (error) {
                console.log(`Request error: ${error.message}`);
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before retry
            }
        }
        return null;
    }

    // Specifically handle POST requests for Bico API
    async postBico(path, postData) {
        const url = `${this.host}${path}`;
        const config = {
            timeout: this.timeout,
            proxy: this.proxy,
            headers: {
                'X-SITE-ID': '127',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: querystring.stringify(postData)
        };

        for (let i = 0; i < this.tryCounts; i++) {
            try {
                const response = await axios.post(url, config.data, config);
                if (response.status === 200) {
                    return response.data;
                } else {
                    console.log(`${response.status}-${response.statusText}`);
                }
            } catch (error) {
                console.log(`Post request error: ${error.message}`);
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before retry
            }
        }
        return null;
    }

    buildParameters(params) {
        const sortedKeys = Object.keys(params).sort();
        return sortedKeys.map(key => `${key}=${params[key]}`).join('&');
    }
}

// Example usage
async function main() {
    const apiKey = "test_api_key_20240501";
    const secret = "test_secret_20240501";
    const host = "https://api.biconomy.com";

    const yourInstance = new YourClassNameHere(apiKey, secret, host);

    const market = "O2_USDT";
    const amount = "4590.3";
    const side = "1";  // 1 for `ask`, 2 for `bid`
    const price = "0.00581";
    const orderResponse = await yourInstance.placeOrderBico(market, amount, side, price);
    console.log("Place order response:", orderResponse);

    const assetsResponse = await yourInstance.getUserAssets();
    console.log("User assets response:", assetsResponse);
}

main().catch(error => console.error(error));
