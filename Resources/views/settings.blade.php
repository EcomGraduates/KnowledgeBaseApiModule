@extends('layouts.app')

@section('title', __('Knowledge Base API'))

@section('content')
<div class="section-heading">
    {{ __('Knowledge Base API Settings') }} <span class="badge badge-info">v2.0.1</span>
    <span class="section-heading-right">
        <a href="{{ route('knowledgebaseapimodule.analytics') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-stats"></i> {{ __('View Analytics') }}
        </a>
    </span>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12 margin-top">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="glyphicon glyphicon-key"></i> {{ __('API Authentication') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('knowledgebaseapimodule.settings.save') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('api_token') ? ' has-error' : '' }}">
                            <label for="api_token" class="col-sm-2 control-label">{{ __('API Token') }}</label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input id="api_token" type="text" class="form-control" name="api_token" value="{{ old('api_token', $api_token) }}" maxlength="64" required autofocus>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info generate-token" type="button">
                                            <i class="glyphicon glyphicon-refresh"></i> {{ __('Generate New Token') }}
                                        </button>
                                    </span>
                                </div>

                                @if ($errors->has('api_token'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('api_token') }}</strong>
                                    </span>
                                @endif
                                <p class="help-block">
                                    <i class="glyphicon glyphicon-info-sign"></i> {{ __('This token is required to authenticate API requests. Keep it secure!') }}
                                </p>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('custom_url_template') ? ' has-error' : '' }}">
                            <label for="custom_url_template" class="col-sm-2 control-label">{{ __('Custom URL Template') }}</label>

                            <div class="col-sm-8">
                                <input id="custom_url_template" type="text" class="form-control" name="custom_url_template" value="{{ old('custom_url_template', $custom_url_template) }}" placeholder="https://example.com/docs/[mailbox]/[category]/[article]">

                                @if ($errors->has('custom_url_template'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('custom_url_template') }}</strong>
                                    </span>
                                @endif
                                <p class="help-block">
                                    <i class="glyphicon glyphicon-info-sign"></i> {{ __('Optional: Customize how article URLs are returned in API responses. Available placeholders:') }}
                                    <code>[mailbox]</code>, <code>[category]</code>, <code>[article]</code>
                                    <br>
                                    <small class="text-muted">{{ __('Example: https://your-app.com/api/docs/[category]/[article]') }}</small>
                                    <br>
                                    <small class="text-muted">{{ __('Leave empty to use default FreeScout knowledge base URLs.') }}</small>
                                </p>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('client_url_template') ? ' has-error' : '' }}">
                            <label for="client_url_template" class="col-sm-2 control-label">{{ __('Client URL Template') }}</label>

                            <div class="col-sm-8">
                                <input id="client_url_template" type="text" class="form-control" name="client_url_template" value="{{ old('client_url_template', $client_url_template ?? '') }}" placeholder="https://ecomgraduates.com/pages/documentation?category=[category]&article=[article]">

                                @if ($errors->has('client_url_template'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('client_url_template') }}</strong>
                                    </span>
                                @endif
                                <p class="help-block">
                                    <i class="glyphicon glyphicon-info-sign"></i> {{ __('Optional: Add front-end client URLs to API responses. Available placeholders:') }}
                                    <code>[mailbox]</code>, <code>[category]</code>, <code>[article]</code>
                                    <br>
                                    <small class="text-muted">{{ __('Example: https://your-website.com/pages/documentation?category=[category]&article=[article]') }}</small>
                                    <br>
                                    <small class="text-muted">{{ __('This will add a "client_url" field to API responses for direct linking to your frontend.') }}</small>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-floppy-disk"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> {{ __('API Documentation') }}</h3>
                </div>
                <div class="panel-body">
                    <h4>{{ __('Available Endpoints') }}</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Endpoint') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Example') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/categories</code></td>
                                    <td>{{ __('Get all categories from a mailbox') }}</td>
                                    <td><code>/api/knowledgebase/1/categories?token=YOUR_TOKEN</code></td>
                                </tr>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/categories/{categoryId}</code></td>
                                    <td>{{ __('Get a specific category with its articles') }}</td>
                                    <td><code>/api/knowledgebase/1/categories/5?token=YOUR_TOKEN</code></td>
                                </tr>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/categories/{categoryId}/articles/{articleId}</code></td>
                                    <td>{{ __('Get a specific article within a category') }}</td>
                                    <td><code>/api/knowledgebase/1/categories/5/articles/10?token=YOUR_TOKEN</code></td>
                                </tr>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/search</code></td>
                                    <td>{{ __('Search for articles by keyword') }}</td>
                                    <td><code>/api/knowledgebase/1/search?q=help&token=YOUR_TOKEN</code></td>
                                </tr>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/popular</code></td>
                                    <td>{{ __('Get most popular articles and categories') }}</td>
                                    <td><code>/api/knowledgebase/1/popular?token=YOUR_TOKEN</code></td>
                                </tr>
                                <tr>
                                    <td><code>/api/knowledgebase/{mailboxId}/export</code></td>
                                    <td>{{ __('Export all KB content for AI training') }}</td>
                                    <td><code>/api/knowledgebase/1/export?token=YOUR_TOKEN</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4>{{ __('Query Parameters') }}</h4>
                    <div class="well">
                        <dl class="dl-horizontal">
                            <dt><code>token</code> <span class="text-danger">*</span></dt>
                            <dd>{{ __('Your API token for authentication (required)') }}</dd>
                            
                            <dt><code>format</code></dt>
                            <dd>{{ __('Response format. Options: json (default)') }}</dd>
                            
                            <dt><code>q</code></dt>
                            <dd>{{ __('Search query keyword (required for search endpoint)') }}</dd>
                            
                            <dt><code>locale</code></dt>
                            <dd>{{ __('Optional locale for returned content (default: mailbox default locale)') }}</dd>
                            
                            <dt><code>limit</code></dt>
                            <dd>{{ __('Maximum number of results to return (applicable to popular endpoint, default: 5)') }}</dd>
                            
                            <dt><code>type</code></dt>
                            <dd>{{ __('Filter type for popular endpoint. Options: all (default), articles, categories') }}</dd>
                            
                            <dt><code>include_hidden</code></dt>
                            <dd>{{ __('Include hidden/unpublished content in export endpoint. Options: true, false (default)') }}</dd>
                        </dl>
                    </div>
                    
                    <h4>{{ __('Custom URL Templates') }}</h4>
                    <p>{{ __('You can customize how URLs are returned in API responses using the URL template settings.') }}</p>
                    
                    <div class="well">
                        <h5>{{ __('Available Placeholders:') }}</h5>
                        <ul>
                            <li><code>[mailbox]</code> - {{ __('Replaced with the mailbox ID') }}</li>
                            <li><code>[category]</code> - {{ __('Replaced with the category ID') }}</li>
                            <li><code>[article]</code> - {{ __('Replaced with the article ID (for article URLs only)') }}</li>
                        </ul>
                        
                        <h5>{{ __('Examples:') }}</h5>
                        <p><strong>{{ __('API URLs:') }}</strong></p>
                        <p>{{ __('If your Custom URL Template is:') }}</p>
                        <pre>https://your-app.com/api/docs/[category]/[article]</pre>
                        
                        <p>{{ __('Then an article with category ID 5 and article ID 10 will have this URL in the response:') }}</p>
                        <pre>https://your-app.com/api/docs/5/10</pre>
                        
                        <p><strong>{{ __('Client URLs:') }}</strong></p>
                        <p>{{ __('If your Client URL Template is:') }}</p>
                        <pre>https://ecomgraduates.com/pages/ecomifytheme-documentation?category=[category]&article=[article]</pre>
                        
                        <p>{{ __('Then an article with category ID 5 and article ID 10 will have this client_url in the response:') }}</p>
                        <pre>https://ecomgraduates.com/pages/ecomifytheme-documentation?category=5&article=10</pre>
                        
                        <p>{{ __('And a category with ID 5 will have this client_url:') }}</p>
                        <pre>https://ecomgraduates.com/pages/ecomifytheme-documentation?category=5</pre>
                        
                        <div class="alert alert-info">
                            <i class="glyphicon glyphicon-info-sign"></i> 
                            {{ __('Client URLs are useful when you need to link directly to your frontend documentation system from API responses.') }}
                        </div>
                    </div>

                    <h4>{{ __('Example Usage') }}</h4>
                    <p>{{ __('Curl example to retrieve categories:') }}</p>
                    <pre>curl -X GET "{{ url('/api/knowledgebase/1/categories?token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}"</pre>
                    
                    <p>{{ __('JavaScript example with fetch:') }}</p>
                    <pre>fetch('{{ url('/api/knowledgebase/1/categories?token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}')
    .then(response => response.json())
    .then(data => console.log(data));</pre>
                    
                    <p>{{ __('Search example:') }}</p>
                    <pre>curl -X GET "{{ url('/api/knowledgebase/1/search?q=help&token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}"</pre>
                    
                    <p>{{ __('Specific article example:') }}</p>
                    <pre>curl -X GET "{{ url('/api/knowledgebase/1/categories/5/articles/10?token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}"</pre>
                    
                    <p>{{ __('Popular content example:') }}</p>
                    <pre>curl -X GET "{{ url('/api/knowledgebase/1/popular?limit=5&type=all&token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}"</pre>
                    
                    <p>{{ __('Export all content example:') }}</p>
                    <pre>curl -X GET "{{ url('/api/knowledgebase/1/export?token=' . ($api_token ? $api_token : 'YOUR_TOKEN')) }}"</pre>

                    <h4>{{ __('Try it out') }} <i class="glyphicon glyphicon-play-circle"></i></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="api-test-endpoint">{{ __('Endpoint') }}</label>
                                <select id="api-test-endpoint" class="form-control">
                                    <option value="categories">{{ __('Get all categories') }}</option>
                                    <option value="category">{{ __('Get a specific category') }}</option>
                                    <option value="article">{{ __('Get a specific article') }}</option>
                                    <option value="search">{{ __('Search articles') }}</option>
                                    <option value="popular">{{ __('Get popular content') }}</option>
                                    <option value="export">{{ __('Export all content') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="api-test-mailbox">{{ __('Mailbox ID') }}</label>
                                <input type="number" id="api-test-mailbox" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group" id="category-id-container" style="display: none;">
                                <label for="api-test-category">{{ __('Category ID') }}</label>
                                <input type="number" id="api-test-category" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group" id="article-id-container" style="display: none;">
                                <label for="api-test-article">{{ __('Article ID') }}</label>
                                <input type="number" id="api-test-article" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group" id="search-keyword-container" style="display: none;">
                                <label for="api-test-keyword">{{ __('Search Keyword') }}</label>
                                <input type="text" id="api-test-keyword" class="form-control" value="" placeholder="Enter search term...">
                            </div>
                            <div class="form-group" id="popular-limit-container" style="display: none;">
                                <label for="api-test-limit">{{ __('Results Limit') }}</label>
                                <input type="number" id="api-test-limit" class="form-control" value="5" min="1" max="50">
                            </div>
                            <div class="form-group" id="popular-type-container" style="display: none;">
                                <label for="api-test-type">{{ __('Content Type') }}</label>
                                <select id="api-test-type" class="form-control">
                                    <option value="all">{{ __('All (categories & articles)') }}</option>
                                    <option value="articles">{{ __('Articles only') }}</option>
                                    <option value="categories">{{ __('Categories only') }}</option>
                                </select>
                            </div>
                            <button type="button" id="api-test-button" class="btn btn-success" {{ empty($api_token) ? 'disabled' : '' }}>
                                <i class="glyphicon glyphicon-send"></i> {{ __('Send Request') }}
                            </button>
                            
                            @if(empty($api_token))
                            <p class="text-danger margin-top">
                                <i class="glyphicon glyphicon-warning-sign"></i> {{ __('Please set and save an API token to test requests') }}
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">{{ __('Response') }}</h4>
                                </div>
                                <div class="panel-body">
                                    <pre id="api-response" style="max-height: 300px; overflow: auto;">{{ __('Response will appear here...') }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" {!! \Helper::cspNonceAttr() !!}>
    document.addEventListener('DOMContentLoaded', function() {
        // Token generator code
        const generateButton = document.querySelector('.generate-token');
        
        generateButton.addEventListener('click', function() {
            let token = '';
            const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            
            for (let i = 0; i < 32; i++) {
                token += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            
            document.getElementById('api_token').value = token;
        });

        // API testing functionality
        const endpointSelect = document.getElementById('api-test-endpoint');
        const categoryContainer = document.getElementById('category-id-container');
        const testButton = document.getElementById('api-test-button');
        const responseElement = document.getElementById('api-response');
        
        // Show/hide category ID field based on selected endpoint
        endpointSelect.addEventListener('change', function() {
            // First, hide all containers
            categoryContainer.style.display = 'none';
            document.getElementById('article-id-container').style.display = 'none';
            document.getElementById('search-keyword-container').style.display = 'none';
            document.getElementById('popular-limit-container').style.display = 'none';
            document.getElementById('popular-type-container').style.display = 'none';
            
            // Then show only the relevant ones based on selection
            if (this.value === 'category') {
                categoryContainer.style.display = 'block';
            } else if (this.value === 'article') {
                categoryContainer.style.display = 'block';
                document.getElementById('article-id-container').style.display = 'block';
            } else if (this.value === 'search') {
                document.getElementById('search-keyword-container').style.display = 'block';
            } else if (this.value === 'popular') {
                document.getElementById('popular-limit-container').style.display = 'block';
                document.getElementById('popular-type-container').style.display = 'block';
            }
        });
        
        // Handle API test request
        testButton.addEventListener('click', function() {
            const endpoint = endpointSelect.value;
            const mailboxId = document.getElementById('api-test-mailbox').value;
            let url = '';
            
            if (endpoint === 'categories') {
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/categories?token={{ $api_token }}';
            } else if (endpoint === 'category') {
                const categoryId = document.getElementById('api-test-category').value;
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/categories/' + categoryId + '?token={{ $api_token }}';
            } else if (endpoint === 'article') {
                const categoryId = document.getElementById('api-test-category').value;
                const articleId = document.getElementById('api-test-article').value;
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/categories/' + categoryId + '/articles/' + articleId + '?token={{ $api_token }}';
            } else if (endpoint === 'search') {
                const keyword = document.getElementById('api-test-keyword').value;
                if (!keyword) {
                    responseElement.textContent = 'Error: Search keyword is required';
                    return;
                }
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/search?q=' + encodeURIComponent(keyword) + '&token={{ $api_token }}';
            } else if (endpoint === 'popular') {
                const limit = document.getElementById('api-test-limit').value;
                const type = document.getElementById('api-test-type').value;
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/popular?token={{ $api_token }}&limit=' + encodeURIComponent(limit) + '&type=' + encodeURIComponent(type);
            } else if (endpoint === 'export') {
                url = '{{ url("/api/knowledgebase/") }}/' + mailboxId + '/export?token={{ $api_token }}';
            }
            
            // Show loading message
            responseElement.textContent = 'Loading...';
            
            // Make the API call
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Format and display the JSON response
                    responseElement.textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    responseElement.textContent = 'Error: ' + error.message;
                });
        });
    });
</script>
@endsection 