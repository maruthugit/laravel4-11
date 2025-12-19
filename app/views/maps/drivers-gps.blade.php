@extends('layouts.master')

@section('title') Drivers | Maps Gps Simulation @endsection

@section('extra-css')
<style>
    .navbar-custom{ background-color: rgba(0,0,0,.075);color: #464545; margin-top: 15px; }
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
        left: 60%;
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
@endsection

@section('content')
    <div id="mapApp">
        <div id="page-wrapper">
            <div class="row">
                <nav class="navbar navbar-custom">
                  <div class="container-fluid">
                    <div class="navbar-header" style="padding-left: 10px">
                      {{-- <a href="/campaigns/festival-campaigns" class="btn btn-default navbar-btn pull-left"><span class="glyphicon glyphicon-chevron-left"></span> Back</a> --}}
                      <div class="navbar-text"><h3>Driver(s) - Maps GPS Simulation</h3></div>
                          <div class="navbar-text navbar-brand-right-pull">
                            <form class="form-inline" v-on:submit.prevent="getMarkers" method="post">
                                <div class="form-group"> <!-- Date input -->
                                    {{-- <date-picker placeholder="Select Date" :format="customFormatter" v-model="selected_date" :config="config"></date-picker> --}}
                                    <input type="date" required placeholder="Choose Date" value="" v-model="selected_date" name="selected_date" id="selected_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <select v-model="driverId" class="form-control" required ref="search">
                                        <option disabled value="">Please select driver</option>
                                        <option v-for="res in qResults" v-bind:value="res.id">@{{res.name}}</option>
                                    </select>
                                    {{-- <span>Selected: @{{ res.name }}</span> --}}
                                </div>
                                <button @click="setFocus()" type="submit" class="btn btn-danger">Search</button>
                            </form>
                        </div>
                    </div>
                  </div>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12">
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
                            <span v-if="diverInfo.status == 0">Not Active</span><span v-else>Active</span>
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
                            <div class="panel-footer text-right"><small><strong>At:</strong> @{{ m.updated_at }}</small></div>
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
@endsection

@section('extra-js')
{{-- if vue cdn file is not enabled at master layout, enable the below link  --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.0/vue.js"></script>  --}}
<script src="{{ asset('/vendors/vue-google-maps.js') }}"></script>
<script src="{{ asset('/vendors/axios.min.js') }}"></script>
<script src="https://unpkg.com/vuejs-datepicker"></script>

<script>
    Vue.use(VueGoogleMaps, {
      load: {
        key: 'AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4',
        // libraries: 'places', // This is required if you use the Autocomplete plugin
      },
    });

    document.addEventListener('DOMContentLoaded', function() {

        Vue.component('date-picker', vuejsDatepicker);

        var app = new Vue({
            el: '#mapApp',
            data: {
                showMap: false,
                selected: '',
                query: '',
                qResults: [],
                center: { lat: 3.14524, lng: 101.123456 },
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

                    distance: function (n) {
                        this.getMarkers();
                        this.getDriver()
                    },

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
                    this.getMarkers();
                    this.getAllDrivers();
                    this.$refs.map;
                    var state = {
                      date: new Date(2016, 9,  16)
                    }
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

                    getMarkers: function () {
                        this.driverShowInfo = false;
                        this.driverListInfo = false;
                        this.alertEmptyInfo = false;
                        // Only after Google Maps loads...
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
                                var url = '{{ route('get-drivers-location-details') }}?user='+this.driverId+'&s_date='+this.selected_date;
                                axios.get(url).then(response => {
                                    if (response.data.markers.length > 0) {
                                        this.mapLoaded = true
                                        this.markers = response.data.markers;
                                        this.paths = response.data.paths;
                                        this.diverInfo = response.data.diverInfo;
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

                                })

                                // 2). Set "mapLoad" = true so we can add the Markers
                            })
                        }
                    },

                    getDriver: function () {
                        if (this.driverId == "") {
                            // alert("Please input required search field..");
                            this.form.driverId.focus();
                        } else {
                            this.$refs.map.$mapPromise.then(() => {
                                axios.get('{{ route('get-drivers-location-details') }}'+this.driverId)
                                .then(function (res) {
                                    this.markers = res.data.markers;
                                    this.paths = res.data.paths;
                                    // console.log(this.markers);
                                    this.mapLoaded = true;
                                })
                            })
                        }
                    },

                    getAllDrivers:  function () {
                        // this.qResults = [];
                        // if(this.query.length > 2){
                            axios.get('{{ route('get-drivers') }}',{params: {query: this.query}}).then(response => {
                                this.qResults = response.data;
                            });
                        // }
                    },

                    customFormatter(date) {
                      return moment(date).format('DD-MM-YYYY');
                    }

                }
        })
    });

    </script>
@endsection
