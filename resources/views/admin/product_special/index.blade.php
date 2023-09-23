@extends('admin.index')
@section('content')
    @if(session()->has('message') && session()->get('status') == 'success')
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session()->get('message') }}
        </div>
    @elseif(session()->has('message') && session()->get('status') == 'fail')
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session()->get('message') }}
        </div>
    @endif

    <form action="{{ route(ADMIN_PRODUCT_SPECIAL_STORE) }}" method="post" id="add-type-form">
    @csrf
        <div class="row mb-3">
            <div class="col-lg-6">
                <div>
                    <h1 class="h3 d-inline align-middle">Sản phẩm đặc biệt</h1>
                    <a class="badge bg-dark text-white ms-2">
                        Danh sách sản phẩm đặc biệt
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <button class="btn btn-primary ml-3" type="submit">Thêm mới</button>
                <a class="btn btn-info ml-3" href="{{ route(ADMIN_DASHBOARD) }}">Quay lại</a>
            </div>
        </div>

        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-6">
                    <table class="table table-bordered table-hover table-striped" id="users-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Loại sản phẩm</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>
                                        @if($value->status == 1)
                                            <p class="text-bg-success text-center">Hiện thị</p>
                                        @else
                                            <p class="text-bg-danger text-center">Ẩn</p>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route(ADMIN_PRODUCT_SPECIAL_EDIT, $value->id) }}" class="btn btn-primary">Thay đổi</a>
                                        <a href="{{ route(ADMIN_PRODUCT_SPECIAL_DELETE, $value->id) }}"
                                           onclick="return confirm('Bạn có chắc chắn muốn xoá không?');"
                                           class="btn btn-danger">Xoá</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    @include('admin.product_type.form')
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('#add-type-form').submit(function (e) {
                let form = this;
                e.preventDefault();

                $.ajax({
                    type: "post",
                    method:"POST",
                    dataType: 'json',
                    url: "{{ route(ADMIN_PRODUCT_SPECIAL_STORE) }}",
                    data: new FormData(form),
                    processData: false,
                    contentType:false,
                    success: function (e) {
                        if (e.status == 1) {
                            let stt = Number($('tbody tr').last().children('td').first().text()) + 1;
                            let html = '<tr>'+
                                '<td>'+ stt +'</td>'+
                                '<td>'+ e.data.name + '</td>'+
                                '<td><p class="text-bg-success">Hiển thị</p></td>'+
                                '<td>'+
                                '<a href="http://127.0.0.1:8000/admin/product/type/'+e.data.id+'" class="btn btn-primary">Thay đổi</a>'+
                                '<a href="http://127.0.0.1:8000/admin/product/type/'+e.data.id+'/delete" class="btn btn-danger" >Xoá</a>'+
                                '</td>'+
                                '</tr>'

                            $('tbody').append(html);
                            location.reload();
                        } else {
                            alert('Thêm thất bại');
                        }
                    }
                });
            });
        });
    </script>
@endsection
