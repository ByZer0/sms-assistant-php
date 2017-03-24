[![StyleCI](https://styleci.io/repos/69090570/shield?style=flat)](https://styleci.io/repos/69090570)

# sms-assistent.by API PHP wrapper

This library can be used to send messages via [sms-assistent.by](http://sms-assistent.by) site.

More info about API can be found [here](http://help.sms-assistent.by/sms-rassyilka/rassylka-po-api/).

# Installation

Add following requirement to your `composer.json` file:

```json
{
    "require": {
        "by-zer0/sms-assistant-php": "1.2"
    }
}
```

or just use composer command

```bash
composer require by-zer0/sms-assistant-php:1.2
```

# Usage

First create client instance.

```php
require 'vendor/autoload.php';

use ByZer0\SmsAssistantBy\Client;
use ByZer0\SmsAssistantBy\Http\GuzzleClient;

$client = (new Client(new GuzzleClient()))
    ->setUsername('<username>') // Set username to pass API authorization.
    ->setSender('<sender-name>') // Set default sender name.
    ->setPassword('<password>'); // Set account password to pass API authorization.
//    ->setToken('<token>'); // Optional, set access token instead of password.
```

Constructor accepts `ByZer0\SmsAssistantBy\Http\ClientInterface` instance. This instance will be actually used to perform HTTP requests. By default, package contains `ByZer0\SmsAssistantBy\Http\GuzzleClient` class - request wrapper for `guzzlehttp/guzzle` library. You can write your own implementation of `ByZer0\SmsAssistantBy\Http\ClientInterface` to use with any other library.

Execute following command if you want to use default guzzle adapter:

```bash
composer require guzzlehttp/guzzle
```

## Massive sending

Use method `sendMessages($messages, $default = [], $time = null)` to send multiple messages at once.

```php
$default = [
    'sender' => '<default-sender>',
    'text' => 'This is default message text',
];

$messages = [
    [
        'phone' => '+375294011111',
        'sender' => 'notdefault',
        'text' => 'Message for first recipient',
    ],
    [
        'phone' => '+375294022222', // default sender name and text will be used
    ],
];

$client->sendMessages($messages, $default);
```

in this case `$default` represents common settings for each message. Default message can contain next fields:

* `sender` - to use one sender name for every message
* `text` - common text can be placed here to avoid duplicate in every message

Every message must contain one required field:

* `phone` - phone number of message recipient.

In addition to `phone`, every default value can be overwritten in any message.

You can also use third parameter of `sendMessages()` to delay send. Third parameter `$time` accepts `DateTime` instance.

## Single message

Use method `sendMessage($phone, $text, $time = null, $sender = null)` to send single message.

```php
$client->sendMessage('+375294011111', 'Example message text');
```

This method accepts up to four parameters:

- `phone` - Phone number of message recipient.
- `text` - Message text.
- `time` - `DateTime` instance. Use to delay message delivery.
- `sender` - Sender name. If used, this name will override default value specified by `setSender()` method.
