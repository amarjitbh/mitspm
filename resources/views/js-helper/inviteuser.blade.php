<script>
    function removeProjectMemeber(id, type) {
        NProgress.start();
        if (type == 'pending') {
            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "{{route('ajax-delete-user-invite')}}",
                    data: {
                        _token: '{{csrf_token()}}',
                        id: id,
                    },
                    type: "POST",
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function (data) {

                        if (data == '100') {

                            var p = $('body').find("[id=pending_" + id + "]").hide();
                            console.log(p);
                            toastr.success('Record deleted successfully.');
                            $(this).hide();
                        } else {
                            toastr.error('Something went wrong please try again.');
                        }

                        return false;

                    }
                });
            }
            return false;
        } else if (type == "accepted") {
            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "{{route('ajax-delete-user-project')}}",
                    data: {
                        _token: '{{csrf_token()}}',
                        id: id,
                    },
                    type: "POST",
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function (data) {
                        if (data == '100') {
                            var p = $('body').find("[id=accepted_" + id + "]").hide();
                            toastr.success('Record deleted successfully.');
                        } else {
                            toastr.error('Something went wrong please try again.');
                        }
                    }
                });
            }
            return false;
        }
        NProgress.stop();
    }
    function popupOpen(ProjectId) {
        //alert('open');
        $('#projectId').val(ProjectId);
        $('#inviteUser').modal();
    }

    $(function () {
        $('#invitingUser').click(function (event){
            NProgress.start();
            event.preventDefault();

            var email = $('#emails').val();
            var user_project_id = $('#userProjectId').val();
            var projectId = $('#emails').attr('data-project-id');
            var userRole=$("#userRole option:selected").val();
            $('.c-loader').removeClass('loader-hide');
            //alert($('.c-loader').attr('class'));
            //return false;
            $.ajax({
                url: "{{route('ajax-invite-user-for-project')}}",
                data: {
                    _token: '{{csrf_token()}}',
                    projectId: projectId,
                    email:email,
                    role:userRole,
                    fxn: 'addUserInProject',
                },
                dataType:'json',
                type:"POST",
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success:function(response){

                    $.each(response, function (key, data) {
                        //alert(data.success);
                        if(data.success=="100"){
                            var userRole = '';
                            if(data.role == 1){
                                userRole = 'Project-Admin';
                            }else{
                                userRole = 'Member';
                            }
                            toastr.success('User successfully added');
                            $("#appendedInviteUser").append('<tr id='+data.type+'_'+data.id+'><td class="font-600">'+data.user+'</td><td>'+data.result+'</td><td>'+userRole+'</td><td>'+data.invitation+'</td><td><a class="btn btn-default btn-xs btn-danger" href="javascript:void(0);"onclick=removeProjectMemeber("'+data.id+'","'+data.type+'")>Remove</a></td></tr>');
                        }else if(data.success==2){
                            toastr.success('Role has been updated for the exisiting user.');
                        }else if(data.success==0){
                            toastr.error('Something went wrong.Please try again.');
                        }else if(data.success==3){
                            toastr.warning('User already exists.');
                        }else{
                            toastr.error('Something went wrong.Please try again.');
                        }

                    });
                    $('#emails').val('');

                },
                error: function (response) {

                    $.each(response, function (ind, val) {
                        if (!$.isEmptyObject(val.email)) {
                            toastr.warning(val.email);
                        } if (!$.isEmptyObject(val.role)) {
                            toastr.warning(val.role);
                        }
                    });
                },
                complete:function(){

                    $('.c-loader').addClass('loader-hide');
                },

            });
            return false;
            NProgress.stop();
        });



        $(".inviteMoreUsers").click(function (event) {
            var page = '';
            event.preventDefault();
            var projectId = $('#projectId').val();
            var adminEmail = $("input[name='adminEmail']").val();
            var UserEmail = $("input[name='UserEmail']").val();
            var page = $("input[name='page']").val();

            $.ajax({
                url: "{{route('invitedUserofProject')}}",
                data: {
                    _token: '{{csrf_token()}}',
                    projectId: projectId,
                    adminEmail: adminEmail,
                    UserEmail: UserEmail,
                    page: page,
                    fxn: 'invite',
                },
                type: "POST",
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success: function (data) {
                    console.log(data);
                    if (page == 'board_detail') {

                        if (data == 0) {

                            $('#alert_fail').html('User are invited');
                            $('#alert_fail').show();
                            $('#inviteUser').modal('hide');
                        } else if (data == 1) {

                            $('#alert_fail').html('project already assign to this user');
                            $('#alert_fail').show();
                            $('#inviteUser').modal('hide');
                        } else if (data == 2) {

                            $('#alert_fail').html('Request invite has been send');
                            $('#alert_fail').show();
                            $('#inviteUser').modal('hide');
                        } else if (data == 22) {

                            $('#alert_fail').html('Request invite has been send');
                            $('#alert_fail').show();
                            $('#inviteUser').modal('hide');
                        } else {

                            $('#usersBox').append(data);
                            $('#inviteUser').modal('hide');
                        }
                    } else {

                        $('#inviteUser').modal('hide');
                        $('.success-message-wish').removeClass('hide').show();
                        $('.error-message-wish').addClass('hide').show();
                    }

                },
                error: function (response) {
                    $.each(response, function (ind, val) {
                        if (!$.isEmptyObject(val.adminEmail)) {
                            $('.alert-warning').text(val.adminEmail);
                        } else if (!$.isEmptyObject(val.UserEmail)) {
                            $('.alert-warning').text(val.UserEmail);
                        }
                    });
                },

            });


            return false;
        });
    });


    $( document ).ready(function() {

        function changeUserRole(user_id,inputEmail,role,project_id){
            $.ajax({
                url: '{{route('change-user-role')}}',
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    'user_id': user_id,
                    'project_id': project_id,
                    'role': role,
                    'action': 'action-send-report',
                },

                success: function (response) {

                    if (response.success == true) {
                        if(response.role == 0){
                            $("#role"+user_id).html('<td>Member</td>')
                        }else{
                            $("#role"+user_id).html('<td>Project-Admin</td>')
                        }
                        $('#myModal').modal('hide');
                        toastr.success('Role change successfully done');
                    } else {
                        alert("sorry something went wrong please reload page and try again");
                    }
                }
            });
        }

        $('body').on('click','.change-role',function(e){
            e.stopPropagation();
            var user_id = $(this).attr('id');
            var email = $('#email'+user_id).text();
            $('#userId').val(user_id);
            var inputEmail = $('#userEmail').text(email);
            if(user_id != ''){

                $('#myModal').modal('show');
                //changeUserRole(user_id,inputEmail,'','');
            }
        })
        $('body').on('click','#changeRoleButton', function() {
            var user_id = $('#userId').val();
            var inputEmail = $('#userEmail').text();
            var project_id = $('#projectId').val();
            var role = $('#changeUserRole').val();
            changeUserRole(user_id,inputEmail,role,project_id);
        })
    });



</script>
