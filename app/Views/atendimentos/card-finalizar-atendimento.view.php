<div class="modal fade" id="finalizar-atendimento" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="card-title">Finalizar Atendimento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja finalizar este atendimento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="finalizarAtendimento(<?= $atendimento->id ?>)">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function finalizarAtendimento(atendimento_id) {
        fetch(`/atendimento/finalizar/${atendimento_id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.href = "/home";
        });
    }
</script>