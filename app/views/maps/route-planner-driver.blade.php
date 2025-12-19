<html>


<head>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>Driver Route Planner</title>
        <link href="<?=url('images/favicon.png');?>" rel="icon" type="image/png">

        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/animate.css') }}
        {{ HTML::style('css/dimmer.min.css') }}
        {{ HTML::style('css/loader.min.css') }}

        
        <!-- Custom Fonts -->
        {{ HTML::style('font-awesome/css/font-awesome.min.css') }}


<style>
    .container { padding-top: 0 !important; }
    .navbar-custom{ background-color: rgba(0,0,0,.075);color: #464545; }
    .navbar-custom h3{ font-size: 20px; font-weight: 700 !important;  }
    .navbar-brand-centered {
        position: absolute;
        left: 50%;
        display: block;
        text-align: center;
        background-color: transparent;
    }
    .navbar-brand-centered h3{margin:0;margin-left: -30px;margin-top:-2px}
        .navbar>.container .navbar-brand-centered,
        .navbar>.container-fluid .navbar-brand-centered {
            margin-left: -80px;
    }
    .navbar-brand-right-pull {
        position: absolute;
        margin-top: 25px;
        left: 75%;
    }
    .infoBlk { font-size: 11px; }
    /* loader */
    .loaderParent {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 64px;
        height: 64px;
    }
    .lds-ellipsis div {
        position: absolute;
        top: 27px;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: #ee8c68;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }
    .lds-ellipsis div:nth-child(1) {
        left: 6px;
        animation: lds-ellipsis1 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(2) {
        left: 6px;
        animation: lds-ellipsis2 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(3) {
        left: 26px;
        animation: lds-ellipsis2 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(4) {
        left: 45px;
        animation: lds-ellipsis3 0.6s infinite;
    }
    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }
        100% {
            transform: scale(1);
        }
    }
    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(0);
        }
    }
    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(19px, 0);
        }
    }
    /* loader ends */
</style>
</head>

<body>
    <div id="mapApp">
        <div id="page-wrapper" class="container">
            <div class="row">
                <nav class="navbar navbar-custom">
                  <div class="">
                    <div class="navbar-header" style="padding-left: 10px">
                      <div class="navbar-text"><h3>Driver(s) - Route Planner</h3></div>
                    </div>
                  </div>
                </nav>
            </div>

            <div class="row">
                <form class="form-inline" style="margin: 10" v-on:submit.prevent="getMarkers({{$driver_id}})" method="post">
                    @if (isset($drivers))
                    <div class="form-group">
                      <select v-model="driverId" class="form-control" required ref="search">
                        <option disabled value="">Please select driver</option>
                        @foreach ($drivers as $driver) 
                            <option value="{{$driver->id}}">{{$driver->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <button @click="setFocus()" type="submit" class="btn btn-danger form-control">Search</button>
                    @else
                    <input type="hidden" name="driverId" value="{{$driver_id}}">
                    <button type="submit" class="btn btn-danger form-control">Search</button>
                    @endif
                </form>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="loaderParent"><div v-if="loader" class="lds-ellipsis text-right"><div></div><div></div><div></div><div></div></div></div>
                    <gmap-map
                        id="map"
                        ref="map"
                        :center="center"
                        :zoom="zoom"
                        style="width: 100%; height: 500px">
                        <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen" @closeclick="infoWinOpen=false">
                        @{{ infoContent }}
                        </gmap-info-window>

                        <gmap-polygon v-if="mapLoaded" :path="paths" :editable="false" @paths_changed="updateEdited($event)"></gmap-polygon>

                        <ul v-if="edited" @click="edited = null">
                            <li v-for="path in edited">
                                <ol>
                                    <li v-for="point in path">
                                        @{{point.lat}}, @{{point.lng}}
                                    </li>
                                </ol>
                            </li>
                        </ul>

                        <gmap-marker
                        ref="markers"
                        v-if="mapLoaded"
                        :key="i" v-for="(m,i) in markers"
                        :position="m.position"
                        :clickable="true"
                        :title="m.name"
                        :label="String(i+1)"
                        :zIndex="i+100"
                        :animation="0"
                        @click="toggleInfoWindow(m,i)"></gmap-marker>
                    </gmap-map>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br>
                    <h3 class="text-muted">List Details:</h3>
                    <div class="loaderParent"><div v-if="loader" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>
                    <div class="alert alert-success infoBlk" v-if="driverShowInfo">
                        <p>
                            <strong>Driver Name : </strong>@{{ diverInfo.name }} <br>
                            <strong>Contact : </strong>@{{ diverInfo.contact_no }} <br>
                            <strong>Status : </strong>
                            <span v-if="diverInfo.status == 0">Not Active</span><span v-else>Active</span><br>
                            <strong>Total Distance : </strong>@{{ totalDistance }} km <br>
                            <strong>Total Duration : </strong>@{{ totalDuration }} <br>
                            <strong>Total Duration With Traffic : </strong>@{{ totalDurationWithTraffic }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4" v-if="driverListInfo" v-for="(m, index) in markers">
                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>Location @{{ index+1 }}</strong> - Lat: @{{ m.position.lat }} , Lng: @{{ m.position.lng }}</div>
                            <div class="panel-body">@{{ m.location_info.formatted_address }}</div>
                            <div class="panel-footer text-right"><small><strong>Distance:</strong> @{{ m.distance }}</small><small><strong>Duration:</strong> @{{ m.duration }}</small></div>
                        </div>
                    </div>

                    <div class="alert alert-info" v-if="alertInfo">
                        Not Available. <br>
                        <small>Try searching with valid Driver ID.</small>
                    </div>
                    <div class="alert alert-info" v-if="alertEmptyInfo">
                        Information Not Available. <br>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.0/vue.js"></script>
<script src="{{ asset('/vendors/vue-google-maps.js') }}"></script>
<script src="{{ asset('/vendors/axios.min.js') }}"></script>

<script>

    Vue.use(VueGoogleMaps, {
      load: {
        // key: 'AIzaSyDUFmbwJMBHU_paeMfVO7oqPC1IJEtbJUU',
        key: 'AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4',
        // libraries: 'places', // This is required if you use the Autocomplete plugin
      },
    });

    document.addEventListener('DOMContentLoaded', function() {


        var app = new Vue({
            el: '#mapApp',
            data: {
                showMap: false,
                selected: '',
                query: '',
                center: { lat: 3.1961575, lng: 101.6788476 },
                current: {},
                infoContent: [],
                infoWindowPos: null,
                infoWinOpen: false,
                currentMidx: null,
                //optional: offset infowindow so it visually sits nicely on top of our marker
                zoom: 9,
                infoOptions: {
                    pixelOffset: {
                    width: 0,
                    height: -35
                    },
                },
                driverShowInfo: false,
                driverListInfo: false,
                alertInfo: false,
                alertEmptyInfo: false,
                infoBlk: false,
                driverId: '',
                diverInfo: {},
                totalDistance: 0,
                totalDuration: '',
                totalDurationWithTraffic: '',
                locName: {},
                markers: [],
                mapLoaded: false,
                distance: 25,
                edited: null,
                paths: [],
                loader: false,
                selected_date: null,
                config: {
                      format: 'DD-MM-YYYY',
                      minimumView: 'day',
                      maximumView: 'day',
                      useCurrent: true,
                      showClear: true,
                      showClose: true,
                    }
                },

                watch: {

                    markers: {
                        handler: function (newMarkers, o) {
                            // console.log(newMarkers);
                            // Ensure we have markers in the list so that
                            if (newMarkers.length > 2) {
                                this.mapLoaded = true;
                                const bounds = new google.maps.LatLngBounds()
                                for (let m of newMarkers) {
                                bounds.extend({lat: m.position.lat, lng: m.position.lng})
                                }
                                this.$refs.map.fitBounds(bounds);
                                this.$refs.map.panToBounds(bounds);
                            }
                        },
                        deep: true
                    },
                },

                 mounted: function () {
                    // Try HTML5 geolocation.
                    // this.getCurrentLocation(function(pos) {
                    //     this.current = pos;console.log(current);
                    // });
                    this.geolocate();
                    this.getMarkers();
                    this.$refs.map;
                },

                methods: {
                    toggleInfoWindow: function(marker, idx) {
                        this.infoWindowPos = marker.position;
                        this.infoContent = marker.location_info.formatted_address;

                        //check if its the same marker that was selected if yes toggle
                        if (this.currentMidx == idx) {
                            this.infoWinOpen = !this.infoWinOpen;
                        }
                        //if different marker set infowindow to open and reset current marker index
                        else {
                            this.infoWinOpen = true;
                            this.currentMidx = idx;
                        }
                    },

                    updateEdited(mvcArray) {
                        let paths = [];
                        for (let i=0; i<mvcArray.getLength(); i++) {
                        let path = [];
                        for (let j=0; j<mvcArray.getAt(i).getLength(); j++) {
                            let point = mvcArray.getAt(i).getAt(j);
                            path.push({lat: point.lat(), lng: point.lng()});
                        }
                        paths.push(path);
                        }
                        this.edited = paths;
                    },

                    setFocus: function()
                    {
                        // Note, you need to add a ref="search" attribute to your input.
                        this.$refs.search.focus();
                    },

                    getMarkers: function (driverId) {
                        this.driverShowInfo = false;
                        this.driverListInfo = false;
                        this.alertEmptyInfo = false;
                        // Only after Google Maps loads...
                        if (driverId != null) {
                            this.driverId = driverId;
                        }
                        if (this.driverId == "") {
                            // alert("Please input required search field..");
                            // this.setFocus();
                            this.alertInfo = true;
                        } else {
                            // console.log(this.selected_date);

                            this.showMap = true;
                            this.loader = true;
                            this.alertInfo = false;
                            this.mapLoaded = false;

                            this.$refs.map.$mapPromise.then(() => {
                                // var url = '{{ route('get-drivers-location-details') }}?user='+this.driverId+'&s_date='+this.selected_date;
                                
                                var url = `/api/route-planner/driver/route?driver_id=${this.driverId}&lat=${this.center.lat}&lng=${this.center.lng}`;
                                axios.get(url).then(response => {

                                    if (response.data.error) {
                                        this.loader = false;
                                        this.alertInfo = true;
                                        this.alertEmptyInfo = false;
                                        alert('Error');
                                    }
                                    if (response.data.markers.length > 0) {
                                        this.mapLoaded = true
                                        this.markers = response.data.markers;
                                        this.paths = response.data.paths;
                                        this.diverInfo = response.data.diverInfo;
                                        this.totalDistance = response.data.totalDistance;
                                        this.totalDuration = response.data.totalDuration;
                                        this.totalDurationWithTraffic = response.data.totalDurationWithTraffic;
                                        this.loader = false;
                                        this.alertInfo = false;
                                        this.driverShowInfo = true;
                                        this.driverListInfo = true;
                                        this.alertEmptyInfo = false;
                                    } else {
                                        this.loader = false;
                                        this.alertInfo = false;
                                        this.alertEmptyInfo = true;
                                    }

                                });

                                // 2). Set "mapLoad" = true so we can add the Markers
                            })
                        }
                    },

                    geolocate: function() {
                        navigator.geolocation.getCurrentPosition(position => {
                            this.center = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                        });
                    },


                }
        })
    });

    </script>

</body>
</html>
