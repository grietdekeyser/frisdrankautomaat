@if ($errors->any())
    <div class="alert alert-danger mt-4">
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-4">
        <ul class="list-unstyled">
            <li>{{ session('error') }}</li>
        </ul>
    </div>
@endif
