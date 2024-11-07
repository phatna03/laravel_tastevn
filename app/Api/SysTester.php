<?php

namespace App\Api;
//lib
use App\Models\Restaurant;
use App\Models\RestaurantFoodScan;
use App\Models\RestaurantFoodScanMissing;

class SysTester
{
  public static function photo_scan($img_url)
  {
    $api_url = 'http://171.244.46.137:9001/infer/workflows/tastvn/custom-workflow';
//    $img_url = 'https://s3.ap-southeast-1.amazonaws.com/cargo.tastevietnam.asia/58-5b-69-19-ad-83/SENSOR/1/2024-09-01/22/SENSOR_2024-09-01-22-03-38-968_124.jpg';

    $ch = curl_init();
    $headers = [
      'Content-Type: application/json',
      'Accept: application/json',
    ];

    $postData = [
      'api_key' => 'uYUCzsUbWxWRrO15iar5',
      'inputs' => [
        'image' => [
          'type' => 'url',
          'value' => $img_url
        ]
      ]
    ];

    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $datas = (array)json_decode($result);

    $datas = count($datas) && isset($datas['outputs']) ? (array)$datas['outputs'][0] : [];

    return $datas;
  }
}
