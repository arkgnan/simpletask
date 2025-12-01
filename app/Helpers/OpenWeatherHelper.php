<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWeatherHelper
{
    const WEATHER_API_URL = "https://api.openweathermap.org/data/2.5/weather";
    /*
     * @param float $latitude
     * @param float $longitude
     * @return array
     * @throws \Exception
     */
    public static function getWeather($latitude, $longitude): array
    {
        $apiKey = config("weather.api_key");
        if (empty($apiKey)) {
            throw new \Exception("API key is missing");
        }

        $url =
            self::WEATHER_API_URL .
            "?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";
        try {
            $response = Http::get($url);
            $data = $response->json();
            if ($response->status() !== 200) {
                Log::error("Failed to fetch weather data: {$response->body()}");
                throw new \Exception("Failed to fetch weather data");
            }
            if (!isset($data["main"]) || !isset($data["weather"])) {
                Log::error("Invalid weather data: {$response->body()}");
                throw new \Exception("Invalid weather data");
            }
            Log::info("Weather data fetched successfully: {$response->body()}");
            return [
                "temperature" => $data["main"]["temp"],
                "humidity" => $data["main"]["humidity"],
                "pressure" => $data["main"]["pressure"],
                "wind_speed" => $data["wind"]["speed"],
                "description" => $data["weather"][0]["description"],
                "weather" => $data["weather"][0]["main"],
                "icon" => $data["weather"][0]["icon"],
                "icon_url" => "http://openweathermap.org/img/wn/{$data["weather"][0]["icon"]}@2x.png",
            ];
        } catch (\Exception $e) {
            Log::error("Failed to fetch weather data: {$e->getMessage()}");
            throw new \Exception("Failed to fetch weather data");
        }
    }
}
