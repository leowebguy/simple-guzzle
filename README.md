Simple Guzzle plugin for Craft CMS
===

A simple way to call guzzle and cache data from API's

## Table of Contents

1. [Install](#install)
2. [Parameters](#parameters)
3. [Usage](#usage)
   1. [GET Json](#get-json)
   2. [GET non-Json](#get-non-json)
   3. [GET with Options](#get-with-options)
   4. [GET with Caching](#get-with-caching)
   5. [POST with Bearer Auth](#post-with-bearer-auth)
   6. [POST with Basic Auth](#post-with-basic-auth)
4. [Twig Output](#twig-output)

## Install

```bash
composer require leowebguy/simple-guzzle && php craft plugin/install simple-guzzle
```

## Parameters

| Parameter     | Example value                                                            |
|---------------|--------------------------------------------------------------------------|
| `client`      | `{ base_uri: 'https://api.myapi.com/' }`                                 |
| `method`      | `'GET'`                                                                  |
| `destination` | `'v1/path'`                                                              |
| `headers`     | `{ headers: { 'client_id': 'your-id', 'client_secret': 'your-secret' }}` |
| `cache`       | `0`                                                                      |

## Usage

### GET Json

```twig
{% set request = guzzle({
    base_uri : 'https://official-joke-api.appspot.com/'
}, 'GET', 'random_joke') %}

{% header "Content-Type: application/json; charset=utf-8" %}
{{ request|json_encode|raw }}
```

Result

```json
{
    "type": "general",
    "setup": "Who is the coolest Doctor in the hospital?",
    "punchline": "The hip Doctor!",
    "id": 302
}
```

### GET non-Json

_Plugin will automatically return string if result can't be parsed as Json_

```twig
{% set request = guzzle({
    base_uri : 'http://api.geonames.org/'
}, 'GET', 'srtm1?lat=50.01&lng=10.2&username=demo&style=full') %}

{{ request }}
```

Result

```
"208"
```

### GET with Options

```twig
{% set request = guzzle({
    base_uri: 'https://currency-converter5.p.rapidapi.com/'
}, 'GET', 'currency/convert?from=USD&to=CAD&amount=1', {
    headers: {
        'X-RapidAPI-Key': 'you-api-key',
        'X-RapidAPI-Host': 'currency-converter5.p.rapidapi.com'
    }
}) %}
```

Result

```json
{
    "base_currency_code": "USD",
    "base_currency_name": "United States dollar",
    "amount": "1.0000",
    "updated_date": "2023-04-19",
    "rates": {
        "CAD": {
            "currency_name": "Canadian dollar",
            "rate": "1.3443",
            "rate_for_amount": "1.3443"
        }
    },
    "status": "success"
}
```

### GET with Caching

By default cache will be set to `0` meaning no caching unless you provide a cache duration in seconds. ex: `3600` = 1h , `86400` = 24h

```twig
{% set request = guzzle({
    base_uri : 'https://official-joke-api.appspot.com/'
}, 'GET', 'random_joke', {}, 3600) %}

{% header "Content-Type: application/json; charset=utf-8" %}
{{ request|json_encode|raw }}
```

### POST with Bearer Auth

```twig
{% set request = guzzle({
    base_uri: 'https://api-ssl.bitly.com/'
}, 'POST', 'v4/bitlinks', {
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer my-bearer-token'
    },
    body: '{
              "domain": "bit.ly",
              "long_url": "https://craftcms.com/"
           }'
}) %}
```

Result

```json
{
    "created_at": "2023-04-20T00:10:44+0000",
    "id": "bitly.is/41no4QW",
    "link": "https://bitly.is/41no4QW",
    "custom_bitlinks": [],
    "long_url": "https://craftcms.com/",
    "archived": false,
    "tags": [],
    "deeplinks": [],
    "references": {
        "group": "https://api-ssl.bitly.com/v4/groups/Bi331psZCY8"
    }
}
```

### POST with Basic Auth

```twig
{% set request = guzzle({
    base_uri: 'https://gtmetrix.com/api/2.0/'
}, 'POST', 'tests', {
    headers: {
        'Content-Type': 'application/vnd.api+json'
    },
    auth: ['my-auth', ''],
    body: '{
              "data": {
                  "type": "test",
                  "attributes": {
                      "url":      "https://craftcms.com"
                  }
              }
           }'
}) %}
```

_Basic auth usually accepts username and password as parameters ex: `auth: ['username', 'password'],` in the example above the `token` is passed as username and pw is blank, per gtmetrix documentation._

Result

```json
{
    "data": {
        "id": "tMsUIR0M",
        "type": "test",
        "attributes": {
            ...
        }
    },
    "meta": {
        "credits_left": 49,
        "credits_used": 1
    },
    "links": {
        "self": "https://gtmetrix.com/api/2.0/tests/tMsUIR0M"
    }
}
```

## Twig Output

Making sure y'all understand how to output those results into your template.

```twig
{% set request = guzzle({
    base_uri: 'https://api.openai.com/v1/'
}, 'POST', 'completions', {
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer my-bearer-token'
    },
    body: '{
              "model": "text-davinci-003",
              "prompt": "Hello, who are you?"
           }'
}) %}

Question: Hello, who are you?
Answer: {{ request.choices[0].text }}
```

Output

```html
Question: Hello, who are you?
Answer: I'm Naveen. It's nice to meet you.
```

In the example above `request` outputs:

```json
{
    "id": "cmpl-77BD86ixAzutOIdaOP6nNY98V5vSf",
    "object": "text_completion",
    "created": 1681945746,
    "model": "text-davinci-003",
    "choices": [
        {
            "text": "\n\nI'm Naveen. It's nice to meet you.",
            "index": 0,
            "logprobs": null,
            "finish_reason": "stop"
        }
    ],
    "usage": {
        "prompt_tokens": 6,
        "completion_tokens": 14,
        "total_tokens": 20
    }
}
```
