<?php

class Base_Helper_Paypal
{

    var $host = 'https://api.sandbox.paypal.com';
    var $token = '';

    public static function read_stdin()
    {
        $fr = fopen("php://stdin", "r");
        $input = fgets($fr, 128);
        $input = rtrim($input);
        fclose($fr);
        return $input;
    }

    public static function get_access_token($clientId, $clientSecret, $url, $postdata)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $response = curl_exec($curl);
        if (empty($response)) {
            die(curl_error($curl));
            curl_close($curl);
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl);
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                $jsonResponse = json_decode($response, TRUE);
                return $jsonResponse;
            }
        }
        $jsonResponse = json_decode($response);
        return $jsonResponse->access_token;
    }

    public static function make_post_call($url, $postdata, $token)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $response = curl_exec($curl);
        if (empty($response)) {
            die(curl_error($curl));
            curl_close($curl);
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl);
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                $jsonResponse = json_decode($response, TRUE);
                return $jsonResponse;
            }
        }
        $jsonResponse = json_decode($response, TRUE);
        return $jsonResponse;
    }

    public static function make_get_call($url, $token)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        $response = curl_exec($curl);
        if (empty($response)) {
            die(curl_error($curl));
            curl_close($curl);
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl);
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                $jsonResponse = json_decode($response, TRUE);
                return $jsonResponse;
            }
        }
        $jsonResponse = json_decode($response, TRUE);
        return $jsonResponse;
    }
}