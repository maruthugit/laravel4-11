  @extends('layouts.dashboard.main-v5')
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
          <div class="row bg-white has-shadow card-body">
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                <!-- <div class="icon bg-violet"><i class="icon-user"></i></div> -->
                <div class="title">
                  <span>Total Sales Today</span> <span class="number">{{ $total_sales_today }}</span>
                  <hr>
                  <span>Total Sales Yesterday</span> <span class="number">{{ $total_sales_yesterday }}</span>
                  <hr>
                  <span>Total Lazada Sales Today</span> <span class="number">{{ $total_lazada_sales_today }}</span>
                </div>
              </div>
            </div>
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                <div class="title">
                    <span>Total Orders Of The Week</span> <span class="number">{{ $total_sales_current_week }}</span>
                    <hr>
                    <span>Total Orders Of The Last Week</span> <span class="number">{{ $total_sales_previous_week }}</span>
                    <hr>
                    <span>Total Shopee Sales Today</span> <span class="number">{{ $total_shopee_sales_today }}</span>
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
                      <hr>
                      <span>Total PGMALL Sales Today</span> <span class="number">{{ $total_pgmall_sales_today }}</span>
                  </div>
              </div>
            </div>
            <!-- Item -->
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="title">
                        <span>Total Products</span> <span class="number">{{ $total_products }}</span>
                        <hr>
                        <span>Total Customers</span> <span class="number">{{ $total_customers }}</span>
                        <hr>
                        <span>Total lamboplace Sales Today</span> <span class="number">{{ $total_lamboplace_sales_today }}</span>
                    </div>
                </div>
              </div>
              <!-- Item -->
            <!-- <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                  <div class="title">
                      <span>Total Products</span> <span class="number">{{ $total_products }}</span>
                      <hr>
                      <span>Total Customers</span> <span class="number">{{ $total_customers }}</span>
                      <hr>
                      <span>Total Astro Go Shop Sales Today</span> <span class="number">{{ $total_astrogo_sales_today }}</span>
                  </div>
              </div>
            </div> -->
            <!-- Item -->
            <!-- <div class="col-xl-3 col-sm-6">
              <div class="item d-flex align-items-center">
                  <div class="title">
                      <span>Total Transactions</span> <span class="number">{{ $total_transactions }}</span>
                      <hr>
                      <span>Transactions Processed Today</span> <span class="number">{{ $total_transactions_processed_today }}</span>
                      <hr>
                      <span>Total Qoo10 Sales Today</span> <span class="number">{{ $total_qoo10_sales_today }}</span>
                  </div>
              </div>
            </div> -->
            <!-- Item -->
            <!-- <div class="col-xl-2 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="title mt-4">
                        <span>Total Transactions</span> <span class="number">{{ $total_transactions }}</span>
                    </div>
                </div>
            </div> -->
          </div>
        </div>
      </section>

      <!-- Dashboard Header Section    -->
      <section class="dashboard-header">
        <div class="container-fluid">
          <div class="row slide" style="">

            <!-- Platform Comparisons -->
            <div class="chart col-lg-8 col-12">

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
                <div class="cart-footer mt-3 load-platform-percentage card-body">                 
                  <div class="row text-left ml-2">
                        <div class="col-sm">
                            <img src="{{ asset('/jdboard-v2/images/platform-logos/pgmall.png') }}" class="platform-logo" alt="">
                            <div class="pgmallPercent" style="display:inline;"></div>
                        </div>
                        <!-- <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/prestomall-logo.png') }}" class="platform-logo" style="background-color: white" alt="">
                          <div class="prestomallPercent" style="display:inline;"></div>
                        </div> -->

                        <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/Lazada.png') }}" class="platform-logo" alt="">
                          <div class="lazadaPercent" style="display:inline;"></div>
                        </div>
                        
                        <div class="col-sm">
                            <img src="{{ asset('/jdboard-v2/images/platform-logos/lamboplace.png') }}" class="platform-logo" alt="">
                            <div class="lamboplacePercent" style="display:inline;"></div>
                        </div>

                        <!-- <div class="col-sm">
                          <img src="{{ asset('/jdboard-v2/images/platform-logos/Qoo10.png') }}" class="platform-logo" alt="">
                          <div class="qoo10Percent" style="display:inline;"></div>
                        </div> -->
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

                  <!-- <div class="row text-left ml-2 mt-3">
                    <div class="col-sm">
                      <img src="{{ asset('/jdboard-v2/images/platform-logos/astrogoshop.png') }}" class="platform-logo" alt="">
                      <div class="astrogoShopPercent" style="display:inline;"></div>
                    </div>
                  </div> -->
                </div>
              </div>

            </div>

            <!-- Sidebar 2 -->
            <div class="chart col-lg-4 col-12">

              <!-- Daily Sales - Bar Chart   -->
              <div class="bar-chart has-shadow bg-white">
                  <div class="card-header">
                    <h3 class="h4 title-green">Daily Sales</h3>
                  </div>
                  <div class="loader daily-loader" style="display:none;">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <canvas id="DailyStatusCanvas" class="card-body"></canvas>
              </div>

              <!-- Weekly Sales - Bar Chart   -->
              <div class="bar-chart has-shadow bg-white">
                <div class="card-header">
                  <h3 class="h4 title-green">Weekly Sales</h3>
                </div>
                <div class="loader monthly-loader" style="display:none;">
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                    <div class="loader-inner"></div>
                </div>
                <canvas id="barChartHome" class="card-body"></canvas>
              </div>

              <!-- Platform Sales - Bar Chart   -->
              <div class="bar-chart has-shadow bg-white">
                  <div class="card-header">
                    <h3 class="h4 title-green">Platform Sales</h3>
                  </div>
                  <div class="loader platforms-loader" style="display:none;">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <canvas id="PlatformStatusCanvas" class="card-body"></canvas>
              </div>
            </div>

          </div>
          <div class="row slide" style="">
            

            <!-- Top 5 Products -->
            <div class="statistics col-lg-6 col-12">
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
            </div>

              <!-- Top 5 Products Last Week -->
            <div class="statistics col-lg-6 col-12">
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
            </div>
          </div>

          <div class="row slide" style="">
            <div class="col-12">
              <div class="card-header">
                  <h3 class="h4 title-green">Stock</h3>
                </div>
                <div class="card-body no-padding">
                  <div class="loader procat-loader">
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                      <div class="loader-inner"></div>
                  </div>
                  <!-- Item-->
                  <div class="stock bg-white"></div>
                </div>
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
      {{ HTML::script('jdboard-v2/js/main-v5.js') }}
  @stop
