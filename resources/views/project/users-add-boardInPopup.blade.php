@if(!empty($users))
    <input type="hidden" class="hiddenUserAppendClassData" value="usersBox">
    <div class="checkbox">
        <input class="checkbox ml0 users" name="users" id="{{$users['id']}}" value="{{$users['id']}}" type="checkbox">
        <label class="font14" for="{{$users['id']}}">{{$users['name']}}</label>
    </div>
@endif

@if(empty($users))
    <input type="hidden" class="hiddenUserAppendClassData" value="alert_fail">
@endif