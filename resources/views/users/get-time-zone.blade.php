
@if(!empty($timeZone))
    @foreach($timeZone as $zone)
        <option value="{{$zone['countries_timezone_id']}}">{{$zone['timezone']}}</option>
    @endforeach
@endif
