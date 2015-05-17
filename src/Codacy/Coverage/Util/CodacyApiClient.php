<?php

namespace Codacy\Coverage\Util;

/**
 * Class ApiClient
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class CodacyApiClient
{

    //TODO: do it with fsockopen
    /**
     * @param string $url  url to post to
     * @param string $data the JSON data
     *
     * @return string      sucess message
     *
     * @throws \Exception   when remote server response
     */
    static function postData($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json")
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($status < 200 || $status > 201) {
            throw new \Exception(
                sprintf("Error: call to URL %s failed with status %s, response %s, curl_error %u",
                         $url, $status, $json_response, curl_error($curl), curl_errno($curl)
                )
            );
        }

        curl_close($curl);

        $json = json_decode($json_response, true);
        return $json['success'];
    }
}
