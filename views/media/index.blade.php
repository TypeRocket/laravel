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
        <h3>Media Manager</h3>
    </div>

    <p>
        <a href="{!! route('media.create') !!}" class="btn btn-default">
            Upload Media
        </a>
    </p>

    @include('elements.search')

    <div class="image-grid">
        <ul>
        @foreach($media as $item)
            <li>
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
        @endforeach
        </ul>
    </div>


    {!! $media->appends(Request::only('search'))->render() !!}

@stop
