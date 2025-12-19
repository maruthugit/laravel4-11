  @extends('layouts.dashboard.main1')
  @section('title') Dashboard | CMS | Jocom @stop
  @section('additional-scripts-on-top')
  <meta name="_token" content="{{ csrf_token() }}"/>
  <script type="text/javascript">
      // Time Loader
      function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        var ampm = h >= 12 ? 'pm' : 'am';
        document.getElementById('timeTxt').innerHTML = h + ":" + m + ":" + s + " "  + ampm;
        var t = setTimeout(startTime, 500);
      }
      function checkTime(i) {
        if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
        return i;
      }
      // auto refresh per 3m
      setInterval(function(){
        window.location.reload();
      }, 1000000);
  </script>
  @stop
  @section('content')
      <!-- Dashboard Counts Section-->
      <section class="dashboard-counts no-padding-bottom">
        <div class="container-fluid">
          <div class="row bg-white has-shadow">
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                <!-- <div class="icon bg-violet"><i class="icon-user"></i></div> -->
                <div class="title">
                  <span>Total Sales Today</span> <span class="number">{{ $total_sales_today }}</span>
                  <hr>
                  <span>Total Sales Yesterday</span> <span class="number">{{ $total_sales_yesterday }}</span>
                </div>
              </div>
            </div>
            <!-- Item -->
            <div class="col-xl-4 col-sm-6">
              <div class="item d-flex align-items-center">
                <div class="title">
                    <span>Total Orders Of The Week</span> <span class="number">{{ $total_sales_current_week }}</span>
                    <hr>
                    <span>Total Orders Of The Last Week</span> <span class="number">{{ $total_sales_previous_week }}</span>
                </div>
              </div>
            </div>
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                  <div class="title">
                      <span>Total Products</span> <span class="number">{{ $total_products }}</span>
                      <hr>
                      <span>Total Customers</span> <span class="number">{{ $total_customers }}</span>
                  </div>
              </div>
            </div>
            <!-- Item -->
            <div class="col-xl-2 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="title mt-4">
                        <span>Total Transactions</span> <span class="number">{{ $total_transactions }}</span>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </section>

      <!-- Dashboard Header Section    -->
      <section class="dashboard-header">
        <div class="container-fluid">
          <div class="row">
            <!-- Sidebar 1 -->
            <div class="statistics col-lg-3 col-12">
                
              <!-- E-Buzz   -->

              {{-- <div class="overdue card bg-white">
                  <div class="card-body">
                  <h3 class="title-green">E-Buzz</h3><small>April W1 - 11 Street</small>
                  <div class="number text-center text-white">$20,000</div>
                  <div class="chart">
                      <canvas id="lineChart1"></canvas>
                  </div>
                  </div>
              </div> 

              <!-- Top 5 Products Last Month -->
              <div class="daily-feeds card bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green">Top 10 Undelivered Items</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="top-undelivered"></div>
                </div>
              </div>

              <!-- Top 5 Products Last Month -->
              <div class="daily-feeds card bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green">Top 10 Undelivered Items With Zero Stock/Inventory</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="top-undelivered-0-stock"></div>
                </div>
              </div> --}}
              
              <!-- Top 5 Products -->
              <div class="daily-feeds card bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green top-5-prod-curweek">Top 5 Products This Week</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="top-prodcuts"></div>
                </div>
              </div>

              <!-- Top 5 Products Last Month -->
              <div class="daily-feeds card bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green top-5-prod-lastweek">Top 5 Products Last Week</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="top-prodcuts-last-week"></div>
                </div>
              </div>

              <!-- Top 5 Categories -->
              <div class="daily-feeds card bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green top-5-cat">Top 5 Categories</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="top-categories"></div>
                </div>
              </div> 
              
              <!-- Platform - Pie Chart -->
             {{--  <div class="pie-chart bg-white card">
                <div class="card-header d-flex align-items-center">
                  <h3 class="h4 title-green">Mobile Platform</h3>
                </div>
                <div class="card-body">
                  <div class="loader mobiles-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <canvas id="pieChart"></canvas>
                </div>
              </div>
                --}}
            </div> 

            <!-- Platform Comparisons -->
            <div class="chart col-lg-6 col-12">

              <div class="line-chart bg-white card no-padding">
                <div class="card-header d-flex align-items-center">
                  <h3 class="h4 title-green platform-compare-lbl">Platform Comparison</h3>
                </div>
                <div class="card-body line-chart d-flex has-shadow">
                    <div class="loader comparison-loader">
                        <div class="loader-inner"></div>
                        <div class="loader-inner"></div>
                        <div class="loader-inner"></div>
                    </div>
                  <canvas id="lineCahrt"></canvas>
                </div>
                <div class="cart-footer mt-3 load-platform-percentage">                 
                  <div class="row text-left ml-2">
                        <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/prestomall-logo.png') }}" class="platform-logo" style="background-color: white" alt="">
                          <div class="prestomallPercent" style="display:inline;"></div>
                        </div>

                        <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/Lazada.png') }}" class="platform-logo" alt="">
                          <div class="lazadaPercent" style="display:inline;"></div>
                        </div>
                        
                        <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/Qoo10.png') }}" class="platform-logo" alt="">
                          <div class="qoo10Percent" style="display:inline;"></div>
                        </div>
                  </div>

                  <div class="row text-left ml-2 mt-3">
                    <div class="col-sm">
                      <img src="{{ asset('/jdboard-v2/images/platform-logos/shopee.png') }}" class="platform-logo" alt="">
                      <div class="shopeePercent" style="display:inline;"></div>
                    </div>
                    
                    <div class="col-sm">
                        <img src="{{ asset('/jdboard-v2/images/platform-logos/Jocom.png') }}" class="platform-logo" alt="">
                        <div class="appPercent" style="display:inline;"></div>
                    </div> 
                    
                    <div class="col-sm">
                      <img src="{{ asset('/jdboard-v2/images/platform-logos/offline.png') }}" class="platform-logo" alt="">
                      <div class="offlinePercent" style="display:inline;"></div>
                    </div>
                  </div>

                  <div class="row text-left ml-2 mt-3">
                    <div class="col-sm">
                      <img src="{{ asset('/jdboard-v2/images/platform-logos/astrogoshop.png') }}" class="platform-logo" alt="">
                      <div class="astrogoShopPercent" style="display:inline;"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="line-chart bg-white card no-padding">
                <div class="card-header d-flex align-items-center">
                  <h3 class="h4 title-green">Distribution Of Buyer By Region (Top 5 Regions)</h3>
                </div>
                <div class="card-body line-chart d-flex has-shadow">
                <div class="container">

                  <div class="row">
                      <div class="col-sm-12 mb-2 text-left">
                          <div class="loader region-loader">
                              <div class="loader-inner"></div>
                              <div class="loader-inner"></div>
                              <div class="loader-inner"></div>
                          </div>
                          <div class="top-regions"></div>
                      </div>
                      <!--<div class="col-sm-12">-->
                      <!--    <div id="visualization"> </div>-->
                      <!--</div>-->
                  </div>
                </div>

                </div>
              </div>

            </div>

            <!-- Sidebar 2 -->
            <div class="chart col-lg-3 col-12">
              
            <!-- Daily Sales - Bar Chart   -->
            <div class="bar-chart has-shadow bg-white">
                <div class="title"><strong class="text-green">Daily Sales</strong></div>
                <div class="loader daily-loader" style="display:none;">
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                </div>
                <canvas id="DailyStatusCanvas"></canvas>
            </div>

              <!-- Monthly Sales - Bar Chart   -->
              <div class="bar-chart has-shadow bg-white">
                <div class="title"><strong class="text-green">Monthly Sales</strong></div>
                <div class="loader monthly-loader" style="display:none;">
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                </div>
                <canvas id="barChartHome"></canvas>
              </div>

              <!-- Platform Sales - Bar Chart   -->
              <div class="bar-chart has-shadow bg-white">
                  <div class="title"><strong class="text-green">Platform Sales</strong></div>
                  <div class="loader platforms-loader" style="display:none;">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <canvas id="PlatformStatusCanvas"></canvas>
              </div>

            </div>
          </div>
        </div>
      </section>
  @stop
  @section('scripts')
      <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
      {{ HTML::script('jdboard-v2/vendor/jquery/jquery.min.js') }}
      {{ HTML::script('jdboard-v2/vendor/popper.js/umd/popper.min.js') }}
      {{ HTML::script('jdboard-v2/vendor/bootstrap/js/bootstrap.min.js') }}
      {{ HTML::script('jdboard-v2/vendor/jquery.cookie/jquery.cookie.js') }}
      {{ HTML::script('jdboard-v2/vendor/chart.js/Chart.min.js') }}
      {{ HTML::script('jdboard-v2/js/chart.piecelabel.js') }}
      {{ HTML::script('jdboard-v2/js/front.js') }}
      {{ HTML::script('jdboard-v2/js/main.js') }}
  @stop
