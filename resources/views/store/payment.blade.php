@extends('store.index')
@section('content')
    @include('common.noti_message')
    <div class="container mt-4 py-2" style="background-color: #e9e9e9">
        <div class="row">
            <div class="col-12">
                <table class="table-bordered table-dark w-100">
                    <tr>
                        <th>#</th>
                        <th>Mã hóa đơn</th>
                        <th>Phương thức thanh toán</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Vận chuyển</th>
                        <th>Lịch sử</th>
                        <th>Thông tin</th>
                    </tr>
                    @foreach($payments as $key => $payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $payment->order_id }}</td>
                            <td>{{ $payment->payment_type }}</td>
                            <td>{{ number_format($payment->total, 0, '.', '.') }}</td>
                            <td>{{ $payment->status == 1 ? 'Thành công' : 'Thất bại' }}</td>
                            <td>
                                {{ $payment->delivery == 0 ? 'Đang giao' :
                                   ($payment->delivery == 1 ? 'Hoàn thành' : 'Huỷ đơn') }}
                            </td>
                            <td>{{ $payment->created_at }}</td>
                            <td>
                                <div class="btn btn-primary" data-toggle="modal" data-target="#detail" onclick="getinfo({{ $payment->order_id }})">Chi tiết</div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="detail" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Thông tin đơn hàng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table-bordered table-dark w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Dung lượng</th>
                                        <th>Màu</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                </thead>
                                <tbody id="info-modal">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function getinfo(data) {
            $('#info-modal').html('');
            $.ajax({
                type: "get",
                url: "/store/customer/get-bill-info",
                data: {
                    "id": data,
                },
                success: function success(e) {
                    window.total = e.data[0].total;
                    var html = '';
                    $.each(e.data, function (key, value) {
                        console.log(key);
                        html += '<tr>'+
                            '<th>'+ Number(key + 1) +'</th>'+
                            '<th>'+ value.product_name +'</th>'+
                            '<th>'+ value.memory +'</th>'+
                            '<th>'+ value.color +'</th>'+
                            '<th>'+ value.amount +'</th>'+
                            '<th>'+ value.price +'</th>'+
                            '<th>'+ value.product_total +'</th>'+
                            '</tr>'
                    });

                    //Add total
                    html += '<tr>'+
                            '<td colspan="6">Tổng</td>'+
                            '<td>'+ window.total +'</td>'+
                            '</tr>'

                    $('#info-modal').append(html);
                }
            });
        }
    </script>
@endsection
