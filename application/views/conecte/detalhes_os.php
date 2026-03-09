<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/signature_pad.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/assinaturas.js"></script>

<style>
    .ui-datepicker {
        z-index: 9999 !important;
    }

    .trumbowyg-box {
        margin-top: 0;
        margin-bottom: 0;
    }

    #assCliente-pad {
        border: 1px solid #333333;
        border-radius: 6px;
        background-color: #fff;
    }

    .buttons-a {
        margin-top: 10px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Detalhes OS</h5>
            </div>
            <div class="widget-content nopadding tab-content">

                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li <?php echo $tab != 5 ? 'class="active" ' : '' ?> id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                        <li id="tabProdutos"><a href="#tab2" data-toggle="tab">Produtos</a></li>
                        <li id="tabServicos"><a href="#tab3" data-toggle="tab">Serviços</a></li>
                        <li id="tabAnexos"><a href="#tab4" data-toggle="tab">Anexos</a></li>
                        <?php if ($usar_assinatura): ?>
                            <li <?php echo $tab == 5 ? 'class="active" ' : '' ?> id="tabAssinar"><a href="#tab5" data-toggle="tab">Assinatura</a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php echo $tab != 5 ? 'active' : '' ?>" id="tab1">

                            <div class="span12" id="divCadastrarOs">
                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <div class="span6" style="margin-left: 0">
                                        <h3>#Protocolo: <?php echo $result->idOs ?></h3>
                                        <input id="valorTotal" type="hidden" name="valorTotal" value="" />
                                    </div>
                                    <div class="span6">
                                        <label for="tecnico">Técnico / Responsável</label>
                                        <input disabled="disabled" id="tecnico" class="span12" type="text" name="tecnico" value="<?php echo $result->nome ?>" />
                                    </div>
                                </div>
                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <div class="span3">
                                        <label for="status">Status</label>
                                        <input disabled="disabled" class="span12" type="text" name="status" id="status" value="<?php echo $result->status; ?>">
                                    </div>
                                    <div class="span3">
                                        <label for="dataInicial">Data Inicial</label>
                                        <input id="dataInicial" disabled="disabled" class="span12" type="text" name="dataInicial" value="<?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>" />
                                    </div>
                                    <div class="span3">
                                        <label for="dataFinal">Data Final</label>
                                        <input id="dataFinal" disabled="disabled" class="span12" type="text" name="dataFinal" value="<?php echo date('d/m/Y', strtotime($result->dataFinal)); ?>" />
                                    </div>
                                    <div class="span3">
                                        <label for="garantia">Garantia</label>
                                        <input id="garantia" disabled="disabled" type="text" class="span12" name="garantia" value="<?php echo $result->garantia ?>" />
                                    </div>
                                </div>

                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <label for="descricaoProduto">Descrição Produto/Serviço</label>
                                    <textarea class="span12 editor" name="descricaoProduto" id="descricaoProduto" cols="30" rows="5" disabled><?php echo $result->descricaoProduto; ?></textarea>
                                </div>

                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <label for="defeito">Defeito</label>
                                    <textarea class="span12 editor" name="defeito" id="defeito" cols="30" rows="5" disabled><?php echo $result->defeito; ?></textarea>
                                </div>

                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="span12 editor" name="observacoes" id="observacoes" cols="30" rows="5" disabled><?php echo $result->observacoes; ?></textarea>
                                </div>

                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <label for="laudoTecnico">Laudo Técnico</label>
                                    <textarea class="span12 editor" name="laudoTecnico" id="laudoTecnico" cols="30" rows="5" disabled><?php echo $result->laudoTecnico; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab2">
                            <div class="span12" id="divProdutos" style="margin-left: 0">
                                <table class="table table-bordered" id="tblProdutos">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Preço unit.</th>
                                            <th>Quantidade</th>
                                            <th>Sub-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($produtos as $p) {
                                            $total = $total + $p->subTotal;
                                            echo '<tr>';
                                            echo '<td>' . $p->descricao . '</td>';
                                            echo '<td>R$ ' . number_format($p->preco, 2, ',', '.') . '</td>';
                                            echo '<td>' . $p->quantidade . '</td>';
                                            echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                            echo '</tr>';
                                        } ?>
                                        <tr>
                                            <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                                            <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab3">
                            <div class="span12" id="divServicos" style="margin-left: 0">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Serviço</th>
                                            <th>Preço unit.</th>
                                            <th>Quantidade</th>
                                            <th>Sub-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalS = 0;
                                        foreach ($servicos as $s) {
                                            $totalS = $totalS + $s->subTotal;
                                            echo '<tr>';
                                            echo '<td>' . $s->nome . '</td>';
                                            echo '<td>R$ ' . number_format($s->preco, 2, ',', '.') . '</td>';
                                            echo '<td>' . $s->quantidade . '</td>';
                                            echo '<td>R$ ' . number_format($s->subTotal, 2, ',', '.') . '</td>';
                                            echo '</tr>';
                                        } ?>
                                        <tr>
                                            <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                                            <td><strong>R$ <?php echo number_format($totalS, 2, ',', '.'); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab4">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <?php if ($this->session->userdata('cliente_anexa')) { ?>
                                    <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                        <form id="formAnexos" enctype="multipart/form-data" action="javascript:;" accept-charset="utf-8" method="post">
                                            <div class="span10">
                                                <input type="hidden" name="idOsServico" id="idOsServico" value="<?php echo $result->idOs ?>" />
                                                <label for="">Anexo</label>
                                                <input type="file" class="span12" name="userfile[]" multiple="multiple" size="20" />
                                            </div>
                                            <div class="span2">
                                                <label for="">&nbsp;</label>
                                                <button class="btn btn-success span12"><i class="fas fa-paperclip"></i> Anexar</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php } ?>

                                <div class="span12" id="divAnexos" style="margin-left: 0">
                                    <?php foreach ($anexos as $a) {
                                        $thumb = $a->thumb == null ? base_url() . 'assets/img/icon-file.png' : $a->url . '/thumbs/' . $a->thumb;
                                        $link = $a->thumb == null ? base_url() . 'assets/img/icon-file.png' : $a->url . '/' . $a->anexo;
                                        echo '<div class="span3" style="min-height: 150px; margin-left: 0; text-align:center;">
                                            <a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal">
                                                <img src="' . $thumb . '" alt="">
                                            </a>
                                            <span>' . $a->anexo . '</span>
                                        </div>';
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($usar_assinatura): ?>
                            <div class="tab-pane <?php echo $tab == 5 ? 'active' : '' ?>" id="tab5">
                                <div class="span12" style="padding: 1%; margin-left: 0">
                                    <h3>Autorizar e assinar Ordem de Serviço</h3>
                                    <p style="margin-left: 10px;">Ao assinar e enviar sua assinatura você estará autorizando a execução da ordem de serviço!</p>
                                    <div class="span11">
                                        <div class="span10" id="assinaturaCliente" style="text-align:center;">
                                            <?php if (!$result->assClienteImg): ?>
                                                <canvas id="assCliente-pad" width="600" height="300"></canvas>
                                                <p style="margin-top: 10px;"><input type="text" name="nomeAssinatura" id="nomeAssinatura" placeholder="Nome e Sobrenome*" class="text-center"></p>
                                                <h4>Assinatura do Cliente</h4>
                                            <?php else: ?>
                                                <img src="<?php echo $result->assClienteImg ?>" width="600" alt="">
                                                <h4>Assinatura do Cliente</h4>
                                                <p>Em <?php echo date('d/m/Y H:i:s', strtotime($result->assClienteData)) ?></p>
                                                <p>IP: <?php echo $result->assClienteIp ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!$result->assClienteImg): ?>
                                            <div class="span10" style="text-align:center; margin-left:0;">
                                                <div class="buttons-a">
                                                    <button id="limparAssCliente" type="button" class="btn btn-danger">Limpar</button>
                                                    <button id="salvarAssCliente" type="button" class="btn btn-success">Enviar Assinatura</button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center"></div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" class="btn btn-inverse" id="download">Download</a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.editor').trumbowyg({
            lang: 'pt_br',
            semantic: { 'strikethrough': 's' }
        });

        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#download").attr('href', "<?php echo base_url(); ?>index.php/mine/downloadanexo/" + id);
        });

        // Configurações globais para assinaturas.js
        window.base_url = <?php echo json_encode(base_url()); ?>;
        window.idOs = <?php echo json_encode($result->idOs); ?>;
    });
</script>