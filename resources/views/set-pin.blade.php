<h2>Buat PIN Transaksi</h2>

<form method="POST" action="/set-pin">
    @csrf

    <input
        type="password"
        name="pin"
        maxlength="6"
        placeholder="PIN 6 Digit">

    <button type="submit">
        Simpan PIN
    </button>
</form>

@if($errors->any())
    @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
@endif