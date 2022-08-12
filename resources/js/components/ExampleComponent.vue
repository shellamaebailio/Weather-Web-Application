<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log(process.env.MIX_ACCUWEATHER_URL);
            axios.get('https://dataservice.accuweather.com/forecasts/v1/daily/5day/264908?apikey=ov3QLSx8CTPp6k5ielDxfBK0LVFXnPd3')
            .then(response => {
                console.log('res',response)
            }).catch(error => {
                console.log(error)
            });
        }
    }
</script>
