<?php $totalServico = 0;
$totalProdutos = 0; ?>

<!-- Importa os scripts necessários para a assinatura -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/signature_pad.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/assinaturas.js"></script>

<!-- Variáveis globais necessárias para o assinaturas.js funcionar -->
<script>
    window.base_url = <?php echo json_encode(base_url()); ?>;
    window.idOs = null; // Será preenchido ao clicar no botão de assinar
</script>

<style>
    /* Estilos do Mini Modal de Assinatura */
    .modal-assinatura {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.6);
        align-items: center;
        justify-content: center;
    }
    .modal-assinatura-content {
        background-color: #fff;
        padding: 25px 20px;
        border-radius: 8px;
        width: 95%;
        max-width: 550px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        text-align: center;
    }
    #assCliente-pad {
        border: 1px solid #333333;
        border-radius: 6px;
        background-color: #fff;
        width: 100%;
        max-width: 600px;
        height: 300px;
    }
</style>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Ordem de Serviço</h5>
                <div class="buttons" style=" padding-left:5px;">
                    <!-- Botão de Assinatura (Apenas se estiver Aberto) -->
                    <?php if ($result->status == 'Aberto') { ?>
                        <button type="button" class="button btn btn-mini btn-warning btn-abrir-assinatura" data-id="<?php echo $result->idOs; ?>" title="Assinar OS">
                            <span class="button__icon"><i class="bx bx-pencil"></i></span> <span class="button__text">Assinar OS</span>
                        </button>
                    <?php } ?>
                    
                    <a target="_blank" title="Imprimir Relatório" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/mine/imprimirOs/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Imprimir Relatório</span></a>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head" style="margin-bottom: 0">

                        <table class="table table-condensed">
                            <tbody>
                                <?php if ($emitente == null) { ?>
                                    <tr>
                                        <td colspan="3" class="alert">Os dados do emitente não foram configurados.</td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td style="width: 25%"><img src=" <?php echo $emitente->url_logo; ?> " style="max-height: 100px"></td>
                                        <td>
                                            <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                            <?php if ($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                            <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i> <?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                            <span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></span></br>
                                            <span class="icon"><i class="fas fa-user-check"></i> Responsável: <?php echo $result->nome ?>
                                        </td>
                                        <td style="width: 18%; text-align: center">
                                            <span><b>N° OS: </b><?php echo $result->idOs ?></span></br></br>
                                            <span>Emissão: <?php echo date('d/m/Y') ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 0; padding-top: 0">
                        <table class="table table-condensed">
                            <tbody>
                                    <?php if ($result->dataInicial != null) { ?>
                                        <tr>
                                            <td>
                                                <b>STATUS OS: </b><?php echo $result->status ?>
                                            </td>

                                            <td>
                                                <b>DATA INICIAL: </b><?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                            </td>

                                            <td>
                                                <b>DATA FINAL: </b><?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($result->garantia)) { ?>
                                                    <b>GARANTIA: </b><?php echo $result->garantia . ' dia(s)'; ?>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <b><?php if ($result->status == 'Finalizado') { ?> VENC. DA GARANTIA: </b><?php echo dateInterval($result->dataFinal, $result->garantia); ?><?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>

                        <table class="table table-condensed">
                            <?php if ($result->descricaoProduto != null || $result->defeito != null || $result->laudoTecnico != null || $result->observacoes) { ?>
                                    <?php if ($result->descricaoProduto != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>DESCRIÇÃO: </strong><br>
                                                <?php echo printSafeHtml($result->descricaoProduto) ?>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                    <?php if ($result->defeito != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>DEFEITO APRESENTADO: </strong><br>
                                                <?php echo printSafeHtml($result->defeito) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->observacoes != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>OBSERVAÇÕES: </strong><br>
                                                <?php echo printSafeHtml($result->observacoes) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->laudoTecnico != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>LAUDO TÉCNICO: </strong><br>
                                                <?php echo printSafeHtml($result->laudoTecnico) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->garantias_id != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>TERMO DE GARANTIA </strong><br>
                                                <?php echo printSafeHtml($result->textoGarantia) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            <?php } ?>
                        </table>

                        <?php if ($anexos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Anexo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <th>
                                    <?php foreach ($anexos as $a) {
                                        if ($a->thumb == null) {
                                            $thumb = base_url() . 'assets/img/icon-file.png';
                                            $link = base_url() . 'assets/img/icon-file.png';
                                        } else {
                                            $thumb = $a->url . '/thumbs/' . $a->thumb;
                                            $link = $a->url . '/' . $a->anexo;
                                        }
                                        echo '<div class="span3" style="min-height: 150px; margin-left: 0"><a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal"><img src="' . $thumb . '" alt=""></a></div>';
                                    } ?>
                                    </th>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php $totalServico = 0;
$totalProdutos = 0; ?>
                        <?php if ($produtos != null) { ?>
                            <br />
                            <table class="table table-bordered table-condensed" id="tblProdutos">
                                <thead>
                                    <tr>
                                        <th>PRODUTO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $p) {
                                        $totalProdutos = $totalProdutos + $p->subTotal;
                                        echo '<tr>';
                                        echo '<td>' . $p->descricao . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';
                                        echo '<td>R$ ' . $p->preco ?: $p->precoVenda . '</td>';
                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php if ($servicos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>SERVIÇO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php setlocale(LC_MONETARY, 'en_US');
                            foreach ($servicos as $s) {
                                $totalServico = $totalServico + $s->subTotal;
                                $preco = $s->preco ?: $s->precoVenda;
                                $subtotal = $preco * ($s->quantidade ?: 1);
                                echo '<tr>';
                                echo '<td>' . $s->nome . '</td>';
                                echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                echo '<td>R$ ' . $preco . '</td>';
                                echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                echo '</tr>';
                            } ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>

                        <table class="table table-condensed">
                            <thead>
                                <td>
                                    <?php if ($totalProdutos != 0 || $totalServico != 0) {
                                        if ($result->valor_desconto != 0) {
                                            echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                            echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto : 0.00, 2, ',', '.') . "</h4>" : "";
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_total, 2, ',', '.') . "</h4>";
                                        } else {
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                        }
                                    }?>
                                </td>

                                <?php if ($result->status == 'Finalizado' || $result->status == 'Aprovado') { ?>
                                    <?php if ($qrCode) : ?>
                                        <td style="width: 15%; padding-left: 0; text-align:center;">
                                            <img style="margin:0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="48px" alt="QR Code de Pagamento" /></br>
                                            <img style="margin:6px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . $chaveFormatada . '</span>';?>
                                        </td>
                                    <?php endif ?>
                                <?php } ?>
                            </thead>
                        </table>

                        <!-- SEÇÃO DE EXIBIÇÃO DA ASSINATURA -->
                        <?php if (isset($result->assClienteImg) && $result->assClienteImg != null) { ?>
                            <table class="table table-condensed" style="margin-top: 30px; border-top: 0px;">
                                <tbody>
                                    <tr>
                                        <td style="text-align: center; border-top: 0px;">
                                            <img src="<?php echo $result->assClienteImg; ?>" alt="Assinatura do Cliente" style="max-width: 400px; max-height: 150px;">
                                            <div style="width: 400px; border-top: 1px solid #333; margin: 10px auto 5px auto;"></div>
                                            <strong>Assinatura do Cliente - <?php echo $result->nome; ?></strong><br>
                                            <span style="font-size: 12px; color: #555;">
                                                Assinado em: <?php echo date('d/m/Y H:i:s', strtotime($result->assClienteData)); ?>
                                                <?php if($result->assClienteIp) echo ' | IP: ' . $result->assClienteIp; ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                        <!-- FIM DA SEÇÃO DE ASSINATURA -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inicio Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
    </div>
</div>

<!-- Mini Modal de Assinatura usando os IDs nativos do assinaturas.js -->
<div id="modalAssinatura" class="modal-assinatura">
    <div class="modal-assinatura-content">
        <h3>Assinar Ordem de Serviço</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Desenhe sua assinatura abaixo:</p>
        
        <canvas id="assCliente-pad" width="600" height="300"></canvas>
        
        <div style="margin-top: 15px;">
            <input type="text" name="nomeAssinatura" id="nomeAssinatura" placeholder="Nome e Sobrenome*" style="width: 90%; padding: 6px;" class="text-center">
        </div>
        
        <div style="margin-top: 15px;" class="buttons-a">
            <button type="button" id="limparAssCliente" class="btn btn-danger">Limpar</button>
            <button type="button" id="salvarAssCliente" class="btn btn-success">Enviar Assinatura</button>
            <button type="button" id="fecharAssModal" class="btn">Cancelar</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);
        });

        // Abrir Mini Modal de Assinatura
        $(document).on('click', '.btn-abrir-assinatura', function(e) {
            e.preventDefault();
            window.idOs = $(this).data('id');
            $('#modalAssinatura').css('display', 'flex');
        });

        // Fechar Mini Modal
        $('#fecharAssModal, #modalAssinatura').on('click', function(e) {
            if (e.target === this) {
                $('#modalAssinatura').hide();
            }
        });

        // Monitora as requisições AJAX do assinaturas.js para ocultar o modal após o envio
        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success === true && response.message.includes('Assinatura')) {
                        $('#modalAssinatura').hide(); 
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } catch (e) {
                    // Ignora
                }
            }
        });
    });
</script>
<!-- Fim Modal visualizar anexo -->

<script type="text/javascript">
    $(document).ready(function() {
        $("#imprimir").click(function() {
            PrintElem('#printOs');
        })

        function PrintElem(elem) {
            Popup($(elem).html());
        }

        function Popup(data) {
            var mywindow = window.open('', 'mydiv', 'height=600,width=800');
            mywindow.document.open();
            mywindow.document.onreadystatechange = function() {
                if (this.readyState === 'complete') {
                    this.onreadystatechange = function() {};
                    mywindow.focus();
                    mywindow.print();
                    mywindow.close();
                }
            }

            mywindow.document.write('<html><head><title>Map Os</title>');
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/bootstrap.min.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/matrix-style.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/matrix-media.css' />");

            mywindow.document.write("</head><body >");
            mywindow.document.write(data);
            mywindow.document.write("</body></html>");

            mywindow.document.close(); // necessary for IE >= 10

            return true;
        }
    });
</script>