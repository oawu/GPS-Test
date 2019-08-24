<?php

class Signal extends ApiController {
  
  public function index() {

    Load::systemLib('Html');
    Load::systemLib('Asset');
    Load::lib('CRUD');

    $total = \M\Signal::count();
    $pages = \CRUD\Table\Pagination::info($total);

    $signals = \M\Signal::all([
     'order'  => 'id DESC',
     'offset' => $pages['offset'],
     'limit'  => $pages['limit']]);


    $asset = Asset::create()
         ->addCSS('/Asset/css/Api/Signal.css');

    return View::create('Api/Signal/index.php')
               ->with('signals', $signals)
               ->with('pages', $pages['links'])
               ->with('asset', $asset);
  }
  
  public function create() {
    $params = Input::ValidPost(function($params) {
      Validator::must($params, 'latitude', '緯度')->isLat();
      Validator::must($params, 'longitude', '經度')->isLng();
      Validator::optional($params, 'pressure', '氣壓')->default(null)->isNumber();
      Validator::optional($params, 'temperature', '溫度')->default(null)->isNumber();
      Validator::optional($params, 'humidity', '濕度')->default(null)->isNumber();
      Validator::optional($params, 'timeAt', '時間')->default(null)->isDatetime();
      Validator::optional($params, 'cntSatellite', '衛星數量')->default(0)->isInteger(0);
      return $params;
    });

    transaction(function() use (&$params, &$obj) {
      return $obj = \M\Signal::create($params);
    });

    return [
      'id' => $obj->id,
      'latitude' => $obj->latitude,
      'longitude' => $obj->longitude,
      'pressure' => $obj->pressure,
      'temperature' => $obj->temperature,
      'humidity' => $obj->humidity,
      'timeAt' => $obj->timeAt,
      'cntSatellite' => $obj->cntSatellite,
    ];
  }
}