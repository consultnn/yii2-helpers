<?php

namespace consultnn\helpers;

use Yii;

/**
 * Class InternetCheckerHelper
 * @package common\helpers
 */
class InternetChecker {

    /**
     * @param $address
     * @return bool
     */
    public static function urlAvailable($address)
    {
        if($address == NULL)
            return false;
        $host = parse_url($address, PHP_URL_HOST);
        $address = mb_ereg_replace($host, idn_to_ascii($host), $address);

        $ch = curl_init($address);
        curl_setopt_array($ch, [
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode>=200 && $httpCode<300;
    }

    /**
     * @param $email
     * @return array|bool|string
     */
    public static function emailAvailable($email)
    {
        if($email == NULL)
            return false;

        return self::verifyEmail($email, Yii::$app->params['adminEmail']);
    }

    /**
     * @param $toEmail
     * @param $fromEmail
     * @param bool $getDetails
     * @author github.com/hbattat
     * @link https://github.com/hbattat/verifyEmail
     * @return array|bool|string
     */
    private static function verifyEmail($toEmail, $fromEmail, $getDetails = false){
        $emailArr = explode("@", $toEmail);
        $domain = array_slice($emailArr, -1);
        $domain = $domain[0];
        $details = '';
        // Trim [ and ] from beginning and end of domain string, respectively
        $domain = ltrim($domain, "[");
        $domain = rtrim($domain, "]");
        if( "IPv6:" == substr($domain, 0, strlen("IPv6:")) ) {
            $domain = substr($domain, strlen("IPv6") + 1);
        }

        $mxHosts = [];
        $mxWeight = [];

        if( filter_var($domain, FILTER_VALIDATE_IP) ) {
            $mxIp = $domain;
        }
        else {
            getmxrr($domain, $mxHosts, $mxWeight);
        }

        if(!empty($mxHosts))
            $mxIp = $mxHosts[array_search(min($mxWeight), $mxHosts)];
        else {
            if( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
                $recordA = dns_get_record($domain, DNS_A);
            }
            elseif( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
                $recordA = dns_get_record($domain, DNS_AAAA);
            }
            if( !empty($recordA) )
                $mxIp = $recordA[0]['ip'];
            else {
                $result   = false;
                $details .= "No suitable MX records found.";
                return ( (true == $getDetails) ? [$result, $details] : $result );
            }
        }

        $errNo = null;
        $errStr = null;
        $connect = @fsockopen($mxIp, 25, $errNo, $errStr, 5);
        if($connect) {
            if(preg_match("/^220/i", $out = fgets($connect, 1024))) {
                fputs ($connect , "HELO $mxIp\r\n");
                $out = fgets ($connect, 1024);
                $details .= $out."\n";

                fputs ($connect , "MAIL FROM: <$fromEmail>\r\n");
                $from = fgets ($connect, 1024);
                $details .= $from."\n";
                fputs ($connect , "RCPT TO: <$toEmail>\r\n");
                $to = fgets ($connect, 1024);
                $details .= $to."\n";
                fputs ($connect , "QUIT");
                fclose($connect);
                if(!preg_match("/^250/i", $from) || !preg_match("/^250/i", $to)) {
                    $result = false;
                } else {
                    $result = true;
                }
            }
        } else {
            $result = false;
            $details .= "Could not connect to server";
        }

        if($getDetails){
            return [$result, $details];
        }
        else{
            return $result;
        }
    }
}
