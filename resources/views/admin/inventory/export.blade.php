@extends('layouts.app_master_admin')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Xuất kho</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="">  Xuất kho </a></li>
        </ol>
    </section>

    <div style="margin: 20px;">
    <div class="row">
    <form action="selectDataTimeExport" method="post">
    @csrf
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1">Từ ngày: </label>
                <input type="date" class="form-control" value="{{date('Y-m-d', strtotime($day1 ?? date('Y-m-d')))}}"id="datatime1" name="datatime1">
                <small id="emailHelp" class="form-text text-muted">Hãy chọn ngày bắt đầu</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1">Đến ngày: </label>
                <input type="date" class="form-control" value="{{ date('Y-m-d', strtotime($day2 ?? date('Y-m-d')))}}" id="datatime2" name="datatime2">
                <small id="emailHelp" class="form-text text-muted">Hãy chọn ngày kết thúc</small>
            </div>
        </div>
        <div class="col-md-3 mt-3" >
            <div style="height: 50px; margin: 25px;" >
            <button type="submit" class="btn btn-primary align-self-center">Thực Hiện Truy Vấn</button>
            </div>
        </div>
    </form>
    </div>
	</div>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
{{--                <div class="box-header">--}}
{{--                    <div class="pull-left">--}}
{{--                        <form action="" class="form-inline">--}}
{{--                            <input type="text" class="form-control" autocomplete="off" name="time" placeholder="Thời gian ..." value="{{ Request::get('time') }}">--}}
{{--                            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Tìm kiếm</button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
                @php $sum = 0 ; @endphp
                <div class="box-body">
                    <div class="col-md-12">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Tổng tiền</th>
                                <th>Ngày mua</th>
                            </tr>

                            </tbody>
                            @if (isset($inventoryExport))
                                @foreach($inventoryExport as $item)
                                    {{--									{{ dd($item) }}--}}
                                    <tr>
                                        <td>{{ $item->od_product_id }}</td>
                                        <td><a href="">{{ $item->product->pro_name ?? "[N\A]" }}</a></td>
                                        <td>{{ $item->od_qty }}</td>
                                        <td>{{ number_format($item->od_price * $item->od_qty,0,',','.') }} VNĐ</td>
                                        <td>{{ $item->created_at  }}</td>
                                    </tr>
                                    @php
                                        $sum += $item->od_price;
                                    @endphp
                                @endforeach
                            @endif
                        </table>
                    </div>
                    <p> Tổng tiền <b>{{ number_format($sum,0,',','.') }} VNĐ</b></p>
                </div>
            </div>
            <!-- /.box -->
            <div class="box-footer">
                {!! $inventoryExport->appends($query ?? [])->links() !!}
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('admin/bower_components/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/bower_components/daterangepicker/daterangepicker.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/bower_components/daterangepicker/daterangepicker.css') }}" />
    <script type="text/javascript">
        $(function(){
            $('input[name="time"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                } ,
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                    '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('input[name="time"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('input[name="time"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        })
    </script>
@stop
