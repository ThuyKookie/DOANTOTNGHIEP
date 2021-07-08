
<table class="table table-condensed">
    <tbody>
        <tr>
            <th style="width: 10px">Stt</th>
            <th>Name</th>
            <th>Avatar</th>
            <th>Price</th>
            <th>Number</th>
            <th>Total</th>
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