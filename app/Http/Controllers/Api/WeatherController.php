<?php

namespace App\Http\Controllers\Api;

use App\Helpers\OpenWeatherHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    protected $weatherHelper;

    public function __construct(OpenWeatherHelper $weatherHelper)
    {
        $this->weatherHelper = $weatherHelper;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(WeatherRequest $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        try {
            $result = $this->getWeather($latitude, $longitude);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }

        return response()->json($result);
    }

    private function getWeather($latitude, $longitude): array
    {
        $result = Cache::flexible(
            "coordinate-{$latitude}-{$longitude}",
            [86400, 172800],
            function () use ($latitude, $longitude) {
                try {
                    return $this->weatherHelper->getWeather(
                        $latitude,
                        $longitude,
                    );
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            },
        );
        return $result;
    }
}
