$(document).ready(() => {
	$('#documentacao').on('click', () => {
        //$('#pagina').load('documentacao.html')
        
        /*
        $.get('documentacao.html', data => {
            $("#pagina").html(data)
        })
        */
        $.post('documentacao.html', data => {
            $("#pagina").html(data)
        })
    })

    $('#suporte').on('click', () => {
        //$('#pagina').load('suporte.html')

        /*
        $.get('suporte.html', data => {
            $("#pagina").html(data)
        })
        */
        $.post('suporte.html', data => {
            $("#pagina").html(data)
        })
    })

    $('#competencia').on('change', e => {
        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: data => {
            $('#numeroVendas').html(data.numeroVendas)
            $('#totalVendas').html(data.totalVendas)
            $('#clientesAtivos').html(data.clientesAtivos)
            $('#clientesInativos').html(data.clientesInativos)
            $('#totalReclamacoes').html(data.totalReclamacoes)
            $('#totalElogios').html(data.totalElogios)
            $('#totalSugestoes').html(data.totalSugestoes)
            $('#totalDespesas').html(data.totalDespesas)
            },
            error: erro => {alert(erro)}
        })
    })
})