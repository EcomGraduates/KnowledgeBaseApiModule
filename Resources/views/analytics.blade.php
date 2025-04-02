@extends('layouts.app')

@section('title', __('Knowledge Base Analytics'))

@section('stylesheets')
<style {!! \Helper::cspNonceAttr() !!}>
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .stats-card {
        text-align: center;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .stats-card h3 {
        font-size: 18px;
        margin-bottom: 10px;
    }
    .stats-card .number {
        font-size: 24px;
        font-weight: bold;
    }
    .category-stats {
        background-color: #e3f2fd;
    }
    .article-stats {
        background-color: #e8f5e9;
    }
    .stats-summary-card {
        padding: 10px;
        border-radius: 4px;
        background-color: #f9f9f9;
    }
    .stats-summary-card h5 {
        margin-bottom: 5px;
        font-size: 14px;
        color: #666;
    }
    .stats-summary-card .stats-value {
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }
    .margin-right-10 {
        margin-right: 10px;
    }
    .margin-right-5 {
        margin-right: 5px;
    }
    .margin-bottom {
        margin-bottom: 20px;
    }
    /* Bootstrap 3 panel header fix */
    .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chart-type-toggle {
        margin-bottom: 0;
    }
    /* Tab content padding */
    .tab-content {
        padding-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="section-heading">
    {{ __('Knowledge Base Analytics') }}
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12 margin-top">
            <div class="panel panel-default margin-bottom">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ __('Filter Analytics') }}</h3>
                </div>
                <div class="panel-body">
                    <form method="GET" action="{{ route('knowledgebaseapimodule.analytics') }}" class="form-inline">
                        <div class="form-group margin-right-10">
                            <label for="mailbox_id" class="margin-right-5">{{ __('Mailbox') }}:</label>
                            <select name="mailbox_id" id="mailbox_id" class="form-control">
                                <option value="">{{ __('All Mailboxes') }}</option>
                                @foreach ($mailboxes as $mailbox)
                                    <option value="{{ $mailbox->id }}" {{ $current_mailbox_id == $mailbox->id ? 'selected' : '' }}>{{ $mailbox->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group margin-right-10">
                            <label for="period" class="margin-right-5">{{ __('Time Period') }}:</label>
                            <select name="period" id="period" class="form-control">
                                <option value="all" {{ $current_period == 'all' ? 'selected' : '' }}>{{ __('All Time') }}</option>
                                <option value="today" {{ $current_period == 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                                <option value="yesterday" {{ $current_period == 'yesterday' ? 'selected' : '' }}>{{ __('Yesterday') }}</option>
                                <option value="week" {{ $current_period == 'week' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                                <option value="month" {{ $current_period == 'month' ? 'selected' : '' }}>{{ __('Last 30 Days') }}</option>
                                <option value="year" {{ $current_period == 'year' ? 'selected' : '' }}>{{ __('Last Year') }}</option>
                            </select>
                        </div>
                        <div class="form-group margin-right-10">
                            <label for="limit" class="margin-right-5">{{ __('Limit') }}:</label>
                            <select name="limit" id="limit" class="form-control">
                                <option value="5" {{ request()->input('limit') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ !request()->has('limit') || request()->input('limit') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request()->input('limit') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request()->input('limit') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    </form>
                </div>
            </div>
            
            <!-- Overview Stats -->
            <div class="row">
                <div class="col-md-12 margin-bottom">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ __('Summary') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                @php
                                    $totalCategoryViews = array_sum(array_column($top_categories, 'view_count'));
                                    $totalArticleViews = array_sum(array_column($top_articles, 'view_count'));
                                    $totalSearchCount = isset($top_searches) && count($top_searches) > 0 ? 
                                        array_sum(array_column($top_searches->toArray(), 'search_count')) : 0;
                                    $totalZeroResultCount = isset($zero_result_searches) && count($zero_result_searches) > 0 ? 
                                        array_sum(array_column($zero_result_searches->toArray(), 'search_count')) : 0;
                                    $totalViews = $totalCategoryViews + $totalArticleViews;
                                    
                                    // Get period description
                                    $periodDesc = 'All Time';
                                    if ($current_period == 'today') {
                                        $periodDesc = 'Today';
                                    } elseif ($current_period == 'yesterday') {
                                        $periodDesc = 'Yesterday';
                                    } elseif ($current_period == 'week') {
                                        $periodDesc = 'Last 7 Days';
                                    } elseif ($current_period == 'month') {
                                        $periodDesc = 'Last 30 Days';
                                    } elseif ($current_period == 'year') {
                                        $periodDesc = 'Last Year';
                                    }
                                    
                                    // Calculate search success rate
                                    $searchSuccessRate = 0;
                                    if ($totalSearchCount > 0) {
                                        $searchSuccessRate = round(100 - (($totalZeroResultCount / $totalSearchCount) * 100), 1);
                                    }
                                @endphp
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Period') }}</h5>
                                        <div class="stats-value">{{ $periodDesc }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Total Views') }}</h5>
                                        <div class="stats-value">{{ number_format($totalViews) }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Total Searches') }}</h5>
                                        <div class="stats-value">{{ number_format($totalSearchCount) }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Search Success Rate') }}</h5>
                                        <div class="stats-value">{{ $searchSuccessRate }}%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 15px;">
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Category Views') }}</h5>
                                        <div class="stats-value">{{ number_format($totalCategoryViews) }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Article Views') }}</h5>
                                        <div class="stats-value">{{ number_format($totalArticleViews) }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Zero Result Searches') }}</h5>
                                        <div class="stats-value">{{ number_format($totalZeroResultCount) }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="stats-summary-card text-center">
                                        <h5>{{ __('Articles and Categories') }}</h5>
                                        <div class="stats-value">{{ count($top_articles) + count($top_categories) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Individual Stat Cards -->
            <div class="row">
                <div class="col-md-6">
                    <div class="stats-card category-stats">
                        <h3>{{ __('Total Categories Tracked') }}</h3>
                        <div class="number">{{ count($top_categories) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card article-stats">
                        <h3>{{ __('Total Articles Tracked') }}</h3>
                        <div class="number">{{ count($top_articles) }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="active">
                    <a href="#categories" role="tab" data-toggle="tab">{{ __('Categories') }}</a>
                </li>
                <li>
                    <a href="#articles" role="tab" data-toggle="tab">{{ __('Articles') }}</a>
                </li>
                <li>
                    <a href="#searches" role="tab" data-toggle="tab">{{ __('Searches') }}</a>
                </li>
                <li>
                    <a href="#zero-result-searches" role="tab" data-toggle="tab">{{ __('No Results Searches') }}</a>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Categories Tab -->
                <div class="tab-pane active" id="categories">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Categories Visualization') }}</h3>
                                    <div class="btn-group chart-type-toggle" role="group" aria-label="Chart Types">
                                        <button type="button" class="btn btn-sm btn-primary active" data-chart-type="bar" data-target="categories">{{ __('Bar') }}</button>
                                        <button type="button" class="btn btn-sm btn-default" data-chart-type="pie" data-target="categories">{{ __('Pie') }}</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="chart-container">
                                        <canvas id="categoriesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Categories') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @if (count($top_categories) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Rank') }}</th>
                                                        <th>{{ __('Category') }}</th>
                                                        <th>{{ __('Views') }}</th>
                                                        <th>{{ __('Percentage') }}</th>
                                                        <th>{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalCategoryViews = array_sum(array_column($top_categories, 'view_count'));
                                                    @endphp
                                                    @foreach ($top_categories as $index => $category)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $category['name'] }}</td>
                                                            <td>{{ $category['view_count'] }}</td>
                                                            <td>
                                                                @if($totalCategoryViews > 0)
                                                                    {{ round(($category['view_count'] / $totalCategoryViews) * 100, 1) }}%
                                                                @else
                                                                    0%
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ $category['url'] }}" class="btn btn-sm btn-default" target="_blank">{{ __('View') }}</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('No data available') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Articles Tab -->
                <div class="tab-pane" id="articles">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Articles Visualization') }}</h3>
                                    <div class="btn-group chart-type-toggle" role="group" aria-label="Chart Types">
                                        <button type="button" class="btn btn-sm btn-primary active" data-chart-type="bar" data-target="articles">{{ __('Bar') }}</button>
                                        <button type="button" class="btn btn-sm btn-default" data-chart-type="pie" data-target="articles">{{ __('Pie') }}</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="chart-container">
                                        <canvas id="articlesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Articles') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @if (count($top_articles) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Rank') }}</th>
                                                        <th>{{ __('Article') }}</th>
                                                        <th>{{ __('Views') }}</th>
                                                        <th>{{ __('Percentage') }}</th>
                                                        <th>{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalArticleViews = array_sum(array_column($top_articles, 'view_count'));
                                                    @endphp
                                                    @foreach ($top_articles as $index => $article)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $article['title'] }}</td>
                                                            <td>{{ $article['view_count'] }}</td>
                                                            <td>
                                                                @if($totalArticleViews > 0)
                                                                    {{ round(($article['view_count'] / $totalArticleViews) * 100, 1) }}%
                                                                @else
                                                                    0%
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ $article['url'] }}" class="btn btn-sm btn-default" target="_blank">{{ __('View') }}</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('No data available') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Searches Tab -->
                <div class="tab-pane" id="searches">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Search Queries') }}</h3>
                                    <div class="btn-group chart-type-toggle" role="group" aria-label="Chart Types">
                                        <button type="button" class="btn btn-sm btn-primary active" data-chart-type="bar" data-target="searches">{{ __('Bar') }}</button>
                                        <button type="button" class="btn btn-sm btn-default" data-chart-type="pie" data-target="searches">{{ __('Pie') }}</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="chart-container">
                                        <canvas id="searchesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('Top Searches') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @if (count($top_searches) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Rank') }}</th>
                                                        <th>{{ __('Search Query') }}</th>
                                                        <th>{{ __('Search Count') }}</th>
                                                        <th>{{ __('Results') }}</th>
                                                        <th>{{ __('Locale') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalSearchCount = array_sum(array_column($top_searches->toArray(), 'search_count'));
                                                    @endphp
                                                    @foreach ($top_searches as $index => $search)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $search->query }}</td>
                                                            <td>{{ $search->search_count }}</td>
                                                            <td>{{ $search->results_count }}</td>
                                                            <td>{{ $search->locale ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('No search data available') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Zero Result Searches Tab -->
                <div class="tab-pane" id="zero-result-searches">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default margin-bottom">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{ __('No Results Searches') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @if (count($zero_result_searches) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Rank') }}</th>
                                                        <th>{{ __('Search Query') }}</th>
                                                        <th>{{ __('Search Count') }}</th>
                                                        <th>{{ __('Locale') }}</th>
                                                        <th>{{ __('Last Searched') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($zero_result_searches as $index => $search)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $search->query }}</td>
                                                            <td>{{ $search->search_count }}</td>
                                                            <td>{{ $search->locale ?? '-' }}</td>
                                                            <td>{{ $search->updated_at->format('Y-m-d H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('No zero-result searches found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script {!! \Helper::cspNonceAttr() !!} src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script {!! \Helper::cspNonceAttr() !!}>
document.addEventListener('DOMContentLoaded', function() {
    // Data for categories chart
    const categoryData = {
        labels: [
            @foreach ($top_categories as $category)
                '{{ str_replace("'", "\'", $category['name']) }}',
            @endforeach
        ],
        datasets: [{
            label: '{{ __('Category Views') }}',
            data: [
                @foreach ($top_categories as $category)
                    {{ $category['view_count'] }},
                @endforeach
            ],
            backgroundColor: [
                '#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF6D01', 
                '#46BFBD', '#F7464A', '#949FB1', '#57A1C6', '#8075C4'
            ],
            borderWidth: 1
        }]
    };

    // Data for articles chart
    const articleData = {
        labels: [
            @foreach ($top_articles as $article)
                '{{ str_replace("'", "\'", mb_substr($article['title'], 0, 30)) }}{{ mb_strlen($article['title']) > 30 ? "..." : "" }}',
            @endforeach
        ],
        datasets: [{
            label: '{{ __('Article Views') }}',
            data: [
                @foreach ($top_articles as $article)
                    {{ $article['view_count'] }},
                @endforeach
            ],
            backgroundColor: [
                '#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF6D01', 
                '#46BFBD', '#F7464A', '#949FB1', '#57A1C6', '#8075C4'
            ],
            borderWidth: 1
        }]
    };
    
    // Data for searches chart
    const searchData = {
        labels: [
            @foreach ($top_searches as $search)
                '{{ str_replace("'", "\'", mb_substr($search->query, 0, 30)) }}{{ mb_strlen($search->query) > 30 ? "..." : "" }}',
            @endforeach
        ],
        datasets: [{
            label: '{{ __('Search Count') }}',
            data: [
                @foreach ($top_searches as $search)
                    {{ $search->search_count }},
                @endforeach
            ],
            backgroundColor: [
                '#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF6D01', 
                '#46BFBD', '#F7464A', '#949FB1', '#57A1C6', '#8075C4'
            ],
            borderWidth: 1
        }]
    };

    // Configuration for bar chart
    const barChartConfig = {
        type: 'bar',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y;
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '{{ __('Count') }}'
                    }
                }
            }
        }
    };
    
    // Configuration for pie chart
    const pieChartConfig = {
        type: 'pie',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const dataset = context.dataset;
                            const total = dataset.data.reduce((acc, data) => acc + data, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    };

    // Chart instances
    let categoriesChart = null;
    let articlesChart = null;
    let searchesChart = null;

    // Function to create or update chart
    function createOrUpdateChart(chartId, data, chartType, chartInstance) {
        const ctx = document.getElementById(chartId);
        if (!ctx) return null;
        
        const context = ctx.getContext('2d');
        
        // Destroy existing chart if it exists
        if (chartInstance) {
            chartInstance.destroy();
        }
        
        // Create configuration based on chart type
        let config;
        if (chartType === 'pie') {
            config = JSON.parse(JSON.stringify(pieChartConfig));
        } else {
            config = JSON.parse(JSON.stringify(barChartConfig));
        }
        
        config.data = data;
        return new Chart(context, config);
    }

    // Initialize charts
    categoriesChart = createOrUpdateChart('categoriesChart', categoryData, 'bar', null);
    articlesChart = createOrUpdateChart('articlesChart', articleData, 'bar', null);
    searchesChart = createOrUpdateChart('searchesChart', searchData, 'bar', null);

    // Add event listeners for chart type toggle buttons
    document.querySelectorAll('.chart-type-toggle button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const chartType = this.getAttribute('data-chart-type');
            
            // Update active state of buttons
            document.querySelectorAll(`.chart-type-toggle[aria-label="Chart Types"] button[data-target="${target}"]`).forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.remove('active');
                btn.classList.add('btn-default');
            });
            this.classList.remove('btn-default');
            this.classList.add('btn-primary');
            this.classList.add('active');
            
            // Update chart
            if (target === 'categories') {
                categoriesChart = createOrUpdateChart('categoriesChart', categoryData, chartType, categoriesChart);
            } else if (target === 'articles') {
                articlesChart = createOrUpdateChart('articlesChart', articleData, chartType, articlesChart);
            } else if (target === 'searches') {
                searchesChart = createOrUpdateChart('searchesChart', searchData, chartType, searchesChart);
            }
        });
    });
});
</script>
@endsection 