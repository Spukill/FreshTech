@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ url('/cart') }}" class="text-decoration-none text-primary">Shopping Cart</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Checkout</li>
    </ol>
</nav>

<div class="container py-5">
  <div class="row">
    <div class="col-md-6">
      <h4 class="mb-3">Order summary</h4>

      <ul class="list-group mb-3">
        @foreach(($cart->items ?? collect()) as $item)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ $item->product->name ?? 'Product #' . ($item->product_id ?? '') }}</strong>
              <div class="text-muted small">Qty: {{ $item->quantity }}</div>
            </div>
            <span>€{{ $item->discSubTotal() }}</span>
          </li>
        @endforeach

        <li class="list-group-item d-flex justify-content-between">
          <strong>Total</strong>
          <strong>€{{ number_format($total ?? 0, 2) }}</strong>
        </li>
      </ul>
    </div>

    <div class="col-md-6">
      <h4 class="mb-3">Payment</h4>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('checkout.process') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Shipping address</label>
          <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address') }}</textarea>
          @error('shipping_address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Payment method: radio buttons (card / paypal / mbway) --}}
        <div class="mb-3">
          <label class="form-label d-block">Payment method</label>

          @php $pm = old('payment_method', 'card'); @endphp

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" id="pm_card" value="card" {{ $pm === 'card' ? 'checked' : '' }}>
            <label class="form-check-label" for="pm_card">Card</label>
          </div>

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" id="pm_paypal" value="paypal" {{ $pm === 'paypal' ? 'checked' : '' }}>
            <label class="form-check-label" for="pm_paypal">PayPal</label>
          </div>

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" id="pm_mbway" value="mbway" {{ $pm === 'mbway' ? 'checked' : '' }}>
            <label class="form-check-label" for="pm_mbway">MBWay</label>
          </div>

          @error('payment_method') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Card fields (shown only when card selected) --}}
        @if ($pm === 'card')
        <div id="card_fields" style="display:block">
        @else
        <div id="card_fields" style="display:none">
        @endif
          <div class="mb-3">
            <label class="form-label">Cardholder name</label>
            <input type="text" name="card_name" id="card_name" class="form-control" placeholder="Name on card" value="{{ old('card_name') }}">
            @error('card_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Card number</label>
            <input type="text" name="card_number" id="card_number" class="form-control" placeholder="4111 1111 1111 1111" maxlength="19" inputmode="numeric" value="{{ old('card_number') }}">
            @error('card_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="row">
            <div class="col-6 mb-3">
              <label class="form-label">Expiry</label>
              <input type="text" name="card_expiry" id="card_expiry" class="form-control" placeholder="MM/YY" maxlength="5" value="{{ old('card_expiry') }}">
              @error('card_expiry') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-6 mb-3">
              <label class="form-label">CVC</label>
              <input type="text" name="card_cvc" id="card_cvc" class="form-control" placeholder="CVC" maxlength="4" value="{{ old('card_cvc') }}">
              @error('card_cvc') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
          </div>


        </div>

        {{-- Phone field (shown for non-card methods; mandatory when shown) --}}
        <div id="phone_field" class="{{ $pm === 'card' ? 'd-none' : 'd-block' }}">
          <div class="mb-3">
            <label class="form-label">Phone number</label>
            <input type="tel" name="phone" id="phone" class="form-control" placeholder="+351 9xx xxx xxx" value="{{ old('phone') }}">
            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            <div class="form-text">Phone required for PayPal/MBWay.</div>
          </div>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="agree" id="agree" value="1" {{ old('agree') ? 'checked' : '' }} required>
          <label class="form-check-label" for="agree">I agree to pay the amount shown</label>
          @error('agree') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Pay €{{ number_format($total ?? 0, 2) }}</button>
          <a href="{{ route('cart') }}" class="btn btn-outline-secondary">Back to cart</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const pmEls = Array.from(document.querySelectorAll('input[name="payment_method"]'));
  const cardFields = document.getElementById('card_fields');
  const phoneField = document.getElementById('phone_field');

  function updateVisibility() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'card';
    if (selected === 'card') {
      cardFields.classList.remove('d-none');
      phoneField.classList.add('d-none');
      // set required on card inputs
      document.getElementById('card_name').required = true;
      document.getElementById('card_number').required = true;
      document.getElementById('card_expiry').required = true;
      document.getElementById('card_cvc').required = true;
      document.getElementById('phone').required = false;
    } else {
      cardFields.classList.add('d-none');
      phoneField.classList.remove('d-none');
      document.getElementById('card_name').required = false;
      document.getElementById('card_number').required = false;
      document.getElementById('card_expiry').required = false;
      document.getElementById('card_cvc').required = false;
      document.getElementById('phone').required = true;
    }
  }

  pmEls.forEach(el => el.addEventListener('change', updateVisibility));
  updateVisibility();
});
</script>
@endpush
