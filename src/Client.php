<?php

/*
 * The MIT License
 *
 * Copyright 2016 Zer0.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ByZer0\SmsAssistantBy;

use ByZer0\SmsAssistantBy\Exceptions\Exception;

/**
 * sms-assistent.by HTTP API client class.
 *
 * @author Zer0
 */
class Client
{
    /**
     * Base API URL.
     *
     * @var string
     */
    protected $baseUrl = 'https://userarea.sms-assistent.by/api/v1/';

    /**
     * HTTP client instance which will actually perform requests.
     *
     * @var \ByZer0\SmsAssistantBy\Request\RequestInterface
     */
    protected $client;

    /**
     * Your sms-assistent.by username, need for API authorization.
     *
     * @var string
     */
    protected $username;

    /**
     * Your sms-assistent.by access token, need for API authorization.
     *
     * @var string
     */
    protected $token;

    /**
     * Sender name. Messages will be sent from this name. It must be one of available
     * to your account senders.
     *
     * @var string
     *
     * @link http://help.sms-assistent.by/termini-i-opredeleniya/otpravitel-soobscheniya/
     */
    protected $sender;

    /**
     * Construct instance of Client. Need to pass API authorization data (username and token)
     * to constructor. Also, sender name is required. Sender name must be one of available to you
     * senders.
     *
     * @param string                                          $username
     * @param string                                          $token
     * @param \ByZer0\SmsAssistantBy\Request\RequestInterface $httpClient
     */
    public function __construct($username, $token, $httpClient)
    {
        if (empty($username)) {
            throw new Exception('Username cannot be empty.');
        }

        if (empty($token)) {
            throw new Exception('Token cannot be empty.');
        }

        if (empty($httpClient)) {
            throw new Exception('HTTP client instance must be set.');
        }

        $this->username = $username;
        $this->token = $token;
        $this->client = $httpClient;
    }

    /**
     * Make absolute API endpoint URL from relative.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getEndpointUrl($uri)
    {
        return $this->baseUrl.$uri;
    }

    /**
     * Retreive current user balance status. Returns available amount of credits.
     *
     * @throws \ByZer0\SmsAssistantBy\Exceptions\Exception
     *
     * @return float
     */
    public function getBalance()
    {
        $data = ['user' => $this->username];
        $headers = ['requestAuthToken' => $this->token];
        $response = $this->client->get($this->getEndpointUrl('credits/plain'), $data, $headers);
        $balance = floatval($response);
        if ($balance >= 0) {
            return $balance;
        } else {
            Exception::raiseFromCode($balance);
        }
    }

    /**
     * Send single message.
     *
     * @param string    $phone  Recipient phone number.
     * @param string    $text   Message text.
     * @param \DateTime $time   Time when send message. Optional, only if message delivery must be delayed.
     * @param string    $sender Sender name, default internal sender name will be used if empty.
     *
     * @return bool
     */
    public function sendMessage($phone, $text, $time = null, $sender = null)
    {
        $data = [
            'user'      => $this->username,
            'recipient' => $phone,
            'message'   => $text,
            'sender'    => $sender ?: $this->sender,
        ];
        $headers = ['requestAuthToken' => $this->token];
        if (!is_null($time)) {
            $data['date_send'] = $time->format('YmdHi');
        }
        $response = $this->client->get($this->getEndpointUrl('send_sms/plain'), $data, $headers);
        $code = intval($response);
        if ($code < 0) {
            Exception::raiseFromCode($code);
        } else {
            return $code;
        }
    }

    /**
     * Send multiple messages.
     *
     * Every message field (if presented) will override value from $default.
     * Only phone is required in every message. Each message can have following fields:
     *
     * - phone : (required). Phone number of recipient.
     * - text: (optional). Override default message text for this message with custom text.
     * - sender: (optional). Override default sender name for this message with custom name.
     *
     * Default message can have following fields (all fields are optional):
     *
     * - text: Common text for all messages.
     * - sender: Common sender name for all messages.
     *
     * @param array     $messages Array of messages.
     * @param array     $default  Default message config.
     * @param \DateTime $time     Time when send message. Optional, only if messages delivery must be delayed.
     *
     * @return bool
     */
    public function sendMessages($messages, $default = [], $time = null)
    {
        $data = '<?xml version="1.0" encoding="utf-8" ?>';
        $attributes = "login=\"{$this->username}\"";
        if (isset($time)) {
            $attributes .= " date_send=\"{$time->format('YmdHi')}\"";
        }
        $data .= "<package $attributes><message>";

        $data .= $this->makeDefaultMessageXml($default);

        foreach ($messages as $message) {
            $data .= $this->makeMessageXml($message);
        }
        $data .= '</message></package>';

        $headers = ['requestAuthToken' => $this->token];

        return $this->client->postXml($this->getEndpointUrl('xml'), $data, $headers);
    }

    /**
     * Convert default message parameters to XML string.
     *
     * @param array $message
     *
     * @return string
     */
    protected function makeDefaultMessageXml($message)
    {
        $attributes = '';
        if (isset($message['sender'])) {
            $attributes .= " sender=\"{$message['sender']}\"";
        } else {
            $attributes .= " sender=\"{$this->sender}\"";
        }
        $text = isset($message['text']) ? $message['text'] : '';

        return "<default$attributes>$text</default>";
    }

    /**
     * Convert message parameters to XML string.
     *
     * @param array $message
     *
     * @return string
     */
    protected function makeMessageXml($message)
    {
        $text = isset($message['text']) ? $message['text'] : '';
        $attributes = '';
        if (isset($message['sender'])) {
            $attributes .= " sender=\"{$message['sender']}\"";
        }

        return "<msg recipient=\"{$message['phone']}\"$attributes>$text</msg>";
    }

    /**
     * Change username for API requests.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Change token for API requests.
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Change sender name.
     *
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }
}
