<header id="header" class="headerOuter">
<div class="header_innr">
  <div class="headerNav_topOut">
    <div class="autoContent">
      <div class="headerNave_topInnr clearfix">
        <div class="headerNave_loginAvatar">
        @guest
          <div class="headerNave_avtrPic"> <span><img src="{{ asset("images/loginAvatr.png")}}" alt="#"></span> <a href="{{url('login')}}">Log In</a> </div>
        @endguest
        @auth
          <div class="headerNave_avtrPic"> <span><img src="{{ checkImage('users/thumbs/'.Auth::user()->profile_image)}}" alt="#"></span> <a href="{{url('login')}}">{{Auth::user()->name}}</a> - 
              <a href="{{ url('logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
          </div>
        @endauth
        </div>
        <div class="hdrTop_nav_socials clearfix">
          <ul>
            <li><a class="" href="{{settingValue('facebook')}}" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
            <li><a class="" href="{{settingValue('twitter')}}" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
            <li><a class="" href="{{settingValue('youtube')}}" target="_blank"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
            <li><a class="" href="{{settingValue('instagram')}}" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="headerMain_out">
    <div class="autoContent">
      <div class="headerMain_innr">
       <div class="navSearch_icon"><i class="fa fa-search"></i></div>
        <div class="menuIcon"></div>
        <div class="headerlogo_bar clearfix">
          <div class="logo"> <a href="{{ url('/') }}"><img src="{{ asset("images/logo.png")}}" alt="#"></a> </div>
          <div class="hdr_srchBar">
            {!! Form::open(['url' => '/search', 'class' => 'form-horizontal','id' => 'searchForm']) !!}
            <ul>
              <li>
                <div class="hdr_srchBar_field">
                    <input type="text" name="search_text" id="form_search_text">
                </div>
              </li>
              <li>
                <div class="hdr_srchBar_field leng_select"> <span class="">All Leagues</span>
                    <select name="league_id"  id="form_league_id">
                    <option value='0'>All Leagues</option>
                    @foreach(getLeagues() as $league)  
                        <option value="{{ $league->id }}">{{ $league->category_name }}</option>
                    @endforeach
                  </select>
                </div>
              </li>
              <li>
                <input class="all_button hdr_srchBar_findBtn" type="submit" value="Find">
              </li>
            </ul>
            {!! Form::close() !!}
          </div>
        </div>
        <div class="menu_outer">
          <div class="menu clearfix">
            <ul>
              <li {{ setActive(['/','home']) }}> <a href="{{ url('/') }}">Home</a></li>
              <li {{ setActive(['pages/about-us']) }}> <a href="{{ url('pages/about-us') }}">About</a></li>
              <li class="has_menu{{ setActive(['leagues','league-items','records'],false) }}"> <a href="javascript:void(0)">Leagues</a>
                <ul>
                  <li>
                    <ul>
                      <li> <a href="javascript:void(0)">Consoles</a>
                        <ul>
                          @foreach(getLeagues() as $league)  
                          <li> <a href="{{ url('leagues/'. Hashids::encode($league->id)) }}">{{ $league->category_name }}</a></li>
                          @endforeach
                        </ul>
                      </li>
                    </ul>                    
                  </li>
                </ul>
              </li>
              <li {{ setActive(['create-record','update-record']) }}> <a href="{{ url('create-record') }}">Create Record</a></li>
              <li class="has_menu"> <a href="javascript:void(0)">Active Leagues</a>
              <ul>
                  <li>
                    <ul>
                      <li> <a href="javascript:void(0)">Consoles</a>
                        <ul>
                          @foreach(getActiveLeaguesList() as $league)  
                          <li> <a href="{{ url('leagues/'. Hashids::encode($league->category->id)) }}">{{ $league->category->category_name }}</a></li>
                          @endforeach
                        </ul>
                      </li>
                    </ul>                    
                  </li>
                </ul>
              </li>
              <li class="has_menu{{ setActive(['watchlist-records','watchlist-collectors'],false) }}"> <a href="javascript:void(0)">Watchlist</a>
                <ul>
                  <li>
                    <ul>
                      <li> <a href="javascript:void(0)">Consoles</a>
                        <ul>
                          <li> <a href="{{ url('watchlist-records') }}">Watchlist Records</a></li>
                          <li> <a href="{{ url('watchlist-collectors') }}">Watchlist Collectors</a></li>
                        </ul>
                      </li>
                    </ul>                    
                  </li>
                </ul>
              </li>
            </ul>
            <div class="crossmenu"> <a href="#"></a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</header>