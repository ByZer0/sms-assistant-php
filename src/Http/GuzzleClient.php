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

namespace ByZer0\SmsAssistantBy\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Request interface implementation with Guzzle library.
 *
 * @author Zer0
 */
class GuzzleClient implements ClientInterface
{
    /**
     * Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    public function __construct($client = null)
    {
        if ($client instanceof Client) {
            $this->client = $client;
        }
    }

    public function get($url, $data, $headers)
    {
        $client = $this->getClient();
        $response = $client->get($url, [
            'query' => $data,
            'headers' => $headers,
        ]);

        return $response->getBody()->getContents();
    }

    public function post($url, $data, $headers)
    {
        $client = $this->getClient();

        return $client->post($url, [
            'data' => $data,
            'headers' => $headers,
        ]);
    }

    public function postXml($url, $xml, $headers)
    {
        $headers['Content-Type'] = 'text/xml; charset=UTF8';
        $client = $this->getClient();
        $request = new Request('POST', $url, $headers, $xml);

        return $client->send($request);
    }

    /**
     * Returns guzzle instance.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client([]);
        }

        return $this->client;
    }
}
