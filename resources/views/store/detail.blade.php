@extends('index')
@section('content')
    @include('common.noti_message')
    <!-- Content -->
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">
                {{ Breadcrumbs::render('continent') }}
            </div>
        </div>

        <hr style="width: 100%; margin-top: 8px; height: 4px;">

        <div class="row mt-3" id="sanpham">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-5">
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @foreach($product->component as $key => $item)
                                    <li data-target="#carouselExampleIndicators" data-slide-to="{{ $key }}" {{ $key == 0 ? 'class="active"' : '' }}></li>
                                @endforeach
                            </ol>
                            <div class="carousel-inner">
                                @foreach($product->component as $key => $item)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('images/' . $item->image) }}" class="d-block w-100"
                                             alt="anh">
                                    </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: black"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: black"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div>
                            <form action="" method="get">
                                <input type="hidden" name="id" value="{{ $product->id }}">
                            <ul style="list-style: none;">
                                <li><h3>{{ $product->name }}</h3></li>
                                <li>
                                    <div style="display: flex">
                                        <div style="margin-right: 10px">
                                            <b>Màu sắc:</b>

                                        </div>
                                        @foreach($color as $row)
                                            <div style="width: 20px;height: 20px;border: 2px solid;border-radius: 100%;margin-right: 5px;background-color: {{ $row->color_code }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                                <li>
                                    <h3>{{ number_format($component->first()->price) }} VND</h3>
                                </li>
                                <hr>
                                <li>
                                    <p>IPhone là một điện thoại thông minh được sản xuất bởi Apple kết hợp máy tính, iPod, máy ảnh kỹ thuật số và điện thoại di động thành một thiết bị có giao diện màn hình cảm ứng. IPhone chạy hệ điều hành iOS và vào năm 2021 khi iPhone 13 được giới thiệu, nó đã cung cấp tới 1 TB dung lượng lưu trữ và camera 12 megapixel.</p>
                                </li>
                                <hr>
                                <li>
                                    <div class="product-option">
                                        <strong class="label">Lựa chọn phiên bản</strong>
                                        <div class="options" id="versionProduct" data-id="4" style="display: flex">
                                            <input type="hidden" name="component" value="">
                                        </div>
                                    </div>
                                </li>
                                <hr>
                                <li class="mau-sanpham">
                                    <div style="width: 30%;">
                                        <h4>Color</h4>
                                        <select name="color" required>
                                            @foreach($color as $row)
                                                <option value="{{ $row->id }}">
                                                    {{ $row->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 250px;">
                                        <h4>SỐ LƯỢNG</h4>
                                        <div class="slg-sanpham">
                                            <span class="giam">-</span>
                                            <input type="number" name="amount" class="soluong-sanpham" value="1" readonly style="width: 45px">
                                            <span class="tang">+</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="add" id="add-cart" style="background-color: black;color: white">
                                    THÊM VÀO GIỎ HÀNG
                                </li>
                                <li class="add" style="background-color: #f15e2c;">
                                    <a style="display: block" href="{{ route(STORE_CART) }}">THANH TOÁN</a>
                                </li>
                            </ul>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row" style="display: flex;justify-content: center">
        <div class="col-12 text-center">
            <h1>Giới thiệu sản phẩm</h1>
        </div>
        <div class="col-10">
            <div>
                {!! $product->description !!}
            </div>
        </div>
    </div>
    <hr>

    {{-- Product rating + comment --}}
    <div class="ml-4">
        <h1>Bình luận</h1>
        <form action="{{ route('product.rating') }}" method="post">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div id="comment" class="mt-3">
                <textarea class="form-control mt-3" id="summernote" name="comment" placeholder="Bình luận ở đây"></textarea>
            </div>
            <div id="rating" style="display: block">
                <input type="radio" id="star5" name="rating" value="5" />
                <label class = "full" for="star5" title="Awesome - 5 stars"></label>

                <input type="radio" id="star4" name="rating" value="4" />
                <label class = "full" for="star4" title="Pretty good - 4 stars"></label>

                <input type="radio" id="star3" name="rating" value="3" />
                <label class = "full" for="star3" title="Meh - 3 stars"></label>

                <input type="radio" id="star2" name="rating" value="2" />
                <label class = "full" for="star2" title="Kinda bad - 2 stars"></label>

                <input type="radio" id="star1" name="rating" value="1" />
                <label class = "full" for="star1" title="Sucks big time - 1 star"></label>
            </div>
            <div class="pt-5">
                <button class="btn btn-success mt-3" type="submit">Gửi</button>
            </div>
        </form>
    </div>

    <div id="show_comment" class="mt-3" style="height: 500px; overflow: auto">
        @foreach($rating as $row)
            <div class="pb-3 p-3" style="border: 1px solid">
                <p>Thời gian: {{ $row->created_at }}</p>
                <p>Người dùng: {{ $row->customer->email }}</p>
                <p>Bình chọn: {{ $row->rate }} sao</p>
                <div style="display: flex">
                    <div>Nội dung: </div>
                    <div> {!! $row->comment !!}</div>
                </div>
{{--                <div id="rated" style="display: block">--}}
{{--                    <input type="radio" id="star5" name="rating" value="5" {{ $row->rate == 5 ? 'checked=checked' : '' }} />--}}
{{--                    <label class = "full" for="star5" title="Awesome - 5 stars"></label>--}}

{{--                    <input type="radio" id="star4" name="rating" value="4" {{ $row->rate == 4 ? 'checked=checked' : '' }}/>--}}
{{--                    <label class = "full" for="star4" title="Pretty good - 4 stars"></label>--}}

{{--                    <input type="radio" id="star3" name="rating" value="3" {{ $row->rate == 3 ? 'checked=checked' : '' }}/>--}}
{{--                    <label class = "full" for="star3" title="Meh - 3 stars"></label>--}}

{{--                    <input type="radio" id="star2" name="rating" value="2" {{ $row->rate == 2 ? 'checked=checked' : '' }} />--}}
{{--                    <label class = "full" for="star2" title="Kinda bad - 2 stars"></label>--}}

{{--                    <input type="radio" id="star1" name="rating" value="1" {{ $row->rate == 1 ? 'checked=checked' : '' }} />--}}
{{--                    <label class = "full" for="star1" title="Sucks big time - 1 star"></label>--}}
{{--                </div>--}}
            </div>
        @endforeach
    </div>

    <hr>
    <!-- Sản phẩm liên quan -->
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12 mb-5">
                <h2 class="text-center">Sản phẩm liên quan</h2>
            </div>
        </div>

        <div id="multi-item-example" class="carousel slide carousel-multi-item" data-ride="carousel">
            <!--Slides-->
            <div class="carousel-inner" role="listbox">

                <!--First slide-->
                <div class="carousel-item active">
                    <div class="row">
                        @foreach($sameProduct as $row)
                            <div class="col-md-3 clearfix d-none d-md-block">
                                <div class="border-product">
                                    <img
                                        src="{{ asset('images') . '/' . $row->component->first()->image }}"
                                        class="img-thumbnail">
                                    <div class="pt-3"><strong>{{ $row->name }}</strong></div>
                                    <p>While/Black</p>
                                    <div>
                                        <strong>
                                            {{ number_format($row->component->first()->price, 0, '.', '.') }} VNĐ
                                        </strong>
                                    </div>
                                    <a href="{{ route(STORE_CART_DETAIL, $row->id) }}" class="btn btn-danger">Mua ngay</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
                <!--/.First slide-->
            </div>
            <!--/.Slides-->

        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function getMemory() {
            var form =  $('form').serializeArray();

            $.ajax({
                type: "get",
                url: "{{ route(STORE_GET_MEMORY) }}",
                data: {
                    "id": form[0].value,
                    "color": form[2].value,
                },
                success: function (e) {
                    if (e) {
                        var html = '';
                        $.each(e.data, function (key, value) {
                            // console.log(value);
                            html += '<div class="form-group">'+
                                        '<div class="item" data-component="' + value.id + '">'+
                                            '<span>'+
                                            '<label for="memory">'+
                                            '<strong>' + value.memory + 'GB</strong>'+
                                            '</label>'+
                                            '</span>'+
                                            '<strong>' + parseInt(value.price).toLocaleString() + '₫</strong>'+
                                        '</div>'+
                                    '</div>'
                        });

                        //Delete and apple new option
                        $('.product-option').find('.form-group').remove();
                        $('.product-option').find('.options').append(html);
                    }
                }
            });
        }
        $(document).ready(function () {
            //thay doi so luong
            $(".tang").click(function (event) {
                var tg = $(this).closest('.slg-sanpham');
                var sl = Number(tg.find(".soluong-sanpham").val());
                tg.find(".soluong-sanpham").val(sl + 1);
                tg.find(".giam").css({
                    cursor: 'pointer',
                    color: 'red'
                });

                var row = $(this).closest('.row');
                var dongia = Number(row.find('.price').html());
                var donhang = Number($(this).closest('.container').find('#price-don').html());
                $(this).closest('.container').find('#price-don').html(dongia + donhang);
                //alert(dongia);

            });

            $(".giam").click(function (event) {
                var tg = $(this).closest('.slg-sanpham');
                var sl = Number(tg.find('.soluong-sanpham').val());
                if (sl > 2) {
                    tg.find('.soluong-sanpham').val(sl - 1);
                } else {
                    tg.find('.soluong-sanpham').val(1);
                    $(this).css({
                        cursor: 'not-allowed',
                        color: 'black'
                    });
                }

            });

            getMemory();

            $('select[name="color"]').change(function () {
                getMemory();
                $('[name=component]').val('');
            });

            $('#versionProduct').find('.form-group:nth-child(2)').find('.item').css('background-color', 'gainsboro');

            $(document).on('click', '#versionProduct .item', function () {
                $('#versionProduct .item').removeAttr('style');
                $(this).css('background-color', 'gainsboro');
                $('[name=component]').val($(this).attr('data-component'));
                // console.log($('[name=component]').val());
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Nhập thông tin mô tả sản phẩm',
                height: 120,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                ]
            });
        });
    </script>
@endsection
