@extends('layouts.app_master_admin')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Quản lý đơn hàng</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{  route('admin.transaction.index') }}"> Transaction</a></li>
            <li class="active"> List </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
    <form action="{{  route('admin.transaction.dateTransaction') }}" method="post">
    @csrf
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1">Từ ngày: </label>
                <input type="date" class="form-control" value="{{date('Y-m-d', strtotime($day1 ?? date('Y-m-d')))}}" id="datatime1" name="datatime1">
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
            <div style="height: 25px; margin: 25px;" >
            <button type="submit" class="btn btn-primary align-self-center">Thực hiện truy vấn</button>
            </div>
        </div>
    </form>
    <div class="col-md-3 mt-3" >
        <form action="{{  route('admin.transaction.exportTransaction') }}" method="post">
            @csrf
            <input type="date" class="form-control" id="datatime1a" name="datatime1a">
            <input type="date" class="form-control" id="datatime2a" name="datatime2a">
            <button type="submit" class="btn btn-primary align-self-center">Xuất Báo Cáo</button>
        </form>
            <!-- <div style="height: 50px; margin: 25px;" >
            <a href="{{  route('admin.inventory.exportInven') }}" class="btn btn-primary align-self-center">Xuất Báo Cáo</a>
            </div> -->
        </div>
    </div>
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    <form class="form-inline">
                        <input type="text" class="form-control"  value="{{ Request::get('id') }}" name="id" placeholder="ID">
                        <input type="text" class="form-control"value="{{ Request::get('email') }}" name="email" placeholder="Email ...">
                       <!-- <select name="type" class="form-control" >
                            <option value="0">Phân loại khách</option>
                            <option value="1" {{ Request::get('type') == 1 ? "selected='selected'" : "" }}>Thành viên</option>
                            <option value="2" {{ Request::get('type') == 2 ? "selected='selected'" : "" }}>Khách</option>
                        </select>-->
                        <select name="status" class="form-control">
                            <option value="">Trạng thái</option>
                            <option value="1" {{ Request::get('status') == 1 ? "selected='selected'" : "" }}>Tiếp nhận</option>
                            <option value="2" {{ Request::get('status') == 2 ? "selected='selected'" : "" }}>Đang vận chuyển</option>
                            <option value="3" {{ Request::get('status') == 3 ? "selected='selected'" : "" }}>Đã bàn giao</option>
                            <option value="-1" {{ Request::get('status') == -1 ? "selected='selected'" : "" }}>Huỷ bỏ</option>
                        </select>
                        <!--<button type="submit" class="btn btn-success" ><i class="fa fa-search"></i> Search</button>
                        <button type="submit" name="export" value="true" class="btn btn-info">
                            <i class="fa fa-save"></i> Export
                        </button>-->
                    </form>
                </div>
                <div class="box-body">
                   <div class="col-md-12">
                        <table class="table" id="myTable">
                            <tbody>
                                <tr>
                                    <th style="width: 10px">STT</th>
                                    <th style="width: 10px">ID</th>
                                    <th style="width: 30%">Info</th>
                                    <th>Money</th>
                                  <!--  <th>Account</th>-->
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Note</td>
                                    <th>Action</th>
                                </tr>
                                @if (isset($transactions))
                                    @foreach($transactions as $key => $transaction)
                                        <tr>
                                            <td>{{ (($transactions->currentPage() - 1) * $transactions->perPage()) + ( $key + 1)  }}</td>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                <ul>
                                                    <li>Name: {{ $transaction->tst_name }}</li>
                                                    <li>Email: {{ $transaction->tst_email }}</li>
                                                    <li>Phone: {{ $transaction->tst_phone }}</li>
                                                    <li>Addres: {{ $transaction->tst_address }}</li>
                                                </ul>
                                            </td>
                                            <td>{{ number_format($transaction->tst_total_money,0,',','.') }} đ</td>
                                           <!-- <td>
                                                @if ($transaction->tst_user_id)
                                                    <span class="label label-success">Thành viên</span>
                                                @else
                                                    <span class="label label-default">Khách</span>
                                                @endif
                                            </td>-->
                                            <td>
                                                <span class="label label-{{ $transaction->getStatus($transaction->tst_status)['class'] }}">
                                                    {{ $transaction->getStatus($transaction->tst_status)['name'] }}
                                                </span>
                                            </td>
                                            <td>{{  $transaction->created_at }}</td>
                                            <td>{{$transaction->tst_note}}</td>
                                            <td>
                                                <a data-id="{{  $transaction->id }}" href="{{ route('ajax.admin.transaction.detail', $transaction->id) }}" class="btn btn-xs btn-info js-preview-transaction"><i class="fa fa-eye"></i> View</a>
                                                @if ($transaction->tst_status != 3 && $transaction->tst_status != -1)
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-success btn-xs">Action</button>
                                                        <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">

                                                                <li>
                                                                    <a href="{{  route('admin.transaction.delete', $transaction->id) }}" class="js-delete-confirm"><i class="fa fa-trash"></i> Delete</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                                <li>
                                                                    <a href="{{ route('admin.action.transaction',['process', $transaction->id]) }}" ><i class="fa fa-tint"></i> Đang bàn giao</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('admin.action.transaction',['success', $transaction->id]) }}" ><i class="fa fa-check-circle"></i> Đã bàn giao</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('admin.action.transaction',['cancel', $transaction->id]) }}" ><i class="fa fa-ban"></i> Huỷ</a>
                                                                </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {!! $transactions->appends($query)->links() !!}
                </div>
                <!-- /.box-footer-->
            </div>
        </div>
            <!-- /.box -->
    </section>

    <div class="modal fade fade" id="modal-preview-transaction">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"> Chi tiết đơn hàng [ ID Đơn Hàng : <b id="idTransaction"></b>]</h4>
                </div>
                <div class="modal-body">
                <div class="box">
                </div>
                    <div class="content">
                         
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.content -->
    <script>
function myFunction() {
  var input1,input2,input3,input4, filter1, filter2,filter3,filter4,table, tr, td, i, txtValue;
  input1 = document.getElementById("myInputID");
  input2 = document.getElementById("myInputEmail");
  var e = document.getElementById("myInputPhanLoai");
  input3 = e.options[e.selectedIndex].text;

  var e4 = document.getElementById("myInputTrangthai");
  input4 = e4.options[e4.selectedIndex].text;
  filter1 = input1.value.toUpperCase();
  filter2 = input2.value.toUpperCase();
  filter3 = input3.toUpperCase();
  filter4 = input4.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td1 = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[4];
    td4 = tr[i].getElementsByTagName("td")[5];
    if (td1) {
      txtValue1 = td1.textContent || td1.innerText;
      txtValue2 = td2.textContent || td2.innerText;
      txtValue3 = td3.textContent || td3.innerText;
      txtValue4 = td4.textContent || td4.innerText;
        console.log(txtValue2)

      if (txtValue1.toUpperCase().indexOf(filter1) > -1 &&
            txtValue2.toUpperCase().indexOf(filter2) > -1 &&
            txtValue3.toUpperCase().indexOf(filter3) > -1 &&
            txtValue4.toUpperCase().indexOf(filter4) > -1
      ) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>


@stop
