<?php

class GoogleTranslateForFree
{
    public static function translate($source, $target, $text, $attempts = 5) {
         $translation = self::requestTranslation($source, $target, $text, $attempts = 5);
         return $translation;
    }

    
    protected static function requestTranslationArray($source, $target, $text, $attempts) {
        $arr = [];
        foreach ($text as $value) {
            // timeout 0.5 sec
            usleep(500000);
            $arr[] = self::requestTranslation($source, $target, $value, $attempts = 5);
        }

        return $arr;
    }

  
    protected static function requestTranslation($source, $target, $text, $attempts){
        // Google translate URL
        $url = 'https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=uk-RU&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e';

        $fields = [
            'sl' => urlencode($source),
            'tl' => urlencode($target),
            'q'  => urlencode($text),
        ];

        if (strlen($fields['q']) >= 5000) {
            throw new \Exception('Maximum number of characters exceeded: 5000');
        }
        // URL-ify the data for the POST
        $fields_string = self::fieldsString($fields);

        $content = self::curlRequest($url, $fields, $fields_string, 0, $attempts);

        if (null === $content) {
            //echo $text,' Error',PHP_EOL;
            return '';
        } else {
            // Parse translation
            return self::getSentencesFromJSON($content);
        }
    }

   
    protected static function getSentencesFromJSON($json){
        $arr = json_decode($json, true);
        $sentences = '';

        if (isset($arr['sentences'])) {
            foreach ($arr['sentences'] as $s) {
                $sentences .= isset($s['trans']) ? $s['trans'] : '';
            }
        }

        return $sentences;
    }

   
    protected static function curlRequest($url, $fields, $fields_string, $i, $attempts) {
        $i++;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');

        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (false === $result || 200 !== $httpcode) {
            // echo $i,'/',$attempts,' Aborted, trying again... ',curl_error($ch),PHP_EOL;

            if ($i >= $attempts) {
                echo 'Could not connect and get data.',PHP_EOL;
                return;
            //die('Could not connect and get data.'.PHP_EOL);
            } else {
                // timeout 1.5 sec
                usleep(1500000);

                return self::curlRequest($url, $fields, $fields_string, $i, $attempts);
            }
        } else {
            return $result; //self::getBodyCurlResponse();
        }
        curl_close($ch);
    }

   
    protected static function fieldsString($fields)
    {
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }

        return rtrim($fields_string, '&');
    }
}



//Single
$source = 'en';
$target = 'ru';
$attempts = 5;
$text = 'Hello my name is Alexey';

$tr = new GoogleTranslateForFree();
$result = $tr->translate($source, $target, $text, $attempts);

var_dump($result);