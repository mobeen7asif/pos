<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a  href="{{ route('admin.dashboard') }}" {{ setActive(['admin/dashboard']) }}>
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a  href="{{ url('admin/companies') }}" {{ setActive(['admin/companies']) }}>
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Companies</span>
                    </a>
                </li>
<!--                <li>
                    <a  href="{{ url('admin/stores') }}" {{ setActive(['admin/stores']) }}>
                        <i class="fa fa-home"></i>
                        <span>Stores</span>
                    </a>
                </li>-->
                <li class="sub-menu">
                    <a href="javascript:void(0);" {{ setActive(['admin/email-templates']) }}>
                        <i class="fa fa-envelope"></i>
                        <span>Email Templates</span>
                    </a>
                    <ul class="sub">
                        <li {{ setActive(['admin/email-templates/'.Hashids::encode(1)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(1)) }}">Company Register</a></li>                        
<!--                        <li {{ setActive(['admin/email-templates/'.Hashids::encode(2)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(2)) }}">Activation Email</a></li>                        -->
                        <li {{ setActive(['admin/email-templates/'.Hashids::encode(3)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(3)) }}">Forgot Password</a></li>                        
                        <li {{ setActive(['admin/email-templates/'.Hashids::encode(4)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(4)) }}">Sale</a></li>                        
                        <!--<li {{ setActive(['admin/email-templates/'.Hashids::encode(5)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(5)) }}">Quotation</a></li>-->                        
                        <!--<li {{ setActive(['admin/email-templates/'.Hashids::encode(6)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(6)) }}">Purchase</a></li>-->                        
                        <!--<li {{ setActive(['admin/email-templates/'.Hashids::encode(7)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(7)) }}">Transfer</a></li>-->                        
                        <!--<li {{ setActive(['admin/email-templates/'.Hashids::encode(8)]) }}><a href="{{ url('admin/email-templates/'.Hashids::encode(8)) }}">Payment</a></li>-->                        
                    </ul>
                </li> 
                
            </ul>
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
