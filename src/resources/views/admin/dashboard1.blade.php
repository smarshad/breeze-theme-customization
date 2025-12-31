@extends('layoutsnew.app')
@push('styles')
<!-- C3 Chart css -->
<link href="{{asset('backend/libs/c3/c3.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
<!-- Begin page -->
@section('content')
<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Adminox</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dashboard 1</li>
                        </ol>
                    </div>
                    <h4 class="page-title"><i class="fas fa-chart-line"></i> Dashboard</h4>
                </div>
            </div>
        </div>

        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">

                    <p class="text-muted">Expense Analytics & Insights</p>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary period-btn active" data-period="today">
                            Today
                        </button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="week">
                            Week
                        </button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="month">
                            Month
                        </button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="year">
                            Year
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">


            <div class="col-xl-3 col-sm-6">
                <div class="card-box widget-box-two widget-two-custom">
                    <div class="media">
                        <div class="avatar-lg rounded-circle bg-primary widget-two-icon align-self-center">
                            <i class="fas fa-wallet avatar-title font-30 text-white"></i>
                        </div>

                        <div class="wigdet-two-content media-body">
                            <p class="m-0 text-uppercase font-weight-medium text-truncate" title="Statistics">Total Expenses</p>
                            <h3 class="font-weight-medium my-2"> <span data-plugin="counterup" id="totalExpenses">0</span></h3>
                            <p class="m-0 dateRange"></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-sm-6">
                <div class="card-box widget-box-two widget-two-custom ">
                    <div class="media">
                        <div class="avatar-lg rounded-circle bg-primary widget-two-icon align-self-center">
                            <i class="fas fa-receipt avatar-title font-30 text-white"></i>
                        </div>

                        <div class="wigdet-two-content media-body">
                            <p class="m-0 text-uppercase font-weight-medium text-truncate" title="Statistics">Transactions</p>
                            <h3 class="font-weight-medium my-2"> <span data-plugin="counterup" id="totalCount">0</span></h3>
                            <p class="m-0 dateRange"></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-sm-6">
                <div class="card-box widget-box-two widget-two-custom ">
                    <div class="media">
                        <div class="avatar-lg rounded-circle bg-primary widget-two-icon align-self-center">
                            <i class="fas fa-chart-bar avatar-title font-30 text-white"></i>
                        </div>

                        <div class="wigdet-two-content media-body">
                            <p class="m-0 text-uppercase font-weight-medium text-truncate" title="Statistics">Average Expense</p>
                            <h3 class="font-weight-medium my-2"><span data-plugin="counterup" id="averageExpense">0</span></h3>
                            <p class="m-0 dateRange"></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-sm-6">
                <div class="card-box widget-box-two widget-two-custom ">
                    <div class="media">
                        <div class="avatar-lg rounded-circle bg-primary widget-two-icon align-self-center">
                            <i class="fas fa-arrow-up avatar-title font-30 text-white"></i>
                        </div>

                        <div class="wigdet-two-content media-body">
                            <p class="m-0 text-uppercase font-weight-medium text-truncate" title="Statistics">Highest Expense</p>
                            <h3 class="font-weight-medium my-2"><span data-plugin="counterup" id="highestExpense">0</span></h3>
                            <p class="m-0 dateRange"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card-box">
                    <h4 class="header-title mb-4">Revenue Comparison</h4>

                    <div class="text-center">
                        <h5 class="font-weight-normal text-muted">You have to pay</h5>
                        <h3 class="mb-3"><i class="mdi mdi-arrow-up-bold-hexagon-outline text-success"></i> 25643 <small>USD</small></h3>
                    </div>

                    <div class="chart-container" dir="ltr">
                        <div class="" style="height:280px" id="platform_type_dates_donut"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card-box">
                    <h4 class="header-title mb-4">Visitors Overview</h4>

                    <div class="text-center">
                        <h5 class="font-weight-normal text-muted">You have to pay</h5>
                        <h3 class="mb-3"><i class="mdi mdi-arrow-down-bold-hexagon-outline text-danger"></i> 5623 <small>USD</small></h3>
                    </div>

                    <div class="chart-container" dir="ltr">
                        <div class="" style="height:280px" id="categoryChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card-box">
                    <h4 class="header-title mb-4">Goal Completion</h4>

                    <div class="text-center">
                        <h5 class="font-weight-normal text-muted">You have to pay</h5>
                        <h3 class="mb-3"><i class="mdi mdi-arrow-up-bold-hexagon-outline text-success"></i> 12548 <small>USD</small></h3>
                    </div>

                    <div class="chart-container" dir="ltr">
                        <div class="chart has-fixed-height" style="height:280px" id="page_views_today"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card-box">
                    <h4 class="header-title">Recent Candidates</h4>
                    <p class="sub-header">
                        Your awesome text goes here.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-hover m-0 table-actions-bar">

                            <thead>
                                <tr>
                                    <th>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-light btn-xs dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class="mdi mdi-chevron-down"></i></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Job Timing</th>
                                    <th>Salary</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <img src="{{asset('backend/images/users/avatar-2.jpg')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-medium">Tomaslau</h5>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-map-marker text-primary"></i> New York
                                    </td>

                                    <td>
                                        <i class="mdi mdi-clock-outline text-success"></i> Full Time
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-usd text-warning"></i> 3265
                                    </td>

                                    <td>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-close"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="{{asset('backend/images/users/avatar-3.jpg')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-medium">Erwin E. Brown</h5>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-map-marker text-primary"></i> California
                                    </td>

                                    <td>
                                        <i class="mdi mdi-clock-outline text-success"></i> Part Time
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-usd text-warning"></i> 1365
                                    </td>

                                    <td>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-close"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="{{asset('backend/images/users/avatar-4.jpg')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-medium">Margeret V. Ligon</h5>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-map-marker text-primary"></i> New York
                                    </td>

                                    <td>
                                        <i class="mdi mdi-clock-outline text-success"></i> Full Time
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-usd text-warning"></i> 115248
                                    </td>

                                    <td>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-close"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="{{asset('backend/images/users/avatar-5.jpg')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-medium">Jose D. Delacruz</h5>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-map-marker text-primary"></i> New York
                                    </td>

                                    <td>
                                        <i class="mdi mdi-clock-outline text-success"></i> Part Time
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-usd text-warning"></i> 2451
                                    </td>

                                    <td>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-close"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="{{asset('backend/images/users/avatar-8.jpg')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-medium">Luke J. Sain</h5>
                                    </td>

                                    <td>
                                        <i class="mdi mdi-map-marker text-primary"></i> Australia
                                    </td>

                                    <td>
                                        <i class="mdi mdi-clock-outline text-success"></i> Part Time
                                    </td>

                                    <td>
                                        <i class="mdi mdi-currency-usd text-warning"></i> 3265
                                    </td>

                                    <td>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="table-action-btn"><i class="mdi mdi-close"></i></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- end col -->

            <div class="col-xl-3 col-lg-6">
                <div class="card-box">
                    <h4 class="header-title mb-4">Total Unique Visitors</h4>

                    <div class="widget-chart text-center" dir="ltr">

                        <div id="donut-chart" style="height: 280px;"></div>

                        <div class="row text-center mt-4">
                            <div class="col-6">
                                <h3 data-plugin="counterup">1,507</h3>
                                <p class="text-muted mb-0">Visitors Male</p>
                            </div>
                            <div class="col-6">
                                <h3 data-plugin="counterup">854</h3>
                                <p class="text-muted mb-1">Visitors Female</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card-box">
                    <h4 class="header-title mb-4">Number of Transactions</h4>

                    <div class="widget-chart text-center" dir="ltr">

                        <div id="pie-chart" style="height: 280px;"></div>

                        <div class="row text-center mt-4">
                            <div class="col-6">
                                <h3 data-plugin="counterup">2,854</h3>
                                <p class="text-muted mb-0">Payment Done</p>
                            </div>
                            <div class="col-6">
                                <h3 data-plugin="counterup">22</h3>
                                <p class="text-muted mb-1">Payment Due</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!--- end row -->

    </div> <!-- end container-fluid -->

</div> <!-- end content -->
@endsection

@push('scripts')
<!--C3 Chart-->
<script src="{{asset('backend/libs/d3/d3.min.js')}}"></script>
<script src="{{asset('backend/libs/c3/c3.min.js')}}"></script>

<script src="{{asset('backend/libs/echarts/echarts.min.js')}}"></script>

<script src="{{asset('backend/js/pages/dashboard.init.js')}}"></script>
<!-- <script src="{{asset('backend/js/dashboard.js')}}"></script> -->

<script>
    // Chart instances
    let charts = {};
    let currentPeriod = 'month';

    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();

    });

    function loadDashboardData() {
        Promise.all([
            fetch(`/dashboard/summary?period=${currentPeriod}`).then(r => r.json()),
            fetch(`/dashboard/category-wise?period=${currentPeriod}`).then(r => r.json()),
        ]).then(([summary, category]) => {
            updateSummaryCards(summary.data);
            updateCategoryChart(category.data);

        })
    }

    function updateSummaryCards(data) {
        // Elements to update
        const elements = {
            totalCount: document.getElementById('totalCount'),
            totalExpenses: document.getElementById('totalExpenses'),
            averageExpense: document.getElementById('averageExpense'),
            highestExpense: document.getElementById('highestExpense'),
            lowestExpense: document.getElementById('lowestExpense'),
        };
        const dateRangeElements = document.querySelectorAll('.dateRange') // or getElementById

        // Update date range immediately (not animated)

        dateRangeElements.forEach(element => {
            element.textContent = data.date_range;
        });

        // Store target values
        const targetValues = {
            totalCount: data.total_count,
            totalExpenses: data.total_expenses,
            averageExpense: data.average_expense,
            highestExpense: data.highest_expense,
            lowestExpense: data.lowest_expense
        };

        // Reset all counters to 0 for animation
        Object.keys(elements).forEach(key => {
            if (key !== 'dateRange' && elements[key]) {
                elements[key].textContent = '0';
                // Clear any existing counterUp data
                $(elements[key]).removeData('counterup');
                $(elements[key]).removeData('waypoint');
            }
        });

        // Animate all counters after a short delay
        setTimeout(() => {
            Object.keys(elements).forEach(key => {
                if (key !== 'dateRange' && elements[key] && targetValues[key] !== undefined) {
                    // Set final value
                    elements[key].textContent = targetValues[key];

                    // Initialize counterUp for each element
                    $(elements[key]).counterUp({
                        delay: 10,
                        time: 1000
                    });
                }
            });
        }, 50);
    }


    function updateCategoryChart(data) {
        console.log('Updating category chart with:', data);

        const container = document.getElementById('categoryChart');

        if (!container) {
            console.error('Container element #categoryChart not found');
            return;
        }

        // Check if ECharts is available
        if (typeof echarts === 'undefined') {
            console.error('ECharts is not loaded');
            container.innerHTML = '<div class="alert alert-danger">Chart library not loaded</div>';
            return;
        }

        // Initialize or get ECharts instance
        let chart = echarts.getInstanceByDom(container);
        if (!chart) {
            chart = echarts.init(container);
        }

        // Prepare ECharts options
        const option = {
            tooltip: {
                trigger: 'item',
                formatter: function(params) {
                    const value = params.value || 0;
                    const percentage = params.percent || 0;
                    return `${params.name}<br/>Rs. ${value.toFixed(2)} (${percentage}%)`;
                }
            },
            legend: {
                orient: 'vertical',
                right: 10,
                top: 'center',
                textStyle: {
                    fontSize: 12
                }
            },
            series: [{
                name: 'Category Distribution',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['40%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 5,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '16',
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: false
                },
                data: data.labels.map((label, index) => ({
                    name: label,
                    value: data.datasets[0].data[index],
                    itemStyle: {
                        color: data.datasets[0].backgroundColor[index]
                    }
                }))
            }]
        };

        // Set option and resize
        chart.setOption(option);
        chart.resize();

        // Store chart reference
        charts.category = chart;

        // Update details
        // updateCategoryDetails(data);
    }

    function updateCategoryDetails(data) {
        const detailsContainer = document.getElementById('categoryDetailsContainer') ||
            document.getElementById('categoryDetails');

        if (!detailsContainer) {
            console.warn('Details container not found');
            return;
        }

        if (!data.details || !Array.isArray(data.details) || data.details.length === 0) {
            detailsContainer.innerHTML = '<div class="alert alert-info">No category data available</div>';
            return;
        }

        let detailsHtml = `
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">%</th>
                        <th class="text-center">Count</th>
                    </tr>
                </thead>
                <tbody>
    `;

        data.details.forEach(item => {
            const colorIndex = data.details.indexOf(item) % 12;
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
                '#6A4C93', '#F15BB5', '#00BBF9', '#00F5D4'
            ];

            detailsHtml += `
            <tr>
                <td>
                    <span class="badge" style="background-color: ${colors[colorIndex]}">&nbsp;&nbsp;</span>
                    ${item.category}
                </td>
                <td class="text-end">Rs. ${item.amount?.toFixed(2) || '0.00'}</td>
                <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="progress flex-grow-1 me-2" style="height: 4px; max-width: 60px;">
                            <div class="progress-bar" style="width: ${item.percentage || 0}%"></div>
                        </div>
                        ${item.percentage?.toFixed(1) || '0'}%
                    </div>
                </td>
                <td class="text-center">${item.count || 0}</td>
            </tr>
        `;
        });

        detailsHtml += `
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-end"><strong>Rs. ${data.total?.toFixed(2) || '0.00'}</strong></td>
                        <td class="text-end"><strong>100%</strong></td>
                        <td class="text-center"><strong>${data.details.reduce((sum, item) => sum + (item.count || 0), 0)}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

        detailsContainer.innerHTML = detailsHtml;
    }

    function getCategoryColor(categoryName, index) {
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
            '#6A4C93', '#F15BB5', '#00BBF9', '#00F5D4'
        ];
        return colors[index % colors.length];
    }
</script>

@endpush