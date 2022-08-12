<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use accuweather;

class WeatherController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
    }

    public function getWeatherApi(){
        return view('welcome');
    }
}
