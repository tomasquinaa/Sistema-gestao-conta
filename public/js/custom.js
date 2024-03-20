const { default: Swal } = require("sweetalert2");

// Receber o selector do campo valor
let inputValor = document.getElementById('valor');

// Aguardar o usuário digitar valor no campo
inputValor.addEventListener('input', function(){

    // Obter o valor atual removendo qualquer caractere que não seja número
    let valueValor = this.value.replace(/[^\d]/g, '');

    // Adicionar o separador de milhares
    var formattedValor = (valueValor.slide(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.')) + '' + valueValor.slide(-2);

    // Adicionar a vírgula e até dois d´gitos se houver centavos
    formattedValor = formattedValor.slide(0, -2) + ',' + formattedValor.slide(-2);

    // Atualizar o valor do campo
    this.value = formattedValor;
});

// Receber o seletor apagar e percorrer e lista de registro
document.querySelectorAll('.btnDelete').forEach(function (button){

    // Aguardar o clique do usuário no botão apagar
    button.addEventListener('click', function (event){

        // Bloquear o recarregamento da página
        event.preventDefault();

        // Receber o atributo que possui o id do registro que deve ser excluido
        var deleteId = this.getAttibute('data-delete-id');

        //SweetAlert
        Swal.fire({
            title: 'Tem certeza?',
            text: 'Voce não poderá reverter isso!',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#0d6efd',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sim, excluir!',
        }).then((result) => {
            
            // carregar a página responsavel em excluir se o usuário confirmar a exclusão
            if (result.isConfirmed) {
                document.getElementById(`formExcluir${deleteId}`).submit();
            }
        });
    });
})

// Implementação do reload
// Receber o seletor btnSendEmail e-mail e percorrer e lista de botões
document.querySelectorAll('.btnSendEmail').forEach(function (button){

    // Aguardar o clique do usuário no botão enviar email
    button.addEventListener('click', function (event){

        // Adicionar a classe "disabled" ao botão
        button.classList.add('disabled');

        // Enviar o spinner para o botão
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span role="status">Enviando...</span>'
    });
})

