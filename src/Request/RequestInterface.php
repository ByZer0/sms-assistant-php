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

namespace ByZer0\SmsAssistantBy\Request;

/**
 * Common interface for HTTP libraries.
 *
 * @author Zer0
 */
interface RequestInterface
{
    /**
     * Make GET request to API. $data will be sent as query params.
     *
     * @param string $url
     * @param array  $data
     */
    public function get($url, $data);

    /**
     * Make POST request to API. $data will be sent as POST data in request body.
     *
     * @param string $url
     * @param array  $data
     */
    public function post($url, $data);

    /**
     * Make POST request to API with XML data (Content-Type: text/xml; charset=UTF8).
     *
     * @param string $url
     * @param string $xml
     */
    public function postXml($url, $xml);
}
