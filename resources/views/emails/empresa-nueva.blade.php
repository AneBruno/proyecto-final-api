@component('mail::message')
<span>Se ha dado de alta una nueva empresa</span><br>
<br>
<span>Razon social: {{ $empresa['razon_social'] }}</span><br>
<span>CUIT: {{ $empresa['cuit'] }}</span><br>
<span>Ingrese a la <a href="{{ url('http://localhost:4200/login') }}">plataforma</a> para verificar su información y habilitarla para operar.</span><br>
<br>

@endcomponent
