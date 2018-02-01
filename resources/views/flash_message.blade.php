<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/progressbar.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/toast.js') }}"></script>
<script src="{{ URL::asset('assets/js/main.js') }}"></script>
<div class="padding0">
    @if (Session::has('success'))

        <script>
            toastr.success('{!! Session::get('success') !!}');
        </script>

    @endif
    @if (Session::has('warning'))

        <script>
            toastr.warning('{!! Session::get('warning') !!}');
        </script>
    @endif
        <?php // pr($errors->count()); die;?>
    @if ($errors->count() > 0)

            @foreach ($errors->all() as $error)
                <script>

                    toastr.error('{{ $error }}');
                </script>
            @endforeach
    @endif
</div>