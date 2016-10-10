@extends('layouts.master')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-header">
        <h3>Upload Media</h3>
    </div>

    {!! $form->open(['enctype' => "multipart/form-data", 'class' => "dropzone" ]) !!}

    <div class="fallback">
        {!! $form->dropzone('file') !!}
        {!! $form->submit('Upload Media') !!}
    </div>

    {!! $form->close() !!}

@stop

@section('scripts')
    <script src="/js/dropzone.js"></script>
@endsection
