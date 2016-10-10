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
                    <div class="panel-heading">Edit Media</div>

                    <div class="panel-body">
                        <p>
                            <a href="{!! route('media.index') !!}" class="btn btn-default">
                                Media
                            </a>
                        </p>
                    </div>

                    <div class="panel-body typerocket-container">
                        <a target="_blank" href="{{ $form->getModel()->sizes['local']['full'] }}">
                            <img src="{{ $form->getModel()->sizes['local']['thumb'] }}?w=150&h=150" alt="{{$form->getModel()->alt}}">
                        </a>
                        <hr>
                        {!! $form->open() !!}
                        {!! $form->text('alt')->setLabel('SEO Image Description') !!}
                        {!! $form->text('Caption')->setSetting('help', 'Used by search feature') !!}
                        {!! $form->submit('Update Media') !!}
                        {!! $form->close() !!}
                    </div>


                </div>

            </div>
        </div>
    </div>


@stop
