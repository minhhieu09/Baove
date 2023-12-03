@extends('index')
@section('content')
<!-- Carousel -->
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://theme.hstatic.net/200000191303/1000790874/14/ms_banner_img2.jpg?v=22" class="d-block w-100" alt="anh">
        </div>
        <div class="carousel-item">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRDPtZfRGWfj9LUxZwuidKnrbQM3K-uE4FSz_aFgbMK1mkEbNGRF0vg4DVc0OfJkRcKvU&usqp=CAU" class="d-block w-100" alt="anh">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<!-- Main Content-->
@if(isset($specials))
@foreach($specials as $special)
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="title-product">{{ $special->name }}</h2>
            </div>
        </div>
        <div class="row mt-3">
            @foreach($special->product as $key => $row)
                @if($key <= 3)
                    <div class="col-md-3">
                        <div class="border-product">
                            <input type="hidden" name="product_id" value="{{ $row->id }}">
                            <img src="{{ asset('images/' . $row->component->first()->image) }}"
                                class="img-thumbnail">
                            <div class="pt-3 name"><strong>{{ $row->name }}</strong></div>
                            @php
                                $colors = $row->component->pluck('color.name')->toArray();
                                $combinedColors = implode('/', $colors);
                            @endphp
                            <p>{{ $combinedColors }}</p>
                            <div>
                                <strong class="price">
                                    {{ number_format($row->component->first()->price) }} VNĐ
                                </strong>
                            </div>
                            <a href="{{ route(STORE_CART_DETAIL, $row->id) }}" class="btn btn-danger">Mua ngay</a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endforeach
@endif

<hr>

<!-- Sản phẩm mới -->
<div class="new-product mt-5">
    <img src="https://images.macrumors.com/t/zJxyEXeL_489TZP8hMRA9JA3v1Q=/1600x900/smart/article-new/2022/09/iOS-16-Wallpaper-Spectrum-Feature.jpg" class="img-fluid">
    <div>
        <h3>Nike Hypervemon</h3>
        <p>Up to sale 70%</p>
    </div>
</div>
@endsection
@section('script')
    <script>
        function showCart(data) {
            var row = '';
            var sort = [];
            $.each(data, function (index, value) {
                sort.push(value);
            });
            sort.sort(function (a, b) {
                return a.time - b.time;
            });
            $.each(sort, function (index, val) {
                row += '<div class="row">' + '<input type="hidden" name="product_id" value="' + val.id + '">' + '<div class="col-md-5">' + '<img src="' + 'http://127.0.0.1:8000/images/' + val.img + '">' + '</div>' + '<div class="col-md-7">' + '<strong>' + val.name + '</strong>' + '<div class="product-giohang">' + '<div>' + '<p>Giá: </p>' + '<p>' + val.price + '</p>' + '</div>' + '<div>' + '<p>Số lượng: </p>' + '<p>' + val.amount + '</p>' + '</div>' + '</div>' + '</div>' + '<hr>' + '</div>';
            });
            return row;
        }

        $(document).ready(function () {
            $.ajax({
                type: "get",
                url: "/store/cart/cart-session",
                success: function success(e) {
                    $('#scroll-giohang').append(showCart(e));
                }
            });
        })
    </script>
@endsection


