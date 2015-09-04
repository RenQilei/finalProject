@extends('frameworks.basic')

@section('header-extension')

@stop

@section('script-extension')

@stop

@section('body')
    <div id="step-one" class="container">
        {!!Form::open(['action' => 'InstallController@stepOneHandler', 'class' => 'form-horizontal'])!!}
            <div class="form-title">
                Administrator Information
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                    <input name="name" type="text" class="form-control" id="username" placeholder="Username">
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input name="email" type="email" class="form-control" id="email" placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="password-again" class="col-sm-2 control-label">Password Again</label>
                <div class="col-sm-10">
                    <input name="passwordAgain" type="password" class="form-control" id="password-again" placeholder="Password Again">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-lg btn-success">Start Journey :)</button>
                </div>
            </div>
        {!!Form::close()!!}
    </div>
@stop