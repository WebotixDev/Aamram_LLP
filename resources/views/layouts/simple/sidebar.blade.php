@php
    $logo = DB::table('company')->value('logo');

@endphp
        <!-- Page Sidebar Start-->
        <div class="sidebar-wrapper" data-layout="stroke-svg">


<div class="logo-wrapper">
    <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('public/' . $logo) }}" style="height: 70px; width: 180px;" alt="Company Logo">
    </a>
    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
    <div class="toggle-sidebar">
        <i class="status_toggle middle sidebar-toggle" data-feather="grid"></i>
    </div>
</div>


            <br>
            <!-- <div class="logo-icon-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid"
                        src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div> -->
          <div class="logo-icon-wrapper mt-5"><a href="{{ route('admin.dashboard') }}">        <img src="{{ asset('public/' . $logo) }}" style="height: 70px; width: 180px;" alt="Company Logo">
          </a></div>
            <nav class="sidebar-main">
                <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                <div id="sidebar-menu">
                    <ul class="sidebar-links" id="simple-bar">
                        <li class="back-btn"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid"
                                    src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                            <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2"
                                    aria-hidden="true"></i></div>
                        </li>

                        <li class="sidebar-list pt-4"><a class="sidebar-link sidebar-title"
                                href="javascript:void(0)">

                                <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                                </svg><span class="lan-7-1">Master</span></a>
                            <ul class="sidebar-submenu">

                            @can('role.index')
                            <li><a href="{{ route('admin.role.index') }}">Role</a></li>
                            @endcan

                            @can('user.index')
                            <li><a href="{{ route('admin.user.index') }}">User</a></li>
                            @endcan

                            @can('district.index')
                            <li><a href="{{ route('admin.district.index') }}">District</a></li>
                           @endcan

                            @can('cities.index')
                            <li><a href="{{ route('admin.city.index') }}">City</a></li>
                            @endcan

                              @can('product.index')
                            <li><a href="{{ route('admin.product_size.index') }}">Product Size</a></li>
                            @endcan

                            @can('product.index')
                            <li><a href="{{ route('admin.product.index') }}">Product</a></li>
                            @endcan

                           @can('customer.index')
                            <li><a href="{{ route('admin.customer.index') }}">Customer</a></li>
                           @endcan

                      @can('supplier.index')
                           <li><a href="{{ route('admin.supplier.index') }}">Supplier</a></li>
                          @endcan

                              @can('transporter.index')
                           <li><a href="{{ route('admin.Transporter.index') }}">Transporter</a></li>
                          @endcan

      @can('location.index')
                           <li><a href="{{ route('admin.Location.index') }}">Location</a></li>
                          @endcan

                  @can('customer.index')
                           <li><a href="{{ route('admin.Season.index') }}">Season</a></li>
                          @endcan
                            </ul>
                        </li>

                        <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                                href="javascript:void(0)">

                                <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                                </svg><span class="lan-7-1">Expenses</span></a>
                            <ul class="sidebar-submenu">


                            @can('expense_head.index')
                            <li><a href="{{ route('admin.subject.index') }}">Expenses Head</a></li>
                         @endcan

                            @can('expense.index')
                            <li><a href="{{ route('admin.expense.index') }}"> Expenses</a></li>
                            @endcan

                    @can('expense.index')
                            <li><a href="{{ route('admin.investors.index') }}">Investor Form</a></li>
                            @endcan
                            </ul>
                        </li>



                  @can('farm_outward.index')
            <li class="sidebar-list {{ request()->routeIs('admin.farm_inward.*') || request()->routeIs('admin.Farm_Delivery_challan.*')  || request()->routeIs('admin.warehouse_inward.*') || request()->routeIs('admin.ripening_chamber.*')? 'active' : '' }}">

                <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                    </svg>
                    <span>Farm Management</span>
                </a>

                <ul class="sidebar-submenu">
                    <li class="{{ request()->routeIs('admin.farm_inward.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.farm_inward.index') }}">
                            Farm Inward
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.Farm_Delivery_challan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.Farm_Delivery_challan.index') }}">
                            Farm Delivery Challan
                        </a>
                    </li>


                    <li class="{{ request()->routeIs('admin.warehouse_inward.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.warehouse_inward.index') }}">
                             Warehouse Inward
                        </a>
                    </li>
                     <li class="{{ request()->routeIs('admin.ripening_chamber.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.ripening_chamber.index') }}">
                            Ripening Chamber
                        </a>
                    </li>
                </ul>

            </li>
            @endcan

                    @can('inward.index')
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                     href="{{ route('admin.inward.index') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                        </svg><span class="lan-7-1">Stock</span></a>
                       </li>
                @endcan



                @can('sale_order.index')
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                     href="{{ route('admin.sale_order.index') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                        </svg><span class="lan-7-1">Sale Order</span></a>
                       </li>
                @endcan

                @can('outward.index')
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                     href="{{ route('admin.outward.index') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                        </svg><span class="lan-7-1">Outword</span></a>
                       </li>
                @endcan

         @can('sale_PenDis_Report.index')
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                     href="{{ route('admin.sale_PenDis_Report.index') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                        </svg><span class="lan-7-1">Pending Dispatch</span></a>
                       </li>
                @endcan

      @can('delivery_challan.index')
                <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                 href="{{ route('admin.Delivery_Challan.index') }}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                    </svg><span class="lan-7-1">Delivery Challan </span></a>
                   </li>
            @endcan
                @can('sale_payment.index')
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                     href="{{ route('admin.sale_payment.index') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                        </svg><span class="lan-7-1">Sale Payment</span></a>
                       </li>
                @endcan



                @can('customer_wise_saleBill.index')
                <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                 href="{{ route('admin.Customer_bill.index') }}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                    </svg><span class="lan-7-1">Customer Wise Bill</span></a>
                   </li>
            @endcan

                <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                                href="javascript:void(0)">

                                <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
                                </svg><span class="lan-7-1">Reports</span></a>
                            <ul class="sidebar-submenu">
                                    @can('reports.index')
                                <li><a href="{{ route('admin.Farm_Report.index') }}">Harvest Report</a></li>
                            @endcan
                            @can('reports.index')
                                <li><a href="{{ route('admin.reports.index') }}">Stock Report</a></li>
                            @endcan

                            @can('reports.index')
                            <li><a href="{{ route('admin.OutwardReport.index') }}">Outward Report</a></li>
                            @endcan

                            @can('reports.index')
                            <li><a href="{{ route('admin.Sale_OrderReport.index') }}">Sale Order Report</a></li>
                            @endcan

                            @can('reports.index')
                            <li><a href="{{ route('admin.Batch_report.index') }}">Batch Wise Stock Report</a></li>
                            @endcan

                            <!--@can('reports.index')-->
                            <!--<li><a href="{{ route('admin.sale_PenDis_Report.index') }}">sale Pending Report</a></li>-->
                            <!--@endcan-->

                            @can('reports.index')
                            <li><a href="{{ route('admin.Outstanding_Report.index') }}">Outstanding Report</a></li>
                            @endcan


                            @can('reports.index')
                            <li><a href="{{ route('admin.sale_product.index') }}">Product Sale Report</a></li>
                            @endcan

                            <!--@can('reports.index')-->
                            <!--<li><a href="{{ route('admin.ledger_report.index') }}">Ledger Report</a></li>-->
                            <!--@endcan-->

                            </ul>
                        </li>


                          <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                                href="javascript:void(0)">

                                <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                                </svg><span class="lan-7-1">Setting</span></a>
                            <ul class="sidebar-submenu">
                            @can('bank.index')
                                <li><a href="{{ route('admin.profile.index') }}">Bank Details</a></li>
                            @endcan

                            @can('company.index')
                            <li><a href="{{ route('admin.company.index') }}">Profile</a></li>
                            @endcan



                            <!--@can('product_bulk.index')-->
                            <!--<li><a href="{{ route('admin.product_bulk.index') }}">Product </a></li>-->
                            <!--@endcan-->


                            </ul>
                        </li>

             @can('company.index')
                        <li class="sidebar-list"><a class="sidebar-link sidebar-title"
                         href="{{ route('admin.orders.import') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg><span class="lan-7-1">Import</span></a>
                           </li>
                    @endcan

                    </ul>
                    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                </div>
            </nav>
        </div>
        <!-- Page Sidebar Ends-->

        <style>
    /* Transition for smooth hide/show */
    .logo-wrapper img,
    .logo-icon-wrapper img {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* When collapsed: hide images */
    .sidebar-wrapper.collapsed .logo-wrapper img,
    .sidebar-wrapper.collapsed .logo-icon-wrapper img {
        opacity: 0;
        visibility: hidden;
    }

    /* Optional: make sidebar narrower when collapsed */
    .sidebar-wrapper.collapsed {
        width: 80px !important;
        overflow: hidden;
    }

    .sidebar-wrapper {
        transition: width 0.3s ease;
    }
</style>

<!-- ===== Add this JS ===== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.toggle-sidebar').on('click', function () {
            $('.sidebar-wrapper').toggleClass('collapsed');
        });
    });
</script>
