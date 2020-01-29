
    <div class="lstViewd_leftBar_out">
        <div class="lstViewd_leftBar_Innr">
            <div class="lftBar_lastViwed_lsting">
                <h2>Last Viewed</h2>
                <ul>
                @forelse(GetLastViewed(Auth::id()) as $items)    
                    <li>
                        <div class="lftBar_lastViwed_box">
                            <h3><a href="{{ url('item-details/'. Hashids::encode($items->item->category->id)) }}">{{$items->item->category->category_name}}</a></h3>
                            <strong>{{$items->item->title}} <em>{{@$items->item->category->region->code}}</em></strong>
                            <div class="lftBar_lastViwed_boxPic"> 
                                <span class=""> 
                                    <a href="#.">
                                        <!-- <img src="{{ asset('uploads/not_found.png') }}" alt="#"> -->
                                        <img src="{{ (($items->item->record_images->isEmpty())?asset('uploads/not_found.png'):checkImage('items/'. $items->item->record_images[0]->name)) }}" alt="#">
                                    </a> 
                                </span> 
                            </div>
                        </div>
                    </li>
                @empty
                    <li>
                        <div class="lftBar_lastViwed_box">
                            <strong>No Items Viewed</strong>
                        </div>
                    </li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>
