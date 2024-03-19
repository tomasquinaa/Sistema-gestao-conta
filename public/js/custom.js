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


function confirmarExclusao(event, contaId) {

    event.preventDefault();

    Swal.fire({
        title: 'Tem certeza?',
        text: 'Você não poderá reverter isso!',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: "#0d6efd",
        cancelButtonText: "Cancelar",
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sim, excluir',
    });
}