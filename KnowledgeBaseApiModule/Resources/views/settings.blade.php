@extends('layouts.app')

@section('title', __('Knowledge Base API'))

@section('content')
<div class="section-heading">
    {{ __('Knowledge Base API Settings') }}
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" method="POST" action="{{ route('knowledgebase-api-module.settings.save') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('api_token') ? ' has-error' : '' }}">
                    <label for="api_token" class="col-sm-2 control-label">{{ __('API Token') }}</label>

                    <div class="col-sm-6">
                        <div class="input-group">
                            <input id="api_token" type="text" class="form-control" name="api_token" value="{{ old('api_token', $api_token) }}" maxlength="64" required autofocus>
                            <span class="input-group-btn">
                                <button class="btn btn-default generate-token" type="button" data-loading-text="{{ __('Generating') }}...">{{ __('Generate New Token') }}</button>
                            </span>
                        </div>

                        @if ($errors->has('api_token'))
                            <span class="help-block">
                                <strong>{{ $errors->first('api_token') }}</strong>
                            </span>
                        @endif
                        <p class="help-block">
                            {{ __('This token is required to authenticate API requests. Use it as a query parameter: ?token=YOUR_TOKEN') }}
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.generate-token').click(function() {
            var btn = $(this);
            btn.button('loading');
            
            // Generate a random token
            var token = '';
            var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            
            for (var i = 0; i < 32; i++) {
                token += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            
            $('#api_token').val(token);
            
            btn.button('reset');
        });
    });
</script>
@endsection 