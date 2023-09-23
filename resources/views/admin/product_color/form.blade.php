<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Màu sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Tên màu</label>
                    <input type="text" name="color" value="{{ isset($id) ? $data->name : '' }}"
                           placeholder="Nhập tên màu" required/>
                </div>
                <div class="form-group">
                    <label for="">Mã màu</label>
                    <input type="color" placeholder="Chọn màu sản phẩm"
                           value="{{ isset($id) ? $data->color_code : '' }}" name="color-code" style="width: 200px"
                           required/>
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
