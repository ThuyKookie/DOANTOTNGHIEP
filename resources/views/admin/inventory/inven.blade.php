@extends('layouts.app_master_admin')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Quản lý tồn kho</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{  route('admin.product.index') }}"> Product</a></li>
            <li class="active"> List </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
     <!--   <div class="row">
            <form action="selectDataTimeInline" method="post">
            @csrf
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Từ ngày: </label>
                        <input type="date" class="form-control" id="datatime1" name="datatime1">
                        <small id="emailHelp" class="form-text text-muted">Hãy chọn ngày bắt đầu</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Đến ngày: </label>
                        <input type="date" class="form-control" id="datatime2" name="datatime2">
                        <small id="emailHelp" class="form-text text-muted">Hãy chọn ngày kết thúc</small>
                    </div>
                </div>
                <div class="col-md-3 mt-3" >
                    <div style="height: 50px; margin: 25px;" >
                    <button type="submit" class="btn btn-primary align-self-center">Thực Hiện Truy Vấn</button>
                    </div>
                </div>
            </form>
        <div class="col-md-3 mt-3" >
        <form action="{{  route('admin.inventory.exportInven') }}" method="post">
            @csrf
            <input type="date" class="form-control" id="datatime1a" name="datatime1a">
            <input type="date" class="form-control" id="datatime2a" name="datatime2a">
            <button type="submit" class="btn btn-primary align-self-center">Xuất Báo Cáo</button>
        </form>
            <div style="height: 50px; margin: 25px;" >
            <a href="{{  route('admin.inventory.exportInven') }}" class="btn btn-primary align-self-center">Xuất Báo Cáo</a>
            </div>
        </div>
    </div>-->
        <div class="box">
            <div class="box-header with-border">
               <div class="box-title">
                    <form class="form-inline">
                        <input type="text" class="form-control" value="{{ Request::get('id') }}" name="id" placeholder="ID">
                        <input type="text" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name ...">
                        <select name="category" class="form-control" >
                            <option value="0">Danh mục</option>
                            @foreach($categories as $item)
                                <option value="{{ $item->id }}" {{ Request::get('category') == $item->id ? "selected='selected'" : "" }}>{{  $item->c_name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
{{--                        <button type="submit" name="export" value="true" class="btn btn-info">--}}
{{--                            <i class="fa fa-save"></i> Export--}}
{{--                        </button>--}}
                    </form>
                </div>
   
                <!--<h2>Lọc với số lượng</h2>

                <input type="number" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name">-->
                <div class="box-body">
                   <div class="col-md-12">
                        <table class="table"  id="myTable">
                            <tbody>
                                <tr>
                                    <th style="width: 10px">STT</th>
                                    <th style="width: 10px">ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Ảnh</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                   <!-- <th>Time</th>-->
                                    <th>Action</th>
                                </tr>
                                
                            </tbody>
                            @if (isset($products))
                                    @foreach($products as $key => $product)
                                    @php
                                        $ok = "bg-light";
                                        if($product->pro_number == 0){
                                            $ok = " bg-info";
                                        }
                                        if($product->pro_number > 0 && $product->pro_number <= 5){
                                            $ok = "bg-danger ";
                                        }
                                        if($product->pro_number > 5 && $product->pro_number <= 10){
                                            $ok = "bg-warning";
                                        } 
                                    @endphp
                                        <tr class="{{$ok}}">
                                            <td>{{ (($products->currentPage() - 1) * $products->perPage()) + ( $key + 1)  }}</td>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->pro_name }}</td>
                                            <td>
                                                <span class="label label-success">{{ $product->category->c_name ?? "[N\A]" }}</span>
                                            </td>
                                            <td>
                                                <img src="{{ pare_url_file($product->pro_avatar) }}" style="width: 80px;height: 100px">
                                            </td>
                                            <td>
                                                @if ($product->pro_sale)
                                                    <span style="text-decoration: line-through;">{{ number_format($product->pro_price,0,',','.') }} vnđ</span><br>
                                                    @php 
                                                        $price = ((100 - $product->pro_sale) * $product->pro_price)  /  100 ;
                                                    @endphp
                                                    <span>{{ number_format($price,0,',','.') }} vnđ</span>
                                                @else 
                                                    {{ number_format($product->pro_price,0,',','.') }} vnđ
                                                @endif
                                                
                                            </td>
                                           <td>
                                           {{ $product->pro_number }}
                                           </td>
                                            <!--<td>{{  $product->created_at }}</td>-->
                                            <td>
                                                <a href="{{ route('admin.product.update', $product->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                                                <a href="{{  route('admin.product.delete', $product->id) }}" class="btn btn-xs btn-danger js-delete-confirm"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {!! $products->appends($query)->links() !!}
                </div>
                <!-- /.box-footer-->
            </div>
            <!-- /.box -->
        </div>
    </section>
    <!-- /.content -->
    <script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  if (filter === '0') {
    console.log(filter)
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[6];
        if (td) {
        txtValue = td.textContent || td.innerText;
        console.log(txtValue.toUpperCase())
        if (txtValue.toUpperCase() == 0) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }  
    }
  }else{
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[6];
        if (td) {
        txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }  
    }
  }
}
</script>
@stop