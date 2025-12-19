<?php $totalPending = Task::where("assign_to",Session::get("user_id"))->where("status",0)->count()?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a class="active" href="/home"><i class="fa fa-home fa-fw"></i> Dashboard</a>
            </li>
             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 27, 15, 'OR'))
                <ul class="nav nav-second-level">
                <li>
                <!--<a class="active" href="/home/v2"><i class="fa fa-fw"></i> Dashboard V2</a>-->
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 27, 'home/v2'))>
                        {{ HTML::link('home/v2', 'Dashboard Overall', array('class' => Request::is('home/v2') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 27, 'jlogistic/dashboard'))>
                        {{ HTML::link('jlogistic/dashboard', 'Logistic Dashboard', array('class' => Request::is('jlogistic/dashboard') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 27, 'jlogistic/dashboardregion'))>
                        {{ HTML::link('jlogistic/dashboardregion', 'Logistic Statistics', array('class' => Request::is('jlogistic/dashboardregion') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 27, 'jlogistic/tracking'))>
                        {{ HTML::link('jlogistic/tracking', 'Logistic Tracking', array('class' => Request::is('jlogistic/tracking') ? 'active' : '')) }}
                    </li @endif>
                </li>
                </ul>
             @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 25, 15, 'OR'))
            <li {{(Request::is('task*') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-comments"></i> Customer Service <span class="badge badge-warning"><?php echo $totalPending; ?></span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 25, 'task'))>
                        {{ HTML::link('task', 'Ticketing', array('class' => Request::is('task') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 25, 'task/create'))>
                        {{ HTML::link('task/create', 'Create Ticketing', array('target' => '_blank', 'class' => Request::is('task/create') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 25, 'task/report'))>
                        {{ HTML::link('task/report', 'Ticketing Report', array('target' => '_blank', 'class' => Request::is('task/report') ? 'active' : '')) }}
                    </li @endif>                    
                </ul>
            </li>
            @endif
            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'deddie'), true ) ) {  ?>
                <li>
                    <a class="" href="/home/index2"><i class="fa fa-home fa-fw"></i> Dashboard 1</a>
                </li>
            <?php  } ?>
             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
            <li {{(Request::is('product/*', 'campaigns/*') || Request::is('product') ? 'class="active"' : '')}} {{(Request::is('inventory*') ? 'class="active"' : '')}} >
                <a href="#">
                    <i class="fa fa-tags fa-fw"></i> Products<span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">                   
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product'))>
                        {{ HTML::link('product', 'View All', array('class' => Request::is('product') ? 'active' : '')) }}
                    </li @endif>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND') && Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/create'))
                        <li>
                            {{ HTML::link('product/create', 'Add New Product', array('class' => Request::is('product/create') ? 'active' : '')) }}
                        </li>
                         
                    @endif
                    <?php if (Session::get('branch_access') != 1) { ?>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/category'))>
                            {{ HTML::link('product/category', 'View Category', array('class' => Request::is('product/category') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND') && Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/category/create'))
                            <li>
                                {{ HTML::link('product/category/create', 'Add New Category', array('class' => Request::is('product/category/create') ? 'active' : '')) }}
                            </li>
                        @endif
                    
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/package'))>
                            {{ HTML::link('product/package', 'Product Package', array('class' => Request::is('product/package') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND') && Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/package/create'))
                            <li>
                                {{ HTML::link('product/package/create', 'Add Package', array('class' => Request::is('product/package/create') ? 'active' : '')) }}
                            </li>
                            <li>
                                {{ HTML::link('imports/createimport', 'Import Product', array('class' => Request::is('imports/createimport') ? 'active' : '')) }}
                            </li>
                        @endif
                    
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'inventory'))>
                            {{ HTML::link('inventory', 'Inventory History', array('class' => Request::is('inventory') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/masterinventory'))>
                            {{ HTML::link('product/masterinventory', 'Master Inventory', array('class' => Request::is('product/masterinventory') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productcampaign'))>
                            {{ HTML::link('product/productcampaign', 'Campaign', array('class' => Request::is('product/productcampaign') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productlivestream'))>
                            {{ HTML::link('product/productlivestream', 'Live Streaming', array('class' => Request::is('product/productlivestream') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productboostdeals'))>
                            {{ HTML::link('product/productboostdeals', 'Boost Exclusive Deals ', array('class' => Request::is('product/productboostdeals') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productofficepantry'))>
                            {{ HTML::link('product/productofficepantry', 'Office Pantry', array('class' => Request::is('product/productofficepantry') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productcrossborder'))>
                            {{ HTML::link('product/productcrossborder', 'Cross Border', array('class' => Request::is('product/productcrossborder') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productjocomelevendeals'))>
                            {{ HTML::link('product/productjocomelevendeals', 'tmGrocer 11.11 Deals', array('class' => Request::is('product/productjocomelevendeals') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productboost11deals'))>
                            {{ HTML::link('product/productboost11deals', 'Boost 11.11 Deals', array('class' => Request::is('product/productboost11deals') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/Productjocomfeatured'))>
                            {{ HTML::link('product/productjocomfeatured', 'Jocom Featured Products', array('class' => Request::is('product/Productjocomfeatured') ? 'active' : '')) }}
                        </li @endif>
                    <!-- <li>-->
                    <!--    {{ HTML::link('product/boostonlinestore', 'Boost Online Store', array('class' => Request::is('product/boostonlinestore') ? 'active' : '')) }}-->
                    <!--</li>-->
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/ecommunity'))>
                            {{ HTML::link('product/ecommunity', 'eCommunity Store', array('class' => Request::is('product/ecommunity') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/mycashonline'))>
                            {{ HTML::link('product/mycashonline', 'MyCashOnline Store', array('class' => Request::is('product/mycashonline') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/productbase'))>
                            {{ HTML::link('product/productbase', 'Base Products', array('class' => Request::is('product/productbase') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/history'))>
                            {{ HTML::link('product/history', 'Product History', array('class' => Request::is('product/history') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/costprice'))>
                         {{ HTML::link('product/costprice', 'Cost Price Listing',array('class' => Request::is('product/costprice') ? 'active' : '')) }}
                          </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/festival-campaigns'))>
                            {{ HTML::link('campaigns/festival-campaigns', 'Festival Campaigns', array('class' => Request::is('campaigns/*') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'foc'))>
                            {{ HTML::link('product/foc', 'FOC Product', array('class' => Request::is('product/foc') ? 'active' : '')) }}
                        </li @endif>
                        <li {{(Request::is('product/bulkedit*') ? 'class="active"' : '')}}>
                            <a href="#"> Bulk Edit<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/bulkeditstatus'))>
                                    {{ HTML::link('product/bulkeditstatus', 'Bulk Edit Status', array('class' => Request::is('product/bulkeditstatus') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/bulkeditquantity'))>
                                    {{ HTML::link('product/bulkeditquantity', 'Bulk Edit Quantity', array('class' => Request::is('product/bulkeditquantity') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 2, 'product/bulkeditactualstock'))>
                                    {{ HTML::link('product/bulkeditactualstock', 'Bulk Edit Actual Stock', array('class' => Request::is('product/bulkeditactualstock') ? 'active' : '')) }}
                                </li @endif>
                            </ul>
                        </li>

                    <?php } ?>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 16, 1, 'OR'))
            <li {{(Request::is('product_update/*') || Request::is('product-update/*') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-tags fa-fw"></i> Product Update<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 16, 'product_update/export'))>
                        {{ HTML::link('product_update/export', 'Export Product Details', array('class' => Request::is('product_update/export') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 16, 'product_update/import'))>
                        {{ HTML::link('product_update/import', 'Import Product Details', array('class' => Request::is('product_update/import') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 16, 'product-update/import'))>
                        {{ HTML::link('product-update/import', 'Import Product Price', array('class' => Request::is('product-update/import') ? 'active' : '')) }}
                    </li @endif>
                </ul>
                <!-- /.nav-second-level -->
            </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 1, 'OR'))
            <li {{(Request::is('comment*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-comments-o"></i> Comments<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 3, 'comment'))>
                            {{ HTML::link('comment', 'Comments Listing', array('class' => Request::is('comment') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 5, 'AND') && Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 3, 'comment/create'))
                            <li>
                                {{ HTML::link('comment/create', 'Add Comments', array('class' => Request::is('comment/create') ? 'active' : '')) }}
                            </li>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 1, 'OR'))
            <li {{(Request::is('transaction*') ? 'class="active"' : '')}} {{(Request::is('processorDashboard*') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Transaction<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction'))>
                        {{ HTML::link('transaction', 'Transaction Listing', array('class' => Request::is('transaction') ? 'active' : '')) }}
                    </li @endif>
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'processorDashboard/processor'))>
                        {{ HTML::link('processorDashboard/processor', 'Processor Dashboard', array('class' => Request::is('processorDashboard/processor') ? 'active' : '')) }} 
                    </li @endif>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND') ||  Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction'))
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction/add'))>
                            {{ HTML::link('transaction/add', 'Add Transaction', array('class' => Request::is('transaction/add') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'imports/importtransaction'))>
                                       {{ HTML::link('imports/importtransaction', 'Import Transaction Manual', array('class' => Request::is('imports/importtransaction') ? 'active' : '')) }}
                         </li @endif>
                        
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'imports/importtransaction'))>
                         {{ HTML::link('imports/importshopeetransaction', 'Import Shopee Transaction', array('class' => Request::is('imports/importshopeetransaction') ? 'active' : '')) }}
                          </li @endif>
                          
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction/pendingtransaction'))>
                            {{ HTML::link('transaction/pendingtransaction', 'Bulk Approval', array('class' => Request::is('transaction/pendingtransaction') ? 'active' : '')) }}
                        </li @endif>
                        <!-- <li>
                            {{ HTML::link('transaction/location', 'Location', array('class' => Request::is('transaction/location') ? 'active' : '')) }}
                        </li> -->
                        <?php if (Session::get('branch_access') != 1) { ?>
                            <!--<li>-->
                            <!--    {{ HTML::link('transaction/lognew', 'Log', array('class' => Request::is('transaction/lognew') ? 'active' : '')) }}-->
                            <!--</li>-->
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction/history'))>
                                {{ HTML::link('transaction/history', 'Status History', array('class' => Request::is('transaction/history') ? 'active' : '')) }}
                            </li @endif>
                    
                        <?php } ?>
                         <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'joshua01'), true ) ) {  ?>
                        <li><a href="/home/v2" target="_blank">Sales Dashboard</a></li>
                         <?php } ?>
                        <li  @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction/statussummary'))>
                            {{ HTML::link('transaction/statussummary', 'Status Summary', array('class' => Request::is('transaction/statussummary') ? 'active' : '')) }}
                        </li @endif>
                        <li  @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 4, 'transaction/bulkadd'))>
                            {{ HTML::link('transaction/bulkadd', 'Bulk Import', array('class' => Request::is('transaction/bulkadd') ? 'active' : '')) }}
                        </li @endif>
                    @endif
                </ul>
                <!-- /.nav-second-level -->
            </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 29, 1, 'OR'))
            <li {{(Request::is('purchase-order*') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Purchase Order<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'purchase-order'))>
                        {{ HTML::link('purchase-order', 'Purchase Order Listing', array('class' => Request::is('purchase-order') ? 'active' : '')) }}
                    </li @endif>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 29, 1, 'OR') ) 
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'purchase-order/create'))>
                            {{ HTML::link('purchase-order/create', 'Add Purchase Order', array('class' => Request::is('purchase-order/create') ? 'active' : '')) }}
                        </li @endif>
                        <li {{(Request::is('manager*') ? 'class="active"' : '')}}>
                            <a href="#"> Payment Terms<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'payment-terms'))> 
                                    {{ HTML::link('payment-terms', 'Payment Terms List', array('class' => Request::is('payment-terms') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'payment-terms/create'))> 
                                    {{ HTML::link('payment-terms/create', 'Add Payment Terms', array('class' => Request::is('payment-terms/create') ? 'active' : '')) }}
                                </li @endif>
                            </ul>
                        </li>
                        <li {{(Request::is('manager*') ? 'class="active"' : '')}}>
                            <a href="#">Manager<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'manager'))>
                                    {{ HTML::link('manager', 'Manager List', array('class' => Request::is('manager') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'manager/create'))>
                                    {{ HTML::link('manager/create', 'Add Manager', array('class' => Request::is('manager/create') ? 'active' : '')) }}
                                </li @endif>
                            </ul>
                        </li>
                        <li {{(Request::is('warehouse-location*') ? 'class="active"' : '')}}>
                            <a href="#"> Warehouse Location<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'warehouse-location'))>
                                    {{ HTML::link('warehouse-location', 'Warehouse Location Listing', array('class' => Request::is('warehouse-location') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'warehouse-location/create'))>
                                    {{ HTML::link('warehouse-location/create', 'Add Warehouse Location ', array('class' => Request::is('warehouse-location/create') ? 'active' : '')) }}
                                </li @endif>
                            </ul>
                        </li>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'purchase-order/pbx'))>
                            {{ HTML::link('purchase-order/pbx', 'PracBix', array('class' => Request::is('purchase-order/pbx') ? 'active' : '')) }}
                        </li @endif>
                        <li {{(Request::is('einvoice*') ? 'class="active"' : '')}}>
                            <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> eInvoice<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'einvoice'))>
                                    {{ HTML::link('einvoice', 'eInvoice Listing', array('class' => Request::is('einvoice') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'einvoice/pbxn'))>
                                    {{ HTML::link('einvoice/pbx', 'PracBix', array('class' => Request::is('einvoice/pbx') ? 'active' : '')) }}
                                </li @endif>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 29, 'purchase-order/dashboard'))>
                            {{ HTML::link('purchase-order/dashboard', 'PO Dashboard', array('class' => Request::is('purchase-order/dashboard') ? 'active' : '')) }}
                        </li @endif>
                        
                    @endif
                </ul>
                <!-- /.nav-second-level -->
            </li>
             @endif
             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 30, 1, 'OR'))
                <li {{(Request::is('grn*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Goods Received Note<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'grn'))>
                            {{ HTML::link('grn', 'GRN Listing', array('class' => Request::is('grn') ? 'active' : '')) }}
                        </li @endif>
                        @if (Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 30, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'grn/create'))>
                                {{ HTML::link('grn/create', 'Add GRN', array('class' => Request::is('grn/create') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'grn/pbx'))>
                                {{ HTML::link('grn/pbx', 'PracBix', array('class' => Request::is('grn/pbx') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'grn/dashboard'))>
                            {{ HTML::link('grn/dashboard', 'GRN Dashboard', array('class' => Request::is('grn/dashboard') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'quenny'), true ) ) {  ?>
             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 30, 1, 'OR'))
                <li {{(Request::is('admingrn*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Admin Goods Received Note<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'admingrn'))>
                            {{ HTML::link('admingrn', 'Admin GRN Listing', array('class' => Request::is('admingrn') ? 'active' : '')) }}
                        </li @endif>
                        @if (Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 30, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'admingrn/create'))>
                                {{ HTML::link('admingrn/create', 'Add Admin GRN', array('class' => Request::is('admingrn/create') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 30, 'admingrn/dashboard'))>
                            {{ HTML::link('admingrn/dashboard', 'Admin GRN Dashboard', array('class' => Request::is('admingrn/dashboard') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            <?php } ?>
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 31, 1, 'OR'))
                <li {{(Request::is('gdf*') || Request::is('stock-transfer*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Goods Defect / Stock Transfer<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 31, 1, 'OR'))
                            <li {{(Request::is('gdf*') ? 'class="active"' : '')}}>
                                <a href="#"> Goods Defect Form<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'gdf'))>
                                        {{ HTML::link('gdf', 'Goods Defect Form List', array('class' => Request::is('gdf') ? 'active' : '')) }}
                                    </li @endif>
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'gdf/create'))>
                                        {{ HTML::link('gdf/create', 'Add Goods Defect Form', array('class' => Request::is('gdf/create') ? 'active' : '')) }}
                                    </li @endif>
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'gdf/report'))>
                                        {{ HTML::link('gdf/report', 'Goods Defect Form Report', array('class' => Request::is('gdf/report') ? 'active' : '')) }}
                                    </li @endif>
                                </ul>
                            </li>
                            <li {{(Request::is('stock-transfer*') ? 'class="active"' : '')}}>
                                <a href="#">Stock Transfer<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'stock-transfer'))>
                                        {{ HTML::link('stock-transfer', 'Stock Transfer List', array('class' => Request::is('stock-transfer') ? 'active' : '')) }}
                                    </li @endif>
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'stock-transfer/create'))>
                                        {{ HTML::link('stock-transfer/create', 'Add Stock Transfer', array('class' => Request::is('stock-transfer/create') ? 'active' : '')) }}
                                    </li @endif>
                                </ul>
                            </li>
                            <li {{(Request::is('stock-requisition*') ? 'class="active"' : '')}}>
                                <a href="#">Stock Requisition<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'stock-requisition'))>
                                        {{ HTML::link('stock-requisition', 'Stock Requisition List', array('class' => Request::is('stock-requisition') ? 'active' : '')) }}
                                    </li @endif>
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'stock-transfer/create'))>
                                        {{ HTML::link('stock-requisition/create', 'Add Stock Requisition', array('class' => Request::is('stock-requisition/create') ? 'active' : '')) }}
                                    </li @endif>
                                     <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 31, 'stock-transfer/create'))>
                                        {{ HTML::link('stock-requisition/platforms', 'Stock Requisition Platforms', array('class' => Request::is('stock-requisition/platforms') ? 'active' : '')) }}
                                    </li @endif>
                                </ul>
                            </li>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 18, 1, 'OR'))
                <li {{(Request::is('refund*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Refund<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 18, 'refund'))>
                            {{ HTML::link('refund', 'Refund Listing', array('class' => Request::is('refund') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 18, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 18, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 18, 'refund/create'))>
                                {{ HTML::link('refund/create', 'Add Refund', array('class' => Request::is('refund/create') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 18, 'refund/permission'))>
                                {{ HTML::link('refund/permission', 'Permission for Refund', array('class' => Request::is('refund/permission') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 15, 1, 'OR'))
                <li {{(Request::is('gstreport*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-list-alt fa-fw"></i> GST Report<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 15, 'gstreport/search'))>
                            {{ HTML::link('gstreport/search', 'Search Report', array('class' => Request::is('gstreport/search') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 15, 'gstreport/newreport'))>
                            {{ HTML::link('gstreport/newreport', 'Generate Report', array('class' => Request::is('gstreport/newreport') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 1, 'OR'))
                <li {{(Request::is('coupon*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-ticket fa-fw"></i> Coupon<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 12, 'coupon'))>
                            {{ HTML::link('coupon', 'Coupon Listing', array('class' => Request::is('coupon') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 12, 'coupon/couponstatics'))>
{{ HTML::link('coupon/couponstatics', 'Checkout Coupon Listing', array('class' => Request::is('coupon/couponstatics') ? 'active' : '')) }}
</li @endif>
<li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 12, 'coupon/addcoupon'))>
{{ HTML::link('coupon/addcoupon', 'Add Checkout Coupon', array('class' => Request::is('coupon/addcoupon') ? 'active' : '')) }}
</li @endif>
                        <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','nadzri'), true ) ) {  ?>
                            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 12, 1, 'OR'))
                                <li>
                                    {{ HTML::link('coupon/add', 'Add Coupon', array('class' => Request::is('coupon/add') ? 'active' : '')) }}
                                </li>
                            @endif
                            <?php if (in_array(Session::get('username'), array('maruthu'), true ) ) {  ?>
                                <li>
                                    {{ HTML::link('coupon/bulk', 'Bulk Coupon Listing', array('class' => Request::is('coupon/bulk') ? 'active' : '')) }}
                                </li>
                                <li>
                                    {{ HTML::link('coupon/bulkadd', 'Add Bulk Coupon', array('class' => Request::is('coupon/bulkadd') ? 'active' : '')) }}
                                </li>
                            <?php } ?>
                        <?php } ?>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND'))
                        <li>
                            {{ HTML::link('coupon/camping', 'Coupon Camping', array('class' => Request::is('coupon/camping') ? 'active' : '')) }}
                        </li>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
                <li {{(Request::is('coupon/*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-ticket fa-fw"></i>Free Coupon Item<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 33, 'coupon/freelisting'))>
                            {{ HTML::link('coupon/freelisting', 'Free Coupon Item Listing', array('class' => Request::is('coupon/freelisting') ? 'active' : '')) }}
                        </li @endif>
                        <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','joshua01'), true ) ) {  ?>
                            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
                                <li>
                                    {{ HTML::link('coupon/freecoupon', 'Add Free Coupon Item', array('class' => Request::is('coupon/freecoupon') ? 'active' : '')) }}
                                </li>
                            @endif
                        <?php } ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 1, 'OR'))
                <li {{(Request::is('banner*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-flag-o fa-fw"></i> Banner<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'banner/index'))>
                            {{ HTML::link('banner/index', 'Banner Listing', array('class' => Request::is('banner/abcseds') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 5, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'banner/create'))>
                                {{ HTML::link('banner/create', 'Add Banner', array('class' => Request::is('banner/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'banner/bannertemplate'))>
                            {{ HTML::link('banner/bannertemplate', 'Banner Template', array('class' => Request::is('banner/bannertemplate') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'bannertemplate/layout'))>
                            {{ HTML::link('bannertemplate/layout', 'Banner Layout NEW', array('class' => Request::is('bannertemplate/layout') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'bannertemplate/template'))>
                            {{ HTML::link('bannertemplate/template', 'Banner Template NEW', array('class' => Request::is('bannertemplate/template') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'banner/popup'))>
                            {{ HTML::link('banner/popup', 'Popup Listing', array('class' => Request::is('banner/popup') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'coupon/popupcreate'))>
                            {{ HTML::link('banner/popupcreate', 'Create New Popup', array('class' => Request::is('banner/popupcreate') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 5, 'ecombanner'))>
                            {{ HTML::link('ecombanner', 'eCommunity Banner Listing', array('class' => Request::is('ecombanner') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 6, 1, 'OR'))
                <li {{(Request::is('latestnews*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-file-text fa-fw"></i> Latest News<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 6, 'latestnews/index'))>
                            {{ HTML::link('latestnews/index', 'Latest News Listing', array('class' => Request::is('latestnews/index') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 6, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 6, 1, 'OR'))
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 6, 'latestnews/create'))>
                            {{ HTML::link('latestnews/create', 'Add News', array('class' => Request::is('latestnews/create') ? 'active' : '')) }}
                        </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 1, 'OR'))
                <li {{(Request::is('hot_item*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-comments-o"></i> Hot Items<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 7, 'hot_item'))>
                            {{ HTML::link('hot_item', 'Hot Items Listing', array('class' => Request::is('hot_item') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 7, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 7, 'hot_item/create'))>
                                {{ HTML::link('hot_item/create', 'Add Hot Item', array('class' => Request::is('hot_item/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 1, 'OR'))
                <li {{(Request::is('brands*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-comments-o"></i> Brands<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 7, 'brands'))>
                            {{ HTML::link('brands', 'Brand Listing', array('class' => Request::is('brands') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 7, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 7, 'brands/create'))>
                                {{ HTML::link('brands/create', 'Add Brand', array('class' => Request::is('brands/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 1, 'OR'))
                <li {{(Request::is('customer*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-users fa-fw"></i> Customers<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 8, 'customer/index'))>
                            {{ HTML::link('customer/index', 'Customer Listing', array('class' => Request::is('customer/index') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 8, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 8, 'customer/create'))>
                                {{ HTML::link('customer/create', 'Add Customer', array('class' => Request::is('customer/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'grace', 'quenny', 'tammy', 'joshua01'), true ) ) {  ?>
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 28, 1, 'OR'))
                    <li {{(Request::is('visitor*') ? 'class="active"' : '')}}>
                        <a href="#"><i class="fa fa-users fa-fw"></i> Visitor<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                {{ HTML::link('visitor', 'Manage Visitor', array('class' => Request::is('visitor') ? 'active' : '')) }}
                            </li>
                            <li>
                                {{ HTML::link('visitor/temperaturelog', 'MCO Temperature Log', array('class' => Request::is('visitor/temperaturelog') ? 'active' : '')) }}
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                @endif
            <?php } ?>
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 22, 1, 'OR'))
                <li {{(Request::is('charity*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-users fa-fw"></i> Charity User<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 22, 'charity/user'))>
                            {{ HTML::link('charity/user', 'User Listing', array('class' => Request::is('charity/user') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'),22, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 22, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 22, 'charity/user/create'))>
                                {{ HTML::link('charity/user/create', 'Add User', array('class' => Request::is('charity/user/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif

            @if (Permission::CheckAccessLevel(Session::get('role_id'), 20, 1, 'OR'))
                <li {{ Request::is('points*') ? ' class="active"' : '' }}>
                    <a href="#"><i class="fa fa-gift fa-fw"></i> Reward Points<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 20, 'points'))>
                            {{ HTML::link('points', 'Reward Point Listing', array('class' => Request::is('points') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 20, 'points/customers'))>
                            {{ HTML::link('points/customers', 'Customer Reward Points', array('class' => Request::is('points/customers') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 20, 'points/conversions'))>
                            {{  HTML::link('points/conversions', 'Point Conversions', array('class' => Request::is('points/conversions') ? 'active' : ''))  }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 20, 'points/bcard'))>
                            {{ HTML::link('points/bcard', 'BCard Rewards', array('class' => Request::is('points/bcard') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 20, 'points/bcard/create'))>
                            {{ HTML::link('points/bcard/create', 'Add BCard Rewards', array('class' => Request::is('points/bcard/create') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                </li>
            @endif
            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','asif','sclim','gladys','quinn'), true ) ) {  ?>
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 17, 1, 'OR'))
                    <li {{(Request::is('report*') || Request::is('mailchimp-report*') ? 'class="active"' : '')}}>
                        <a href="#"><i class="fa fa-list-alt fa-fw"></i> Report<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/product'))>
                                {{ HTML::link('report/product', 'Product', array('class' => Request::is('report/product') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/transaction'))>
                                {{ HTML::link('report/transaction', 'Transaction', array('class' => Request::is('report/transaction') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/transactionsgmv'))>
                                {{ HTML::link('report/transactionsgmv', 'Transaction GMV', array('class' => Request::is('report/transactionsgmv') ? 'active' : '')) }}
                            </li @endif>
                             <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','sclim','choong','gladys','marcochin','jokonn','yeehao'), true ) ) {  ?>
                            <li>
                                {{ HTML::link('report/edagang', 'EDAGANG Report', array('class' => Request::is('report/edagang') ? 'active' : '')) }}
                            </li>
                            <?php  } ?>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','sclim'), true ) ) {  ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'transaction/createbulkinvoice'))>
                                {{ HTML::link('transaction/createbulkinvoice', 'Bulk Invoice Generate', array('class' => Request::is('transaction/createbulkinvoice') ? 'active' : '')) }}
                            </li @endif>
                            <?php  } ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/topselling'))>
                                {{ HTML::link('report/topselling', 'Top Selling by Seller', array('class' => Request::is('report/topselling') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/top'))>
                                {{ HTML::link('report/top', 'Top Item Sold', array('class' => Request::is('report/top') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/qrcode'))>
                                {{ HTML::link('report/qrcode', 'QRCode Listing', array('class' => Request::is('report/qrcode') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/points'))>
                                {{ HTML::link('report/points', 'Reward Points', array('class' => Request::is('report/points') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'inventory/inventoryexport'))>
                                {{ HTML::link('inventory/inventoryexport', 'Inventory Report', array('class' => Request::is('inventory/inventoryexport') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/elevenstreetcompare'))>
                                {{ HTML::link('report/elevenstreetcompare', '11street Compare', array('class' => Request::is('report/elevenstreetcompare') ? 'active' : '')) }}
                            </li @endif>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes','maruthu', 'wira', 'joshua01'), true ) ) {  ?>
                            <li>
                                {{ HTML::link('/reporttemplate', 'Report Statistic', array('class' => Request::is('/reporttemplate') ? 'active' : '')) }}
                            </li>
                            <?php  } ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/consignmentreport'))>
                                {{ HTML::link('report/consignmentreport', 'Consignment Report') }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'home/top-transactions-products'))>
                                {{ HTML::link('/home/top-transactions-products', 'Top Pending Excel Export') }}
                            </li @endif>
                            <!--<li>-->
                            <!--    {{ HTML::link('/reporttemplate', 'Report Statistic', array('class' => Request::is('/reporttemplate') ? 'active' : '')) }}-->
                            <!--</li>-->
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/toppending'))>
                                {{ HTML::link('report/toppending', 'Top Pending Products(New)', array('class' => Request::is('report/toppending') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'mailchimp-report'))>
                                {{ HTML::link('/mailchimp-report', 'Mailchimp Report') }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 17, 'report/dailytransaction'))>
                                {{ HTML::link('report/dailytransaction', 'Daily Transaction') }}
                            </li @endif>
                        </ul>
                    </li>
                @endif
            <? } ?>

            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 19, 1, 'OR'))
                <li {{(Request::is('account*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-list-alt fa-fw"></i> Accounting System<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 19, 'account'))>
                            {{ HTML::link('account', 'Daily File', array('class' => Request::is('account') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 19, 'account/supplierinvoice'))>
                            {{ HTML::link('account/supplierinvoice', 'Supplier Invoicer', array('class' => Request::is('account/supplierinvoice') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                </li>
            @endif

            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 1, 'OR'))
                <li {{(Request::is('seller*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-user fa-fw"></i> Sellers<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 9, 'seller/index'))>
                            {{ HTML::link('seller/index', 'Seller Listing', array('class' => Request::is('seller/index') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 9, 'seller/gsseller'))>
                            {{ HTML::link('seller/gsseller', 'GS Vendor Listing', array('class' => Request::is('seller/gsseller') ? 'active' : '')) }}
                        </li @endif>
                        <?php if( (Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND')) || (in_array(Session::get('username'), array('nuratiqah', 'toby', 'ganesware'), true ) )) {?>
                            <li>
                                {{ HTML::link('seller/create', 'Add Seller', array('class' => Request::is('seller/create') ? 'active' : '')) }}
                                {{ HTML::link('seller/gssellercreate', 'Add GS Vendor', array('class' => Request::is('seller/gssellercreate') ? 'active' : '')) }}
                            </li>
                        <?php } ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif

            @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 1, 'OR'))
                <li {{ Request::is('agents') ? 'class="active"' : '' }}>
                    <a href="#"><i class="fa fa-briefcase fa-fw"></i> Agents <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 21, 'agents'))>
                            {{ HTML::link('agents', 'Agent Listing', ['class' => Request::is('agents') ? 'active' : '']) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 21, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 21, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 21, 'agents/create'))>
                                    {{ HTML::link('agents/create', 'Add Agent', ['class' => Request::is('agents/create') ? 'active' : '']) }}
                            </li @endif>
                        @endif
                    </ul>
                </li>
            @endif

            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 13, 1, 'OR'))
                <li {{(Request::is('special_price*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-dollar fa-fw"></i> Special Pricing<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/group/index'))>
                            {{ HTML::link('special_price/group', 'Special Price Group', array('class' => Request::is('special_price/group/index') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 13, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 13, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/group/create'))>
                                {{ HTML::link('special_price/group/create', 'Add Special Price Group', array('class' => Request::is('special_price/group/create') ? 'active' : '')) }}
                            </li @endif>
                        @endif
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/customer'))>
                            {{ HTML::link('special_price/customer', 'Special Price Customer', array('class' => Request::is('special_price/customer') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/export'))>
                            {{ HTML::link('special_price/export', 'Export Product Pricing', array('class' => Request::is('special_price/export') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/import'))>
                            {{ HTML::link('special_price/import', 'Import Product Pricing', array('class' => Request::is('special_price/import') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 13, 'special_price/setting'))>
                            {{ HTML::link('special_price/setting', 'Special Pricing Setting', array('class' => Request::is('special_price/setting') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif

            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 1, 'OR'))
                <?php if (in_array(Session::get('username'), array('dashboard'), true ) != true ) {  ?>
                    <li {{(Request::is('jlogistic*') || Request::is('courier') || Request::is('transaction/sort')   ? 'class="active"' : '')}} {{(Request::is('driver*') ? 'class="active"' : '')}} {{(Request::is('batch*') ? 'class="active"' : '')}} {{(Request::is('route*') ? 'class="active"' : '')}}>
                        <a href="#"><i class="fa fa-truck fa-fw"></i> Logistic<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/dashboard'))>
                                {{ HTML::link('jlogistic/dashboard', 'Dashboard', array('class' => Request::is('jlogistic/dashboard') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/dashboardregion'))>
                                {{ HTML::link('jlogistic/dashboardregion', 'Dashboard Statistics', array('class' => Request::is('jlogistic/dashboardregion') ? 'active' : '')) }}
                            </li @endif>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'joshua01'), true ) ) {  ?>
                                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 5, 'OR'))
                                    <li>
                                        {{ HTML::link('driver/create', 'Add Driver', array('class' => Request::is('driver/create') ? 'active' : '')) }}
                                    </li>
                                @endif
                                <li>
                                    {{ HTML::link('driver', 'Driver Listing', array('class' => Request::is('driver') ? 'active' : '')) }}
                                </li>
                            <? } ?>
                            <li>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'driver-locations'))>
                                {{ HTML::link('driver-locations', 'Driver - Maps GPS Simulation', array('class' => Request::is('driver-locations') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'route-planner'))>
                                {{ HTML::link('route-planner', 'Route Planner', array('class' => Request::is('route-planner') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic'))>
                                {{ HTML::link('jlogistic', 'Logistic Transaction Listing', array('class' => Request::is('jlogistic') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch'))>
                                {{ HTML::link('batch', 'Logistic Batch Listing', array('class' => Request::is('batch') ? 'active' : '')) }}
                            </li @endif>
                            <!--  <li>
                                {{ HTML::link('jlogistic/location', 'Location', array('class' => Request::is('jlogistic/location') ? 'active' : '')) }}
                            </li> -->
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/locationlist'))>
                                {{ HTML::link('jlogistic/locationlist', 'Location', array('class' => Request::is('jlogistic/locationlist') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/trackingdrivers'))>
                                {{ HTML::link('jlogistic/trackingdrivers', 'Tracking', array('class' => Request::is('jlogistic/trackingdrivers') ? 'active' : '')) }}
                            </li @endif>
                            <?php if (Session::get('branch_access') != 1) { ?>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'logistic/search'))>
                                    {{ HTML::link('jlogistic/search', 'Logistic Search', array('class' => Request::is('logistic/search') ? 'active' : '')) }}
                                </li @endif>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/export'))>
                                    {{ HTML::link('jlogistic/export', 'Export Transaction Details', array('class' => Request::is('logistic/export') ? 'active' : '')) }}
                                </li @endif>
                                @if ( Session::get('region_access') == 0)
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'courier'))>
                                        {{ HTML::link('/courier', 'Courier', array('class' => Request::is('courier') ? 'active' : '')) }}
                                    </li @endif>
                                @endif
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'logistic/dohistory'))>
                                    {{ HTML::link('jlogistic/dohistory', 'DO History', array('class' => Request::is('logistic/dohistory') ? 'active' : '')) }}
                                </li @endif>
                            <?php } ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/unassign'))>
                                {{ HTML::link('batch/unassign', 'Batch Reset', array('class' => Request::is('batch/unassign') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/assigned'))>
                                {{ HTML::link('jlogistic/assigned', 'Assigned Report', array('class' => Request::is('jlogistic/assigned') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/assignedprescan'))>
                                {{ HTML::link('jlogistic/assignedprescan', 'Assigned Report - PreScan', array('class' => Request::is('jlogistic/assignedprescan') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/checklist'))>
                                {{ HTML::link('jlogistic/checklist', 'Download Checklist', array('class' => Request::is('jlogistic/checklist') ? 'active' : '')) }}
                            </li @endif>
                            
                            <?php if (Session::get('branch_access') != 1) { ?>
                                <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/sort'))>
                                    {{ HTML::link('transaction/sort', 'Sorted', array('class' => Request::is('transaction/sort') ? 'active' : '')) }}
                                </li @endif>
                            <?php } ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'jlogistic/drivertimesheetlist'))>
                                {{ HTML::link('jlogistic/drivertimesheetlist', 'Driver Time Sheet', array('class' => Request::is('jlogistic/drivertimesheetlist') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                                {{ HTML::link('batch/return', 'Return Pending Batch', array('class' => Request::is('batch/return') ? 'active' : '')) }}
                            </li @endif>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                <?php } ?>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 24, 1, 'OR'))
                <li {{(Request::is('warehouse*', 'warehouse*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-university"></i> Warehouse<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                            {{ HTML::link('/warehouse/manage', 'Stock In', array('class' => Request::is('warehouse/manage') ? 'active' : '')) }}
                        </li @endif>
                        <?php if (Session::get('username') != 'tracyyap')  {  ?>
                            <li {{(Request::is('Stock Transfer*', 'Stock Transfer*') ? 'class="active"' : '')}}>
                                <a href="#">Stock Transfer<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                    {{ HTML::link('stock', 'Stock Transfer Listing', array('class' => Request::is('stock') ? 'active' : '')) }} 
                                    </li>
                                    <li>
                                        {{ HTML::link('stock/create', 'Add Stock Transfer', array('class' => Request::is('stock/create') ? 'active' : '')) }}
                                    </li>
                                </ul>
                            
                            
                            </li>
                        
                        <li {{(Request::is('Pallet Management*', 'Pallet Management*') ? 'class="active"' : '')}}>
                                <a href="#"> Pallet Management<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'pallet'))>
                                        {{ HTML::link('pallet', 'Pallet Management Listing', array('class' => Request::is('pallet') ? 'active' : '')) }} 
                                    </li @endif>
                                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                                        {{ HTML::link('supplier', 'Supplier Listing ', array('class' => Request::is('supplier/create') ? 'active' : '')) }}
                                    </li @endif>
                                </ul>
                            
                            
                            </li>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'warehouse/writeoff'))>
                                {{ HTML::link('/warehouse/writeoff', 'Write Off', array('class' => Request::is('warehouse/writeoff') ? 'active' : '')) }}
                            </li @endif>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'asif'), true ) ) {  ?>
                                <li>
                                    {{ HTML::link('/warehouse', 'Master Listing', array('class' => Request::is('warehouse') ? 'active' : '')) }}
                                </li>
                        
                            <?php } ?>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','asif','quenny','ramesh','joshua01','eugeneyong'), true ) ) {  ?>
                                <li>
                                    {{ HTML::link('/warehouse/invadjustments', 'Inventory Adjustments', array('class' => Request::is('warehouse/invadjustments') ? 'active' : '')) }}
                                </li>
                            <?php } ?>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'warehouse/retulinkproductrn'))>
                                {{ HTML::link('/warehouse/linkproduct', 'Link Product to Inventory', array('class' => Request::is('warehouse/linkproduct') ? 'active' : '')) }}
                            </li @endif>
                        
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                                {{ HTML::link('/warehouse/generalreport', 'General Reports', array('class' => Request::is('warehouse/generalreport') ? 'active' : '')) }}
                            </li @endif>
                            
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                                {{ HTML::link(route('sorted.fresh.inventory'), 'Fresh Inventory', array('class' => Request::is('warehouse/sorted/purchase-todos') ? 'active' : '')) }}
                            </li @endif>
                            
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'batch/return'))>
                                {{ HTML::link(route('fresh.inventory.history'), 'Fresh Inventory History', array('class' => Request::is('warehouse/fresh-inventory-history') ? 'active' : '')) }}
                            </li @endif>
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 14, 'warehouse/stockinhistory'))>
                                {{ HTML::link('/warehouse/stockinhistory', 'Stock In History', array('class' => Request::is('warehouse/stockinhistory') ? 'active' : '')) }}
                            </li @endif>
                        <?php } ?>
                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 1, 'OR'))
                <li {{(Request::is('country*', 'zone*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-plane fa-fw"></i> Shipping<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 1, 'country'))>
                            {{ HTML::link('country', 'Country Listing', array('class' => Request::is('country') ? 'active' : '')) }}
                        </li @endif>    
                        <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND'))
                        <li>
                            {{ HTML::link('country/create', 'Add Country', array('class' => Request::is('country/create') ? 'active' : '')) }}
                        </li>
                        @endif -->
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 1, 'zone'))>
                            {{ HTML::link('zone', 'Zone Listing', array('class' => Request::is('zone') ? 'active' : '')) }}
                        </li @endif>        
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 1, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 1, 'zone/create'))>
                                {{ HTML::link('zone/create', 'Add Zone', array('class' => Request::is('zone/create') ? 'active' : '')) }}
                            </li @endif>                        
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 1, 'OR'))
                <li {{(Request::is('sysadmin*', 'exchange*') ? 'class="active"' : '')}} {{(Request::is('fees*') ? 'class="active"' : '')}}>

                    <a href="#"><i class="fa fa-gears fa-fw"></i> System Administration<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/user'))>
                            {{ HTML::link('sysadmin/user', 'User Listing', array('class' => Request::is('sysadmin/user') ? 'active' : '')) }}
                        </li @endif>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 10, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/user/create'))>
                                {{ HTML::link('sysadmin/user/create', 'Add User', array('class' => Request::is('sysadmin/user/create') ? 'active' : '')) }}
                            </li @endif>                        
                        @endif
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/role'))>
                            {{ HTML::link('sysadmin/role', 'Role &amp; Permission', array('class' => Request::is('sysadmin/role') ? 'active' : '')) }}
                        </li @endif>         
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND') || Permission::CheckAccessLevel(Session::get('role_id'), 10, 1, 'OR'))
                            <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/role/create'))>
                                {{ HTML::link('sysadmin/role/create', 'Add Role &amp; Permission', array('class' => Request::is('sysadmin/role/create') ? 'active' : '')) }}
                            </li @endif>                        
                        @endif
                        <li>
                            {{ HTML::link('sysadmin/indvPermission', 'Individual Permission', array('class' => Request::is('sysadmin/indvPermission') ? 'active' : '')) }}
                        </li>
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
                        <li>
                            {{ HTML::link('sysadmin/indvPermission/create', 'Add Individual Permission', array('class' => Request::is('sysadmin/indvPermission/create') ? 'active' : '')) }}
                        </li>
                        @endif
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/app'))>
                            {{ HTML::link('sysadmin/app', 'Apps Version', array('class' => Request::is('sysadmin/app') ? 'active' : '')) }}
                        </li @endif>          
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/appnew'))>
                            {{ HTML::link('sysadmin/appnew', 'Manage Apps Version', array('class' => Request::is('sysadmin/appnew') ? 'active' : '')) }}
                        </li @endif>                                   
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/appnewlogistic'))>
                            {{ HTML::link('sysadmin/appnewlogistic', 'Logistic App Version ', array('class' => Request::is('sysadmin/appnewlogistic') ? 'active' : '')) }}
                        </li @endif>                           
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'fees'))>
                            {{ HTML::link('fees', 'Fees Setup', array('class' => Request::is('fees') ? 'active' : '')) }}
                        </li @endif>                       
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'region'))>
                            {{ HTML::link('/region', 'Region', array('class' => Request::is('region') ? 'active' : '')) }}
                        </li @endif>            
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'reward'))>
                            {{ HTML::link('/sysadmin/reward', 'Reward Setting', array('class' => Request::is('reward') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'exchange'))>
                            {{ HTML::link('/exchange/', 'Exchange Rate', array('class' => Request::is('exchange') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 10, 'sysadmin/address-keywords'))>
                            {{ HTML::link('/sysadmin/address-keywords', 'Keywords Setting', array('class' => (Request::is('sysadmin/address-keywords') ? 'active' : ''))) }}
                        </li @endif>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 11, 1, 'OR'))
                <li {{(Request::is('push*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-paper-plane-o"></i> Push Notification<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 11, 'push'))>
                            {{ HTML::link('push', 'Notification Listing', array('class' => Request::is('push') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 11, 'push/create'))>
                            {{ HTML::link('push/create', 'New Notification', array('class' => Request::is('push/create') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 11, 'push/queue'))>
                            {{ HTML::link('push/queue', 'Queue Listing', array('class' => Request::is('push/queue') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 11, 'push/device'))>
                            {{ HTML::link('push/device', 'Device Listing', array('class' => Request::is('push/device') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 11, 'push/history'))>
                            {{ HTML::link('push/history', 'History', array('class' => Request::is('push/history') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 23, 15, 'AND'))
                <li {{(Request::is('eleven*', 'lazada*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-folder-o"></i> 3rd Party Platform<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'eleven'))>
                            {{ HTML::link('/eleven', '11Street', array('class' => Request::is('eleven') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'lazada'))>
                            {{ HTML::link('/lazada', 'Lazada', array('class' => Request::is('lazada') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'qoo10'))>
                            {{ HTML::link('/qoo10', 'Qoo10', array('class' => Request::is('qoo10') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'shopee'))>
                            {{ HTML::link('/shopee', 'Shopee', array('class' => Request::is('shopee') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'pgmall'))>
                            {{ HTML::link('/pgmall', 'PGMall', array('class' => Request::is('pgmall') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'astrogo'))>
                            {{ HTML::link('/astrogo', 'Astro Go Shop', array('class' => Request::is('astrogo') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'transaction/taobao'))>
                            {{ HTML::link('/transaction/taobao', 'Taobao', array('class' => Request::is('/transaction/taobao') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'transaction/one688'))>
                            {{ HTML::link('/transaction/one688', '1688', array('class' => Request::is('/transaction/one688') ? 'active' : '')) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 23, 'transaction/tmall'))>
                            {{ HTML::link('/transaction/tmall', 'Tmall', array('class' => Request::is('/transaction/tmall') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 26, 4, 'OR'))
            <?php if (Session::get('branch_access') != 1) { ?>
                <?php if (in_array(Session::get('username'), array('dashboard'), true ) != true ) {  ?>
                    <li {{(Request::is('feedback/*') ? 'class="active"' : '')}}>
                        <a href="#"><i class="fa fa-comments-o"></i> Feedback <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                {{ HTML::link('/feedback', 'Feedback Listing', array('class' => Request::is('feedback') ? 'active' : '')) }}
                            </li>
                            <li>
                                {{ HTML::link('/board', 'Leaderboard Listing', array('class' => Request::is('board') ? 'active' : '')) }}
                            </li>
                        </ul>
                    </li>
                    <li {{(Request::is('blog') ? 'class="active"' : '')}}>
                        <a href="#"><i class="fa fa-comments-o"></i> News <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                {{ HTML::link('/blog', 'Posts', array('class' => Request::is('blog') ? 'active' : '')) }}
                            </li>
                            <li>
                                {{ HTML::link('/blog/create', 'New Post', array('class' => Request::is('blog/create') ? 'active' : '')) }}
                            </li>
                        </ul>
                    </li>
                    <!--<li {{(Request::is('jocommy/*') ? 'class="active"' : '')}}>-->
                    <!--    <a href="#"><i class="fa fa-picture-o"></i> JocomMy Banner <span class="fa arrow"></span></a>-->
                    <!--    <ul class="nav nav-second-level">-->
                    <!--        <li>-->
                    <!--            {{ HTML::link('/jocommy/layout', 'JocomMy Layout', array('class' => Request::is('/jocommy/layout') ? 'active' : '')) }}-->
                    <!--        </li>-->
                    <!--        <li>-->
                    <!--            {{ HTML::link('/jocommy/template', 'JocomMy Template', array('class' => Request::is('/jocommy/template') ? 'active' : '')) }}-->
                    <!--        </li>-->
                    <!--    </ul>-->
                    <!--</li>-->
                <?php } ?>
            <?php } ?>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 26, 4, 'OR'))
                <li {{(Request::is('jocommy*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-picture-o"></i> tmGrocerMy Banner <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 26, 'jocommy/index'))>
                            {{ HTML::link('/jocommy/index', 'tmGrocerMy Banner', array('class' => (Request::is('jocommy/index') || Request::is('jocommy/create') || Request::is('jocommy/update/*') ? 'active' : ''))) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 26, 'jocommy/event'))>
                            {{ HTML::link('/jocommy/event', 'tmGrocerMy Event', array('class' => (Request::is('jocommy/event*') ? 'active' : ''))) }}
                        </li @endif>
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 26, 'jocommy/template'))>
                            {{ HTML::link('/jocommy/template', 'tmGrocerMy Template', array('class' => Request::is('jocommy/template*') ? 'active' : '')) }}
                        </li @endif>
                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 32, 1, 'OR'))
                <li {{(Request::is('flashsale/*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-tags fa-fw"></i> Flash Sale <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 32, 'flashsale'))>
                            {{ HTML::link('/flashsale', 'Flash Sale', array('class' => Request::is('/flashsale') ? 'active' : '')) }}
                        </li @endif>
                        
                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
                <li {{(Request::is('jocomexccorner/*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-tags fa-fw"></i>Jocom Exclusive Corner <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 32, 'jocomexccorner'))>
                            {{ HTML::link('/jocomexccorner', 'Exclusive Corner', array('class' => Request::is('/jocomexccorner') ? 'active' : '')) }}
                        </li @endif>

                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
                <li {{(Request::is('jcmcombodeals/*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-tags fa-fw"></i>tmGrocer Combo Deals <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 33, 'jcmcombodeals'))>
                            {{ HTML::link('/jcmcombodeals', 'Combo Deals', array('class' => Request::is('/jcmcombodeals') ? 'active' : '')) }}
                        </li @endif>

                    </ul>
                </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
                <li {{(Request::is('jcmdynamicsale/*') ? 'class="active"' : '')}}>
                    <a href="#"><i class="fa fa-tags fa-fw"></i>tmGrocer Dynamic Panel <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 33, 'jcmdynamicsale'))>
                            {{ HTML::link('/jcmdynamicsale', 'Dynamic Panel', array('class' => Request::is('/jcmdynamicsale') ? 'active' : '')) }}
                        </li @endif>

                    </ul>
                </li>
            @endif
             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
            <li {{(Request::is('helpcenter') || Request::is('helpcenter') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-tags fa-fw"></i>Help Center Management<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 33, 'helpcenter'))>
                        {{ HTML::link('helpcenter', 'Help Center Listing', array('class' => Request::is('helpcenter') ? 'active' : '')) }}
                    </li @endif>
                    
                </ul>
                <!-- /.nav-second-level -->
            </li>
            @endif
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 33, 1, 'OR'))
            <li {{(Request::is('contestant') ? 'class="active"' : '')}}>
                <a href="#"><i class="fa fa-tags fa-fw"></i> Contestants <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li @if (Permission::CheckSubModAccess(Session::get('role_id'), Session::get('user_id'), 33, 'contestant'))>
                        {{ HTML::link('contestant', 'Contestant Listing', array('class' => Request::is('contestant') ? 'active' : '')) }}
                    </li @endif>

                </ul>
            </li>
            @endif
        </ul>
    </div>
<!-- /.sidebar-collapse -->
</div>