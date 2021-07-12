
<table class="table table-condensed">
    <b>Nhà sách Nga Tú</b>
    <p>Địa chỉ: 121 Ngô Quyền - P.Ngô Quyền - TP.Vĩnh yên - Vĩnh Phúc</p>
                    <p>Điện thoại: 0919.996.386 - 0913.555.368</p>
                    <p>Khách hàng: <b>{{$orders[0]->tst_name }}</b></p>
                    <p>SĐT: <b>{{$orders[0]->tst_phone }}</b></p>
                    <p>Địa chỉ: <b>{{ $orders[0]->tst_address }}</b></p>
                    <p>Ghi chú: <b>{{$orders[0]->tst_note}}</b></p>
                    <p>Ngày mua hàng: {{  $orders[0]->created_at }}</p>
    <tbody>
        <tr>
            <th style="width: 10px">STT</th>
            <th>Tên sản phẩm</th>
            <th>Ảnh</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
        </tr>
        @php $stt = 0;  @endphp
        @foreach($orders as $item)
        @php $stt += 1;  @endphp
            <tr>
                <!-- <td>#{{ $item->id }}.</td> -->
                <td>{{ $stt }}</td>
                <td><a href="">{{ $item->product->pro_name ?? "[N\A]" }}</a></td>
                <td>
                    <img alt="" style="width: 60px;height: 80px" src="{{ pare_url_file($item->product->pro_avatar ?? "") }}" class="lazyload">
                </td>
                <td>{{ number_format($item->od_price,0,',','.') }} đ</td>
                <td>{{ $item->od_qty }}</td>
                <td>{{ number_format($item->od_price * $item->od_qty,0,',','.') }} đ</td>
               <!-- <td>
                    <a href="{{ route('ajax_admin.transaction.order_item', $item->id) }}" class="btn btn-xs btn-danger js-delete-order-item">Delete</a>
                </td>-->
            </tr>
        @endforeach
    </tbody>
</table>