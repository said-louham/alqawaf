@foreach($attributes as $attribute)
    <div class="form-group row">
        <div class="col-md-3">
            <input type="text" class="form-control" value="{{ $attribute->getTranslation('title', \App::getLocale()) }}" disabled>
            <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">
        </div>
        <div class="col-md-9">
            @php $count = 0; @endphp
            @foreach($attribute->attributeValue as $value)
                @if($count % 2 == 0)
                    <div class="row">
                @endif
                <div class="col-md-6">
                    <label for="price_{{ $attribute->id }}_{{ $value->id }}">
                        <span class="font-weight-bold">{{ $value->value }}</span> Price
                    </label>
                    @foreach($product->attributeValues as $productValue)
                        @if($productValue['attribute_id'] == $attribute->id && $productValue['attribute_value_id'] == $value->id)
                            <?php $price = $productValue['price']; ?>
                        @endif
                    @endforeach
                    <input  type="number" step="any" class="form-control" name="product_attribute_values[{{ $attribute->id }}][{{ $value->id }}][price]" id="price_{{ $attribute->id }}_{{ $value->id }}" value="{{ old('product_attribute_values.'.$attribute->id.'.'.$value->id.'.price', $price ?? '') }}">
                    <input type="hidden" name="product_attribute_values[{{ $attribute->id }}][{{ $value->id }}][attribute_value_id]" value="{{ $value->id }}">
                </div>
                @if($count % 2 == 1 || $loop->last)
                    </div>
                @endif
                @php $count++; @endphp
            @endforeach
        </div>
    </div>
@endforeach
