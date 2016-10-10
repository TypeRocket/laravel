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
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($media as $item)
                            <tr>
                                <th scope="row">{{$item->id}}</th>
                                <td>
                                    @if($item->ext == 'jpg' || $item->ext == 'png' || $item->ext == 'gif' || $item->ext == 'JPG' || $item->ext == 'PNG' || $item->ext == 'GIF')
                                        <a href="{{$item->sizes['local']['full'] or ''}}" target="_blank">
                                            <img width="50"
                                                 height="50"
                                                 src="{{ $item->sizes['local']['thumb'] }}?w=150&h=150"
                                                 alt="{{$item->alt}}"
                                            >
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <strong>Alt Text:</strong> {{ $item->alt }}
                                    <br>
                                    <strong>Caption:</strong> {{ $item->caption }}
                                </td>
                                <td>
                                    <a class="btn btn-default" href="/media/{!! $item->id !!}/edit">Edit</a>
                                    <form style="display: inline;"
                                          method="post"
                                          action="{!! route('media.destroy', ['media' => $item->id]) !!}"
                                    >
                                        {!! csrf_field() !!}
                                        {!! method_field('delete') !!}
                                        <button type="submit" class="btn confirm-action btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row">0</th>
                                <td colspan="4">No media yet.</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>

                    <ul class="list-group">

                    </ul>
                </div>
            </div>

            {!! $media->appends(Request::only('search'))->render() !!}

        </div>
    </div>

@stop
