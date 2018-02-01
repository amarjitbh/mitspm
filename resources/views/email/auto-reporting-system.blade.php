<table border = "0"  cellpadding = "0" cellspacing = "0" width="600" align = "center" style = " border-collapse : collapse;  padding: 10px 10px 10px 10px; background-color: #fff;">
    <tr>
        <td align="center"  style = " padding: 0;">
            <table border ="0" cellpadding ="0" cellspacing ="0" width="100%" style="background-color: #f9f9f9;">
                <tr align="center">
                    <td colspan="2" style="padding: 15px 10px 15px 10px; background-color: #439fe0; color: #f7f7f7; font-weight: bold">
                        {{$final['company_name']}}
                    </td>
                </tr>

                @foreach($final['projects'] as $pro)
                <tr>
                    <td style="padding: 10px 15px 10px 15px; width: 100%;" align="center">
                        <p style="text-align: left; font-size: 12px; margin: 0; color: #999999; font-weight: bold;" >
                            Projects Summary:
                        </p>
                    </td>

                </tr>

                @if(!empty($pro['users']))
                    @foreach($pro['users'] as $user)
                <tr>
                    <td style="padding: 0px 15px 0px 15px; width: 100%;" align="center">
                        <table style="border-collapse: collapse; background-color: #fff; margin-bottom: 15px; border: 1px solid #efefef;" width="100%" cellpadding="10" cellspacing="0" border="0" >
                            <tr>
                                <td style="font-size: 12px; background-color: #e8e8e8; color: #333333; font-weight: bold;">
                                    {{$pro['project_name']}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top:0; margin-bottom: 8px; font-size: 12px; color: #888888;">Members Detail List</p>

                                    <!--Members Section-->
                                    <table style="border-collapse: collapse; font-size: 12px; border-radius: 5px; margin-top: 10px; background-color: #f5f5f5;" cellspacing="0" cellpadding="10" width="100%" border="0">
                                        <tr style="border-bottom: 1px solid #e5e5e5; color: #555555; font-size: 12px;">
                                            <td colspan="2" style="font-weight: bold;">
                                                {{$user['user']}}
                                            </td>
                                            <td width="20%" align="right" style="font-weight: bold;">
                                                <?php

                                                    $times = array_column($user['tasks'],'task_time');
                                                    $total = '';
                                                    foreach($times as $t) {

                                                        if(!empty($t)){
                                                            $total += toSeconds($t);
                                                        }
                                                    }
                                                    echo toTime($total);
                                                ?>
                                            </td>
                                        </tr>
                                        @foreach($user['tasks'] as $tasks)
                                            <tr style="color: #888888;">

                                                <td>{{$tasks['subject']}}</td><!--Task Name-->
                                                {{--<td width="25%" align="right">
                                                    <span style="display: inline-block; background-color: #00AFFB; color: #ffffff; border-radius: 25px; padding: 1px 8px; font-size: 10px;">In Progress</span>
                                                </td><!--Status-->--}}
                                                <td align="right">{{$tasks['task_time']}}</td><!--Time Taken-->
                                            </tr>
                                        @endforeach
                                    </table>
                                    <!--Members Section End-->


                                </td>
                            </tr>
                        </table>


                    </td>

                </tr>
                    @endforeach
                @endif
                @endforeach

            </table>
        </td>
    </tr>
</table>
