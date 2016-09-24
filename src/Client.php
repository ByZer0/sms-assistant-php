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
     * Your sms-assistent.by password, need for API authorization.
     *
     * @var string
     */
    protected $password;

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
     * Construct instance of Client. Need to pass API authorization data (username and password)
     * to constructor. Also, sender name is required. Sender name must be one of available to you
     * senders.
     *
     * @param string                                          $username
     * @param string                                          $password
     * @param \ByZer0\SmsAssistantBy\Request\RequestInterface $httpClient
     */
    public function __construct($username, $password, $httpClient)
    {
        if (empty($username)) {
            throw new Exception('Username cannot be empty.');
        }

        if (empty($password)) {
            throw new Exception('Password cannot be empty.');
        }

        if (empty($httpClient)) {
            throw new Exception('HTTP client instance must be set.');
        }

        $this->username = $username;
        $this->password = $password;
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
        $response = $this->client->get($this->getEndpointUrl('credits/plain'), [
            'user'     => $this->username,
            'password' => $this->password,
        ]);
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
            'password'  => $this->password,
            'recipient' => $phone,
            'message'   => $text,
            'sender'    => $sender ?: $this->sender,
        ];
        if (!is_null($time)) {
            $data['date_send'] = $time->format('YmdHi');
        }
        $response = $this->client->get($this->getEndpointUrl('send_sms/plain'), $data);
        $code = intval($response);
        if ($code < 0) {
            Exception::raiseFromCode($code);
        } else {
            return $code;
        }
    }

    public function sendMessages($messages, $default = [], $time = null)
    {
        $data = '<?xml version="1.0" encoding="utf-8" ?>';
        $attributes = "login=\"{$this->username}\" password=\"{$this->password}\"";
        if (isset($time)) {
            $attributes .= " date_send=\"{$time->format('YmdHi')}\"";
        }
        $data .= "<package $attributes><message>";

        $attributes = '';
        if (isset($default['sender'])) {
            $attributes .= " sender=\"{$default['sender']}\"";
        } else {
            $attributes .= " sender=\"{$this->sender}\"";
        }
        $text = isset($default['text']) ? $default['text'] : '';
        $data .= "<default$attributes>$text</default>";

        foreach ($messages as $message) {
            $text = isset($message['text']) ? $message['text'] : '';
            $attributes = '';
            if (isset($message['sender'])) {
                $attributes .= " sender=\"{$message['sender']}\"";
            }

            $data .= "<msg recipient=\"{$message['phone']}\"$attributes>$text</msg>";
        }
        $data .= '</message></package>';

        return $this->client->postXml($this->getEndpointUrl('xml'), $data);
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
     * Change password for API requests.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

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
