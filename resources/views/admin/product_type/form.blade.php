<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Loại sản phẩm</h5>
            </div>
            <div class="card-body">
                <div>
                    <label for="">Tên loại sản phẩm</label>
                    <input type="text" class="form-control" placeholder="Nhập tên loại sản phẩm"
                           value="{{ isset($id) ? $data->name : '' }}" name="name" required/>
                </div>
                @if(isset($id))
                    <div>
                        <label>Trạng thái</label>
                        <input type="radio" name="status" value="1" {{ $data->status == 1 ? "checked" : "" }}>Hiển thị
                        <input type="radio" name="status" value="0" {{ $data->status == 0 ? "checked" : "" }}>Ẩn
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
