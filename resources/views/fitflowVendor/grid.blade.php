
    <div class="row" id="dashboard_module_top2">
        @if ($collection->contains($module1->id))
    <a class="col-sm-6 col-xs-12 col_H50 module1_back" href="{{ url($configName.'/'.$module1->slug.'/dashboard') }}">
               
                  <h3> {{ $module1->name_en }}  </h3>
            </a>
    @endif
     @if ($collection->contains($module2->id))
           <a class="col-sm-6 col-xs-12 col_H50 module2_back" href="{{ url($configName.'/'.$module2->slug.'/m2/dashboard') }}">
    
                 <h3>{{ $module2->name_en }} </h3>
            </a>
      @endif
       @if ($collection->contains($module3->id))
        <a class="col-sm-6 col-xs-12 col_H50 module3_back" href="{{ url($configM3.'/m3/report/bookings') }}">
    
                 <h3>{{ $module3->name_en }} </h3>
            </a>
       @endif
        @if ($collection->contains($module4->id))
            <a class="col-sm-6 col-xs-12 col_H50 module4_back" href="{{ url($configM4.'/categories') }}">
    
                 <h3>{{ $module4->name_en }}</h3>
            </a>
        @endif
    </div>