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

namespace ByZer0\SmsAssistantBy\Exceptions;

/**
 * Base exception class.
 *
 * @author Zer0
 */
class Exception extends \Exception
{
    protected static $codes = [
        -1  => 'ByZer0\SmsAssistantBy\Exceptions\LowBalanceException',
        -2  => 'ByZer0\SmsAssistantBy\Exceptions\AuthentificationException',
        -3  => 'ByZer0\SmsAssistantBy\Exceptions\MessageTextException',
        -4  => 'ByZer0\SmsAssistantBy\Exceptions\PhoneNumberException',
        -5  => 'ByZer0\SmsAssistantBy\Exceptions\SenderNameException',
        -6  => 'ByZer0\SmsAssistantBy\Exceptions\AuthentificationException',
        -7  => 'ByZer0\SmsAssistantBy\Exceptions\AuthentificationException',
        -10 => 'ByZer0\SmsAssistantBy\Exceptions\ServerException',
        -11 => 'ByZer0\SmsAssistantBy\Exceptions\MessageIdException',
        -12 => 'ByZer0\SmsAssistantBy\Exceptions\ServerException',
        -13 => 'ByZer0\SmsAssistantBy\Exceptions\ServerException',
        -14 => 'ByZer0\SmsAssistantBy\Exceptions\SendTimeException',
        -15 => 'ByZer0\SmsAssistantBy\Exceptions\SendTimeException',
    ];

    public static function raiseFromCode($code)
    {
        if (isset(static::$codes[$code])) {
            $class = static::$codes[$code];
            throw new $class();
        } else {
            throw new static("Exception with code $code occured.");
        }
    }
}
