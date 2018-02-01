<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/progressbar.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/toast.js') }}"></script>
<script src="{{ URL::asset('assets/js/main.js') }}"></script>
<div class="padding0">
    @if (Session::has('success'))
        {{--<div class="alert alert-success">
            <button data-dismiss="alert" class="close">
                &times;
            </button>
            <i class="fa fa-check-circle"></i> &nbsp;
            {!! Session::get('success') !!}
        </div>--}}
        <script>
            toastr.success('{!! Session::get('success') !!}');
        </script>
    @endif
    @if (Session::has('warning'))
        {{--<div class="alert alert-warning">
            <button data-dismiss="alert" class="close">
                &times;
            </button>
            <i class="fa fa-exclamation-triangle"></i> &nbsp;
            {!! Session::get('warning') !!}
        </div>--}}

            <script>
                toastr.warning('{!! Session::get('warning') !!}');
            </script>
    @endif

</div>