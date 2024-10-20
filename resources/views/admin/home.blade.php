@extends('layouts.admin')

@section('title')
    <title>Dashboard</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-6">
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
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #a1c4fd, #c2e9fb);">
                                            <div class="inner">
                                                <h4>Rp {{ number_format($totals, 0, ',', '.') }}</h4>
                                                <p>Pendapatan</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #d4fc79, #96e6a1);">
                                            <div class="inner">
                                                <h4>{{ $customers->count() }}</h4>
                                                <p>Konsumen</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-person-add"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #fbc2eb, #a6c1ee);">
                                            <div class="inner">
                                                <h4>{{ $sellers->count() }}</h4>
                                                <p>Penjual</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-person"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);">
                                            <div class="inner">
                                                <h4>{{ $categories->count() }}</h4>
                                                <p>Kategori Produk</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-pricetag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
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
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #ffafbd, #ffc3a0);">
                                            <div class="inner">
                                                <h4>{{ $datas[0]->newOrder ?? 0 }}</h4>
                                                <p>Pesanan Baru</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #a1ffce, #faffd1);">
                                            <div class="inner">
                                                <h4>{{ $datas[0]->processOrder }}</h4>
                                                <p>Pesanan Diproses</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-load-c"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #c2e59c, #64b3f4);">
                                            <div class="inner">
                                                <h4>{{ $datas[0]->shipping }}</h4>
                                                <p>Pesanan Dikirim</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-paper-airplane"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #89f7fe, #66a6ff);">
                                            <div class="inner">
                                                <h4>{{ $datas[0]->arriveOrder }}</h4>
                                                <p>Pesanan Sampai</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-ios-home"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box" style="background: linear-gradient(135deg, #fddb92, #d1fdff);">
                                            <div class="inner">
                                                <h4>{{ $datas[0]->completeOrder }}</h4>
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
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        
                    </div>
                    <div class="col-lg-4">
                        
                    </div>
                    <div class="col-lg-4">
                        
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card loader-area">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Top Produk Terlaris</h3>
                                @if($dataTopSelling->count() < 5)
                                    <a class="float-right" href="#" data-toggle="modal" data-target="#allProductsModal">Lihat Semua</a>
                                @endif
                            </div>
                            <div class="card-body">
                                @forelse($dataTopSelling->take(5) as $row)
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
                                            @if($row['stok'] > 0)
                                                <span class="font-weight-bold mb-1 text-success">Stok Tersedia</span>
                                            @else
                                                <span class="font-weight-bold mb-1 text-danger">Stok Habis</span>
                                            @endif
                                            <small>{{ 'Stok Tersisa : ' . $row['stok'] }}</small>
                                        </div>
                                    </div>
                                @empty    
                                    <h4>Tidak ada produk</h4>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card loader-area">
                            <div class="card-header">
                                <div class="align-items-center">
                                    <h3 class="card-title font-weight-bold">Penarikan Dana</h3>
                                    @if($withdrawals->count() >= 0)
                                        <a href="javascript:void(0);" class="float-right see-all-withdrawals">Lihat semua</a>
                                    @endif
                                </div> 
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless">
                                        <tbody>
                                            @forelse($withdrawals->take(5) as $row)
                                                <tr>
                                                    <td>
                                                        <span>{{ $loop->iteration . '.' }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ \Carbon\Carbon::parse($row->updated_at)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $row->seller->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ 'Rp ' . number_format($row->amount, 0, ',', '.') }}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        @if($row->status == 'disetujui')
                                                            <span class="badge badge-success text-capitalize">{{ $row->status }}</span>
                                                        @elseif($row->status == 'menunggu')
                                                            <span class="badge badge-secondary text-capitalize">{{ $row->status }}</span>
                                                        @else
                                                            <span class="badge badge-danger text-capitalize">{{ $row->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td><span class="font-weight-bold">Tidak ada data</span></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- @forelse($withdrawals as $row)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span>{{ $loop->iteration . '. ' }}</span>
                                            <span>{{ $row->seller->name }}</span>
                                        </div>
                                        <div>
                                            <span class="mr-1">{{ 'Rp ' . number_format($row->amount, 0, ',', '.') }}</span>
                                            <span class="badge badge-success text-capitalize">{{ $row->status }}</span>
                                            <span>{{ \Carbon\Carbon::parse($row->updated_at)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <span class="font-weight-bold">Tidak ada data</span>
                                @endforelse --}}
                            </div>
                        </div>
                        <div class="card loader-area">
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

    <!-- Modal Return Form -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 55%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalModalLabel">Daftar Penarikan Dana</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th class="text-right">Nominal</th>
                                    <th class="text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $row)
                                <tr>
                                    <td>
                                        <span>{{ $loop->iteration . '.' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ \Carbon\Carbon::parse($row->updated_at)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $row->seller->name }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span>{{ 'Rp ' . number_format($row->amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-right">
                                        @if($row->status == 'disetujui')
                                            <span class="badge badge-success text-capitalize">{{ $row->status }}</span>
                                        @elseif($row->status == 'menunggu')    
                                            <span class="badge badge-secondary text-capitalize">{{ $row->status }}</span>
                                        @else
                                            <span class="badge badge-danger text-capitalize">{{ $row->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
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

            // withdrawal modal
            $('.see-all-withdrawals').on('click', function(e){
                e.preventDefault();
                $('#withdrawalModal').modal('show');
            });

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