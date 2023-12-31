@forelse($products as $product)
    <tr>
        <td data-th="Product">
            <img src="{{ asset('/flower-subscription/'.$product->path) }}"
                 width="100" height="100"
                 class="img-responsive"/>
            {{ $product->title }}
        </td>
        <input type="hidden" name="price" value="{{$product->price}}">
        <td data-th="Price" class="price">${{$product->price}}</td>
        <td data-th="Quantity">
            <input type="number" min="1"
                   value="1"
                   data-flowerid="{{ $product->pk_floral_arrangements }}"
                   id="quantity_{{ $product->pk_floral_arrangements }}"
                   class="form-control quantity"/>
        </td>
        <td data-th="Subtotal" class="text-center">$<span
                class="product-subtotal_{{$product->pk_floral_arrangements}}"
                data-flowerid="{{$product->pk_floral_arrangements}}"></span>
        </td>
        <td class="actions" data-th="">
            <button type="button"
                    class="btn btn-info btn-sm update-cart"
                    data-id=""
                    data-prev-qty="">Add to Cart
            </button>

            <i class="fa fa-circle-o-notch fa-spin btn-loading"
               style="font-size:24px; display: none"></i>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-center" colspan="100%">No data found!</td>
    </tr>
@endforelse
