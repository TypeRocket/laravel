@extends('layouts.app')

@section('content')

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
                    <div class="panel-heading">Media Manager</div>

                    <div class="panel-body">
                        <p>
                            <a href="{!! route('media.create') !!}" class="btn btn-default">
                                Upload Media
                            </a>
                        </p>

                        <ul class="list-group">
                            @forelse($media as $item)
                                <li class="list-group-item">
                                    @if($item->ext == 'jpg' || $item->ext == 'png' || $item->ext == 'gif' || $item->ext == 'JPG' || $item->ext == 'PNG' || $item->ext == 'GIF')
                                        <a href="{{$item->sizes['s3']['full'] or ''}}" target="_blank">
                                            <img width="120"
                                                 height="120"
                                                 src="https://{{ $item->sizes['s3']['full'] }}?w=120&h=120"
                                                 alt="{{$item->alt}}"
                                            >
                                        </a>
                                    @endif
                                    <p>
                                        <a href="/media/{!! $item->id !!}/edit">{{$item->id}}: {{ $item->alt }}</a>
                                    </p>

                                    <div>
                                        <form method="post" action="{!! route('media.destroy', ['media' => $item->id]) !!}"
                                              style="display: inline;">
                                            {!! csrf_field() !!}
                                            {!! method_field('delete') !!}
                                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item">No media yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {!! $media->appends(Request::only('search'))->render() !!}

        </div>
    </div>

@stop
