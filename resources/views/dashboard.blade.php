@extends('layouts.app')

@section('content')
  <div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 space-y-6 xl:col-span-12">
      <x-dashboard.dashboard-metrics :users="$users" :tasks="$tasks"/>
    </div>
  </div>
@endsection

@push('scripts')
    <script>
    function getWeatherByLocation() {
        if (!navigator.geolocation) {
            console.log("Browser kamu tidak mendukung geolocation");
            return;
        }

        navigator.geolocation.getCurrentPosition(success, error, {
            enableHighAccuracy: true,
            timeout: 10000
        });
    }
    function success(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        console.log("Latitude:", lat);
        console.log("Longitude:", lon);

        axios.post('/api/weather/update', {
            latitude: lat,
            longitude: lon
        })
        .then(res => {
            console.log(res.data);
            document.getElementById('weather-temperature').textContent = res.data.temperature;
            document.getElementById('weather-description').textContent = res.data.weather;
            document.getElementById('weather-icon').src = res.data.icon_url;
        })
        .catch(err => console.error(err));
    }
    function error(err) {
        console.error("Error mengambil lokasi:", err);
    }
    getWeatherByLocation()
    </script>
@endpush
