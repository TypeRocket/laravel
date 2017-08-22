@extends(config('typerocket.view.extends'))

@section(config('typerocket.view.section'))

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if (!empty($errors) && count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading">Upload Media</div>

                    <div class="panel-body">
                        <p>
                            <a href="{!! route('media.index') !!}" class="btn btn-default">
                                Media
                            </a>
                        </p>
                    </div>

                    <div class="panel-body typerocket-container">
                        {!! $form->open(['enctype' => "multipart/form-data", 'class' => "dropzone" ]) !!}

                        <div class="fallback">
                            {!! $form->dropzone('File') !!}
                            {!! $form->submit('Upload Media') !!}
                        </div>

                        {!! $form->close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
