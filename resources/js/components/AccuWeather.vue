<template>
    <div class="container">
        <div class="input-group mb-3">
            <input type="text" v-model="cityVar" class="form-control" placeholder="Search City" aria-label="">
        </div>
        <div class="search-container">
            <table class="table table-sm location-table">
            <tbody>
                <tr @click="requestWeatherForThisCity(city, false)" v-for="city in cityResultsFiltered">
                    <td>{{ city.LocalizedName }} {{ city.Country.LocalizedName }}</td>
                </tr>
            </tbody>
            </table>
        </div>
        <h3 class="currentlocation">{{ currentLocation }}</h3>
        <div class="row justify-content-center top-row">
            <div class="col col-4" v-for="weather in weatherData" :key="weather.id">
                <div class="card">
                    <div class="main">
                        <h5 class="card-title date">{{ weather.Date | toDate }}</h5>
                        <div class="temperature">{{ parseInt(Math.round((weather.Temperature.Maximum.Value + weather.Temperature.Minimum.Value) / 2)) }} °{{weather.Temperature.Maximum.Unit}}</div>
                        <div class="temperature-details">{{ weather.Temperature.Minimum.Value }} °{{weather.Temperature.Maximum.Unit}} to {{ weather.Temperature.Maximum.Value }} °{{weather.Temperature.Maximum.Unit}}</div>
                        <div class="row daynight">
                            <div class="col border-right">
                                <label class="bold" for="">Day</label>
                                <div>
                                    <img :src="'accuweathericons/'+ weather.Day.Icon + '.png'" :alt="weather.IconPhrase">
                                    <p class="precipitation">{{ weather.Day.ShortPhrase }}</p>
                                    <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Rain</th>
                                            <td>{{ weather.Day.Rain.Value }} {{ weather.Day.Rain.Unit }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Hours of Rain</th>
                                            <td>{{ weather.Day.HoursOfRain }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Wind</th>
                                            <td>{{ weather.Day.Wind.Direction.English }} {{ weather.Day.Wind.Direction.Degrees }} : {{ weather.Day.Wind.Speed.Value }} {{ weather.Day.Wind.Speed.Unit }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Wind</th>
                                            <td>{{ weather.Day.WindGust.Direction.English }} {{ weather.Day.WindGust.Direction.Degrees }} : {{ weather.Day.WindGust.Speed.Value }} {{ weather.Day.WindGust.Speed.Unit }}</td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col">
                                <label class="bold" for="">Night</label>
                                <div>
                                    <img :src="'accuweathericons/'+ weather.Night.Icon + '.png'" :alt="weather.IconPhrase">
                                    <p class="precipitation">{{ weather.Night.ShortPhrase }}</p>
                                    <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Rain</th>
                                            <td>{{ weather.Night.Rain.Value }} {{ weather.Night.Rain.Unit }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Hours of Rain</th>
                                            <td>{{ weather.Night.HoursOfRain }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Wind</th>
                                            <td>{{ weather.Night.Wind.Direction.English }} {{ weather.Night.Wind.Direction.Degrees }} : {{ weather.Night.Wind.Speed.Value }} {{ weather.Night.Wind.Speed.Unit }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Wind Gust</th>
                                            <td>{{ weather.Night.WindGust.Direction.English }} {{ weather.Night.WindGust.Direction.Degrees }} : {{ weather.Night.WindGust.Speed.Value }} {{ weather.Night.WindGust.Speed.Unit }}</td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- source -->
        <div class="source">Source: AccuWeather</div>
    </div>
</template>
<script>
    import moment from 'moment'
    export default {
        filters: {
            toDate: function (value) {
               if (value) {
                    return moment(String(value)).format('MMMM DD, YYYY')
                }
            }
        },
        data: function () {
            return {
                weatherData: [],
                isDayTime: false,
                cityVar: '',
                cityResults: [],
                currentLocation: ''
            }
        },
        async mounted() {
            var scope = this
            scope.getWeather()
            scope.dayTime()
            await scope.getAllCity()
        },
        computed: {
            cityResultsFiltered(){
                var limitedCity = []
                if(this.cityResults){
                    limitedCity = this.cityResults.filter(item => {
                        return !this.cityVar || (item.LocalizedName ? (item.LocalizedName.toLowerCase().includes(this.cityVar.toLowerCase())) : '') ||
                        (item.EnglishName ? (item.EnglishName.toLowerCase().includes(this.cityVar.toLowerCase())) : '') ||
                        (item.Country.LocalizedName ? (item.Country.LocalizedName.toLowerCase().includes(this.cityVar.toLowerCase())) : '');
                    })
                }

                return limitedCity
            }
        },
        methods: {
            getWeather(){
                var scope = this
                // default is cagyaan de oro
                scope.requestWeatherForThisCity(process.env.MIX_ACCUWEATHER_LOC, true)
                scope.currentLocation = "Cagayan de Oro City, Philippines"
            },

            dayTime(){
                var scope = this
                const hours = new Date().getHours()
                scope.isDayTime = hours > 6 && hours < 20
            },
            async getAllCity(){
                // GEO LOCATION
                var scope = this
                var params = {
                    apikey: process.env.MIX_ACCUWEATHER_KEY,
                    details: true,
                }

                try{
                    let response = await axios.get(process.env.MIX_ACCUWEATHER_LOCATION_SEARCH, {params})
                    scope.cityResults = await response.data
                }catch(error){
                    console.log(error)
                }
            },

            requestWeatherForThisCity(key, defaultCity){
                var scope = this
                var locationKey = ''
                if(!defaultCity){
                    scope.currentLocation = key.LocalizedName +', '+ key.Country.LocalizedName
                    locationKey = key.Key
                }else{
                    locationKey = key
                }
                
                var params = {
                    apikey: process.env.MIX_ACCUWEATHER_KEY,
                    details: true,
                    metric: true
                }
                axios.get(process.env.MIX_ACCUWEATHER_URL+'/'+process.env.MIX_ACCUWEATHER_TIME+'/'+locationKey, {params})
                .then(response => {
                    scope.weatherData = response.data.DailyForecasts.slice(0, 3)
                    console.log('scope.weatherData', scope.weatherData)
                }).catch(error => {
                    console.log(error)
                });
            }
        },
    }
</script>