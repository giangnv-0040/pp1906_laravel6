@extends('layouts.shop')

@section('content')

<h1>Product show page</h1>
<h3>Name: {{ $product->name }}</h3>
<h3>Content: {{ $product->content }}</h3>
<h3>Quantity: {{ $product->quantity }}</h3>
<h3>Price: {{ $product->price }}</h3>

@endsection