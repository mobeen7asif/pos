<div class="entryType_region_tabsTitle all_item_view  clearfix">
    <ul>
      <li> <a class="entryType_region_tabsBtn{{ setActive(['leagues'],false) }}"  href="{{ url('leagues/'.Hashids::encode($category->id)) }}"><em>League Oveview</em></a> </li>
      <li> <a class="entryType_region_tabsBtn{{ setActive(['league-items','item-details'],false) }}"  href="{{ url('league-items/'.Hashids::encode($category->id)) }}"><em>All Items</em></a> </li>
      <li> <a class="entryType_region_tabsBtn{{ setActive(['records','record-details'],false) }}" href="{{ url('records/'.Hashids::encode($category->id)) }}"><em>All Records</em></a> </li>
      <li> <a class="entryType_region_tabsBtn" href="#"><em>Scoreboard</em></a> </li>
      <li> <a class="entryType_region_tabsBtn" href="#"><em>Rules</em></a> </li>
      <li> <a class="entryType_region_tabsBtn{{ setActive(['create-record','update-record'],false) }}" href="{{ url('create-record/'.Hashids::encode($category->id)) }}"><em>Create Record</em></a> </li>
    </ul>
</div>