<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a  href="{{ route('company.dashboard') }}" {{ setActive(['company/dashboard']) }}>
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/users') }}" {{ setActive(['company/users']) }}>
                        <i class="fa fa-users"></i>
                        <span>Employees</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/customers') }}" {{ setActive(['company/customers']) }}>
                        <i class="fa fa-users"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/stores') }}" {{ setActive(['company/stores']) }}>
                        <i class="fa fa-home"></i>
                        <span>Stores</span>
                    </a>
                </li>
                @if(Auth::user()->company_type == 1)
                <li>
                    <a  href="{{ url('company/meal_types') }}" {{ setActive(['company/meal_types']) }}>
                        <i class="fa fa-home"></i>
                        <span>Meal Types</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/floors') }}" {{ setActive(['company/floors']) }}>
                        <i class="fa fa-home"></i>
                        <span>Floor Plan</span>
                    </a>
                </li>
                @endif
                <li>
                    <a  href="{{ url('company/ads') }}" {{ setActive(['company/ads']) }}>
                        <i class="fa fa-home"></i>
                        <span>Advertisements</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/categories') }}" {{ setActive(['company/categories']) }}>
                        <i class="fa fa-sitemap"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/discounts') }}" {{ setActive(['company/discounts']) }}>
                        <i class="fa fa-sitemap"></i>
                        <span> @if(Auth::user()->company_type == 1) Happy Hours @else Promotion @endif</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/products') }}" {{ setActive(['company/products','company/product-stocks']) }}>
                        <i class="fa fa-shopping-cart"></i>
                        @if(Auth::user()->company_type == 1) <span>Menu</span> @else <span>Products</span> @endif
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/sales') }}" {{ setActive(['company/sales','company/invoice']) }}>
                        <i class="fa fa-file-text"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/manage-stocks') }}" {{ setActive(['company/manage-stocks']) }}>
                        <i class="fa fa-filter"></i>
                        <span>Manage Inventory</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('company/suppliers') }}" {{ setActive(['company/suppliers']) }}>
                        <i class="fa fa-users"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a href="javascript:void(0);" {{ setActive(['company/reports']) }}>
                        <i class="fa fa-area-chart"></i>
                        <span>Reports</span>
                    </a>
                    <ul class="sub">
                        <!--<li {{ setActive(['company/reports/retail-report']) }}><a href="{{ url('company/reports/retail-report') }}">Retail Dashboard</a></li>-->                        
                        <li {{ setActive(['company/reports/stores-stock']) }}><a href="{{ url('company/reports/stores-stock') }}">Stocks Report</a></li>                        
                        <li {{ setActive(['company/reports/sales-report']) }}><a href="{{ url('company/reports/sales-report') }}">Sales Report</a></li>                        
                        <li {{ setActive(['company/reports/products-report']) }}><a href="{{ url('company/reports/products-report') }}">Products Report</a></li>                        
                        <li {{ setActive(['company/reports/customers-report']) }}><a href="{{ url('company/reports/customers-report') }}">Customers Report</a></li>                        
                        <li {{ setActive(['company/reports/staff-report']) }}><a href="{{ url('company/reports/staff-report') }}">Staff Report</a></li>
                        <li {{ setActive(['company/reports/shift-report']) }}><a href="{{ url('company/reports/shift-report') }}">Shifts Report</a></li>                        
                    </ul>
                </li> 
<!--                <li class="sub-menu">
                    <a href="javascript:void(0);" {{ setActive(['company/roles','company/permissions']) }}>
                        <i class="fa fa-key"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    <ul class="sub">
                        <li {{ setActive(['company/roles']) }}><a href="{{ url('company/roles') }}">Roles</a></li>
                        <li {{ setActive(['company/permissions']) }}><a href="{{ url('company/permissions') }}">Permissions</a></li>
                    </ul>
                </li>                -->
                <li class="sub-menu">
                    <a href="javascript:void(0);" {{ setActive(['company/settings','company/customer-groups','company/currencies','company/tax-rates','company/shipping-options','company/variants','company/email-template','company/roles','company/permissions']) }}>
                        <i class="fa fa-cogs"></i>
                        <span>Settings</span>
                    </a>
                    <ul class="sub">
                        <li {{ setActive(['company/settings']) }}><a href="{{ url('company/settings') }}">System Settings</a></li>
                        <li {{ setActive(['company/duty/settings']) }}><a href="{{ url('company/duty/settings') }}">System Duty Settings</a></li>
                        <li {{ setActive(['company/customer-groups']) }}><a href="{{ url('company/customer-groups') }}">Customer Groups</a></li>
                        <li {{ setActive(['company/currencies']) }}><a href="{{ url('company/currencies') }}">Currencies</a></li>
                        <li {{ setActive(['company/tax-rates']) }}><a href="{{ url('company/tax-rates') }}">Tax Rates</a></li>
                        <li {{ setActive(['company/shipping-options']) }}><a href="{{ url('company/shipping-options') }}">Shipping Options</a></li>
                        <li {{ setActive(['company/variants']) }}><a href="{{ url('company/variants') }}">Attributes</a></li>
                        <li {{ setActive(['company/email-template']) }}><a href="{{ url('company/email-template') }}">Email Templates</a></li>
                        <li {{ setActive(['company/roles']) }}><a href="{{ url('company/roles') }}">Roles</a></li>
                        <li {{ setActive(['company/permissions']) }}><a href="{{ url('company/permissions') }}">Permissions</a></li>                        
<!--                        <li {{ setActive(['company/modifiers']) }}><a href="{{ url('company/modifiers') }}">Modifiers</a></li>-->
                    </ul>
                </li>                
            </ul>
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
