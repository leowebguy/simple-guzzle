# Simple Guzzle plugin for Craft CMS

A simple way to call Guzzle and cache data from APIs directly in your Craft CMS templates.

## Requirements

This plugin requires:
- **Craft CMS**: `^5.0.0`
- **PHP**: `^8.2 || ^8.3 || ^8.4 || ^8.5`

## Table of Contents

1. [Requirements](#requirements)
2. [Install](#install)
3. [Parameters](#parameters)
4. [Usage](#usage)
   1. [GET JSON](#get-json)
   2. [GET Non-JSON](#get-non-json)
   3. [GET with Options](#get-with-options)
   4. [GET with Caching](#get-with-caching)
   5. [POST with Bearer Auth](#post-with-bearer-auth)
   6. [POST with Basic Auth](#post-with-basic-auth)
5. [Twig Output](#twig-output)
6. [Error Handling](#error-handling)

## Install

To install the plugin, run:

```bash
composer require leowebguy/simple-guzzle -w
php craft plugin/install simple-guzzle
```

## Parameters

The `guzzle(...)` Twig function accepts the following parameters:

| Parameter     | Type             | Default | Description / Example value                                              |
|---------------|------------------|---------|--------------------------------------------------------------------------|
| `client`      | `array`          | _required_ | Client options passed to Guzzle (e.g. `{ base_uri: 'https://api.myapi.com/' }`) |
| `method`      | `string`         | _required_ | HTTP method (e.g. `'GET'`, `'POST'`, `'PUT'`, `'DELETE'`)                 |
| `destination` | `string`         | _required_ | Request URI path / query (e.g. `'v1/path'`)                               |
| `options`     | `array`          | `[]`    | Request options such as headers, body, or auth                           |
| `duration`    | `int\|null`       | `0`     | Cache expiration in seconds (`0` or omitted = no caching)                |

## Usage

### GET JSON

```twig
{% set request = guzzle({
    base_uri: 'https://official-joke-api.appspot.com/'
}, 'GET', 'random_joke') %}

{% header "Content-Type: application/json; charset=utf-8" %}
{{ request|json_encode|raw }}
```

Result:

```json
{
    "type": "general",
    "setup": "Who is the coolest Doctor in the hospital?",
    "punchline": "The hip Doctor!",
    "id": 302
}
```

### GET Non-JSON

_The plugin automatically returns a string if the result cannot be parsed as JSON:_

```twig
{% set request = guzzle({
    base_uri: 'http://api.geonames.org/'
}, 'GET', 'srtm1?lat=50.01&lng=10.2&username=demo&style=full') %}

{{ request }}
```

Result:

```
"208"
```

### GET with Options

```twig
{% set request = guzzle({
    base_uri: 'https://currency-converter5.p.rapidapi.com/'
}, 'GET', 'currency/convert?from=USD&to=CAD&amount=1', {
    headers: {
        'X-RapidAPI-Key': 'your-api-key',
        'X-RapidAPI-Host': 'currency-converter5.p.rapidapi.com'
    }
}) %}
```

Result:

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

By default, cache is set to `0` (no caching) unless you provide a positive cache duration in seconds (e.g. `3600` = 1 hour, `86400` = 24 hours):

```twig
{% set request = guzzle({
    base_uri: 'https://official-joke-api.appspot.com/'
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

Result:

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
                      "url": "https://craftcms.com"
                  }
              }
           }'
}) %}
```

## Twig Output

Outputting results into your template:

```twig
{% set request = guzzle({
    base_uri: 'https://api.openai.com/v1/'
}, 'POST', 'chat/completions', {
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer my-bearer-token'
    },
    body: '{
              "model": "gpt-4o",
              "messages": [{"role": "user", "content": "Hello, who are you?"}]
           }'
}) %}

{% if request.choices is defined %}
    <p>{{ request.choices[0].message.content }}</p>
{% endif %}
```

## Error Handling

If an HTTP or Guzzle exception occurs during the request, an array with error details is returned:

```twig
{% set response = guzzle({ base_uri: 'https://api.example.com/' }, 'GET', 'endpoint') %}

{% if response.error is defined and response.error %}
    <p class="error">Request failed: {{ response.reason }}</p>
{% else %}
    {# Render data #}
{% endif %}
```
