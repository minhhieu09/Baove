@extends('admin.index')
@section('content')
    <form action="{{ route(ADMIN_PRODUCT_COMPONENT_STORE, $id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
            <div class="col-lg-6">
                <div>
                    <h1 class="h3 d-inline align-middle">Tạo mới</h1>
                    <a class="badge bg-dark text-white ms-2">
                        Thêm vào sản phẩm
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <button class="btn btn-success" type="submit">Lưu</button>
                <a class="btn btn-primary ml-3" href="{{ route(ADMIN_PRODUCT_INDEX) }}">Quay lại</a>
            </div>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tên sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" value="{{ $product->name }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Loại sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <select class="form-select mb-3" name="type" disabled >
                                <option selected value="">Chọn loại sản phẩm</option>
                                @foreach($type as $row)
                                    <option value="{{ $row->id }}"
                                    @if(isset($id)) {{ $product->productType->id == $row->id ? 'selected' : '' }} @endif>{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3" id="listItem">
                    <div class="row mb-3">
                        <div class="col-2">
                            <label for="memory">Chọn dung lượng</label>
                            <select name="memory">
                                <option selected value="">Chọn bộ nhớ</option>
                                @foreach($memory as $key => $row)
                                    <option value="{{ $key }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="color">Chọn màu</label>
                            <br>
                            <select name="color">
                                <option selected value="">Chọn màu</option>
                                @foreach($color as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="amount">Số lượng</label>
                            <br>
                            <input type="number" name="amount" style="width: 100px">
                        </div>
                        <div class="col-2">
                            <label for="price">Đơn giá</label>
                            <br>
                            <input type="number" name="price" style="width: 100px">
                        </div>
                        <div class="col-3 group-img">
                            <label for="img">Chọn hình ảnh</label>
                            <input name="image" type="file" style="width: 85px"/>
                            <img src="#" class="imgPreview" style="width: 200px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>
        $(document).on('change', '.group-img input', function () {
            let $file = $(this);

            if ($file.prop('files') && $file.prop('files')[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    console.log($file);
                    $file.closest('.group-img').find('.imgPreview').attr('src', e.target.result);
                };

                reader.readAsDataURL($file.prop('files')[0]);
            }
        })
    </script>
@endsection
