@extends('layouts.seller')

@section('title')
    <title>Dashboard</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard {{ auth()->guard('seller')->user()->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <!-- Small boxes (Stat box) -->
                    <div class="col-lg-12">
                        <div class="card">
                            {{-- <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-store mr-1"></i> Aktivitas Penjualan</h3>
                            </div> --}}
                            <div class="card-body loader-area">
                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <!-- small box -->
                                        <div class="small-box" style="background: linear-gradient(135deg, #a1c4fd, #c2e9fb);">
                                            <div class="inner">
                                                <h4>Rp {{ number_format($totalOmset, 0, ',', '.') }}</h4>
                                                <p>Keseluruhan Omset</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #ff9a9e, #fecfef);">
                                            <div class="inner">
                                                <h4>{{ $products->count() }}</h4>
                                                <p>Produk</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #ffafbd, #ffc3a0);">
                                            <div class="inner">
                                                <h4>{{ $detailOrder[0]->newOrder }}</h4>
                                                <p>Pesanan Baru</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #a1ffce, #faffd1);">
                                            <div class="inner">
                                                <h4>{{ $detailOrder[0]->processOrder }}</h4>
                                                <p>Pesanan Diproses</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-load-c"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #c2e59c, #64b3f4);">
                                            <div class="inner">
                                                <h4>{{ $detailOrder[0]->shipping }}</h4>
                                                <p>Pesanan Dikirim</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-paper-airplane"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #89f7fe, #66a6ff);">
                                            <div class="inner">
                                                <h4>{{ $detailOrder[0]->arriveOrder }}</h4>
                                                <p>Pesanan Sampai</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-ios-home"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #fddb92, #d1fdff);">
                                            <div class="inner">
                                                <h4>{{ $detailOrder[0]->completeOrder }}</h4>
                                                <p>Pesanan Selesai</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-checkmark-circled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card loader-area">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Ringkasan Penjualan</h3>
                            </div>
                            <div class="card-body">
                                <div id="sales-chart" style="width:100%; height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card loader-area">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Top Produk Terlaris</h3>
                                @if($dataTopSelling->count() > 5)
                                    <a class="float-right" href="#" data-toggle="modal" data-target="#allProductsModal">Lihat Semua</a>
                                @endif
                            </div>
                            <div class="card-body">
                                @forelse($dataTopSelling->take(5) as $row)
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div style="height: 80px; width: 80px; display: block; border: 1px solid transparent; border-radius: 4px;">
                                                <img style="display: block; object-fit: contain; width: 100%; height: 100%; border-radius: 4px;" src="{{ asset('/products/' . $row['image']) }}" alt="{{ $row['name'] }}">
                                            </div>
                                            <div class="d-flex flex-column ml-3">
                                                <span class="font-weight-bold mb-1">{{ $row['name'] }}</span>
                                                <span>{{ $row['sales'] . ' penjualan' }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            @if($row['stok'] > 100)
                                                <span class="font-weight-bold mb-1 text-success">Stok Tersedia</span>
                                            @elseif($row['stok'] > 0 && $row['stok'] < 50)
                                                <span class="font-weight-bold mb-1 text-warning">Stok Mau Habis</span>
                                            @else
                                                <span class="font-weight-bold mb-1 text-danger">Stok Habis</span>
                                            @endif
                                            <small style="align-self: flex-end;">{{ 'Stok Tersisa : ' . $row['stok'] }}</small>
                                        </div>
                                    </div>
                                @empty    
                                    <h4>Tidak ada produk</h4>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card loader-area">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Daftar Pesanan Baru</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="newsOrder" class="table table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th style="padding: 10px 10px;">Invoice</th>
                                                <th style="padding: 10px 10px;">Nama Pelanggan</th>
                                                <th style="padding: 10px 10px;">Produk</th>
                                                <th style="padding: 10px 10px;">Total</th>
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                       <div class="card">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Kategori Produk</h3>
                            </div>
                            <div class="card-body loader-area">
                                <div id="top-selling-product" style="width:100%; height:400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="allProductsModal" tabindex="-1" aria-labelledby="allProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allProductsModalLabel">Semua Produk Terlaris</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach($dataTopSelling as $row)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div style="height: 80px; width: 80px; display: block; border: 1px solid transparent; border-radius: 5px;">
                                    <img style="display: block; object-fit: contain; width: 100%; height: 100%; border-radius: 5px;" src="{{ asset('/products/' . $row['image']) }}" alt="{{ $row['name'] }}">
                                </div>
                                <div class="d-flex flex-column ml-3">
                                    <span class="font-weight-bold mb-1">{{ $row['name'] }}</span>
                                    <span>{{ $row['sales'] . ' penjualan' }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if($row['stok'] >= 100)
                                    <span class="font-weight-bold mb-1 text-success">Stok Tersedia</span>
                                @elseif($row['stok'] <= 49)
                                    <span class="font-weight-bold mb-1 text-warning">Stok Mau Habis</span>
                                @else
                                    <span class="font-weight-bold mb-1 text-danger">Stok Habis</span>
                                @endif
                                <small>{{ 'Stok Tersisa : ' . $row['stok'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('seller.datatablesIndex') }}" id="getIndex"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>

        // highcharts
        document.addEventListener('DOMContentLoaded', function () {
            // Prepare the data
            var monthlySales = @json($monthlySales);

            // Convert monthlySales to Highcharts format
            var categories = Object.keys(monthlySales);
            var data = Object.values(monthlySales);
            console.log(monthlySales);
            console.log(categories);
            console.log(data);

            // Set month names in Bahasa Indonesia
            Highcharts.setOptions({
                lang: {
                    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des']
                }
            });

            // Data penjualan
            Highcharts.chart('sales-chart', {
                chart: {
                    type: 'line', // Change chart type to line
                    backgroundColor: '#fff',
                    style: {
                        fontFamily: 'Arial'
                    }
                },
                title: {
                    text: 'Ringkasan Penjualan Bulanan',
                    style: {
                        color: '#333',
                        fontSize: '18px'
                    }
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Bulan',
                        style: {
                            color: '#333'
                        }
                    },
                    labels: {
                        style: {
                            color: '#666'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Penjualan (Rp)',
                        style: {
                            color: '#333'
                        }
                    },
                    labels: {
                        style: {
                            color: '#666'
                        }
                    },
                    gridLineColor: '#e6e6e6'
                },
                legend: {
                    enabled: true,
                    itemStyle: {
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.85)',
                    borderColor: '#ccc',
                    shadow: false,
                    style: {
                        color: '#333'
                    },
                    formatter: function() {
                        return 'Penjualan: <b>Rp ' + Highcharts.numberFormat(this.y, 0, ',', '.') + '</b>';
                    }
                },
                series: [{
                    name: 'Penjualan (Rp)',
                    data: data,
                    color: '#7cb5ec',
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#333',
                        style: {
                            fontSize: '12px'
                        },
                        formatter: function() {
                            return 'Rp ' + Highcharts.numberFormat(this.y, 0, ',', '.');
                        }
                    }
                }],
                plotOptions: {
                    line: { // Updated for line chart
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: true
                    }
                },
                credits: {
                    enabled: false
                },
                accessibility: {
                    enabled: false
                }
            });

            // data produk terlaris
            Highcharts.chart('top-selling-product', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Top Kategori Produk'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    name: 'Total',
                    colorByPoint: true,
                    data: {!! json_encode($chartDataTopSelling) !!}
                }]
            });
        });
        
        $(document).ready(function(){

            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Invoice...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                },
                initComplete: function() {
                    var $searchInput = $('#newsOrder_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Invoice...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#newsOrder_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#newsOrder_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#getIndex').attr('href');
            var table = $('#newsOrder').DataTable({
                ajax: {
                    url: url,
                    beforeSend: function() {
                        $('.loader-area').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait'
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none'
                            } 
                        });
                    },
                    complete: function() {
                        $('.loader-area').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start;
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false},
                    {data: 'date', name: 'date'},
                    {data: 'invoice', name: 'invoice', orderable: false, searchable: false},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'product', name: 'product'},
                    {data: 'total', name: 'total'},
                    {data: 'status_display', name: 'status_display', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    {
                        targets: [6],
                        visible: true,
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

        });
    </script>
@endsection