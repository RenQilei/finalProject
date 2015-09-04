@extends('frameworks.basic')

@section('header-extension')

@stop

@section('script-extension')

@stop

@section('body')
    <div class="container">
        <div class="jumbotron" style="margin-top: 50px;">
            <p>
                Welcome to CLCMS!<br/>
                This is a Cloud-base Learning Content Management System.<br/>
                At first, let's start our advanture by installing the system.<br/>
                Please press 'START' button below.
            </p>
            <div>
                <a href="{{url('install/step_one')}}">
                    <button class="btn btn-lg btn-success">Start to install...</button>
                </a>
            </div>
        </div>

    </div>
@stop