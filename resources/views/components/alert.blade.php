@if (session()->has('success'))
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire('Pronto!',"{{ session('success') }}", 'success');
        });
    </script> --}}
    <script>
        swal("pronto","{{ Session::get('success') }}", 'success',{
            button:true,
            button:"OK",
        });
    </script>
@endif

@if (session()->has('error'))
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire('Pronto!',"{{ session('success') }}", 'success');
        });
    </script> --}}
    <script>
        swal("Erro","{{ Session::get('error') }}", 'error',{
            button:true,
            button:"OK",
        });
    </script>
@endif

@if ($errors->any())
    @php 
        $mensagem = '';
        foreach ($errors->all() as $error) {
            $mensagem .= $error . '<br>'; 
        }
    @endphp
    <script>
        swal("Error","{!! $mensagem !!}", 'error',{
            button:true,
            button:"OK",
        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire('Error!',"{!! $mensagem !!}", 'error');
        });
    </script> --}}
@endif

