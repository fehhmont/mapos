<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

<!-- Importa os mesmos scripts usados na tela de detalhes -->
 <script type="text/javascript" src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/signature_pad.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/assinaturas.js"></script>

<!-- Variáveis globais necessárias para o assinaturas.js funcionar -->
<script>
    window.base_url = <?php echo json_encode(base_url()); ?>;
    window.idOs = null; // Inicia vazio e será preenchido dinamicamente ao clicar no lápis
</script>

<style>
    .quick-actions_homepage .cardBox {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
    }
    .quick-actions_homepage .cardBox .card {
        background: #fff;
        border-radius: 12px;
        padding: 25px 15px;
        flex: 1;
        min-width: 200px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .quick-actions_homepage .cardBox .card:hover {
        transform: translateY(-3px);
    }
    .quick-actions_homepage .cardBox .card .custom-icon-box {
        width: 70px;
        height: 70px;
        margin-bottom: 15px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quick-actions_homepage .cardBox .card .custom-icon-box i {
        font-size: 2.3rem;
        color: #fff;
    }
    .quick-actions_homepage .cardBox .card .numbers {
        font-size: 1.3rem !important;
        font-weight: 600;
        color: #333;
        margin-top: 5px;
    }
    .quick-actions_homepage .cardBox .card a {
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

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
    }
</style>

<div class="quick-actions_homepage">
    <ul class="cardBox">
        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/os">
                <div class="custom-icon-box" style="background-color: #436eee;">
                    <i class='bx bx-file'></i>
                </div>
                <div class="numbers">Ordens de Serviço</div>
            </a>
        </li>

        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/compras">
                <div class="custom-icon-box" style="background-color: #FF7F00;">
                    <i class='bx bx-cart-alt'></i>
                </div>
                <div class="numbers">Compras</div>
            </a>
        </li>

        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/cobrancas">
                <div class="custom-icon-box" style="background-color: #00cd00;">
                    <i class='bx bx-credit-card-front'></i>
                </div>
                <div class="numbers">Cobranças</div>
            </a>
        </li>

        <li class="card">
            <a href="<?php echo base_url() ?>index.php/mine/conta">
                <div class="custom-icon-box" style="background-color: #B266FF;">
                    <i class='bx bx-user-circle'></i>
                </div>
                <div class="numbers">Minha Conta</div>
            </a>
        </li>
    </ul>
</div>

<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-signal"></i></span>
            <h5>Últimas Ordens de Serviço</h5>
        </div>
        <div class="widget-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Responsável</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Venc. da Garantia</th>
                        <th>Status</th>
                        <th style="text-align:right">Visualizar / Imprimir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($os != null) {
                        foreach ($os as $o) {
                            $vencGarantia = '';

                            if ($o->garantia && is_numeric($o->garantia)) {
                                $vencGarantia = dateInterval($o->dataFinal, $o->garantia);
                            }
                            $corGarantia = '';
                            if (!empty($vencGarantia)) {
                                $dataGarantia = explode('/', $vencGarantia);
                                $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                if (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) {
                                    $corGarantia = '#4d9c79';
                                } else {
                                    $corGarantia = '#f24c6f';
                                }
                            } elseif ($o->garantia == "0") {
                                $vencGarantia = 'Sem Garantia';
                                $corGarantia = '';
                            } else {
                                $vencGarantia = '';
                                $corGarantia = '';
                            }

                            switch ($o->status) {
                                case 'Aberto':
                                    $cor = '#00cd00';
                                    break;
                                case 'Em Andamento':
                                    $cor = '#436eee';
                                    break;
                                case 'Orçamento':
                                    $cor = '#CDB380';
                                    break;
                                case 'Negociação':
                                    $cor = '#AEB404';
                                    break;
                                case 'Cancelado':
                                    $cor = '#CD0000';
                                    break;
                                case 'Finalizado':
                                    $cor = '#256';
                                    break;
                                case 'Faturado':
                                    $cor = '#B266FF';
                                    break;
                                case 'Aguardando Peças':
                                    $cor = '#FF7F00';
                                    break;
                                case 'Aprovado':
                                    $cor = '#808080';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                                    break;
                            }

                            echo '<tr>';
                            echo '<td>' . $o->idOs . '</td>';
                            echo '<td>' . $o->nome . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($o->dataInicial)) . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($o->dataFinal)) . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $o->status . '</span> </td>';
                            echo '<td style="text-align:right">';
                            
                            // Exibe o ícone do lápis disparando o modal se o status for 'Aberto'
                            if ($o->status == 'Aberto') {
                                echo '<button type="button" class="btn btn-warning btn-abrir-assinatura" data-id="' . $o->idOs . '" title="Assinar OS"> <i class="bx bx-pencil"></i></button> ';
                            }

                            echo '<a href="' . base_url() . 'index.php/mine/visualizarOs/' . $o->idOs . '" class="btn" title="Visualizar"> <i class="fas fa-eye"></i></a> ';
                            echo '<a href="' . base_url('index.php/mine/imprimirOs/' . $o->idOs) . '" class="btn" target="_blank" title="Imprimir"> <i class="fas fa-print"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">Nenhuma ordem de serviço encontrada.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-signal"></i></span>
            <h5>Últimas Compras</h5>
        </div>
        <div class="widget-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Responsável</th>
                        <th>Data da Venda</th>
                        <th>Faturado</th>
                        <th>Venc. da Garantia</th>
                        <th>Status</th>
                        <th style="text-align:right">Visualizar / Imprimir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($compras != null) {
                        foreach ($compras as $c) {
                            $vencGarantia = '';

                            if ($c->garantia && is_numeric($c->garantia)) {
                                $vencGarantia = dateInterval($c->dataVenda, $c->garantia);
                            }
                            $corGarantia = '';
                            if (!empty($vencGarantia)) {
                                $dataGarantia = explode('/', $vencGarantia);
                                $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                if (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) {
                                    $corGarantia = '#4d9c79';
                                } else {
                                    $corGarantia = '#f24c6f';
                                }
                            } elseif ($c->garantia == "0") {
                                $vencGarantia = 'Sem Garantia';
                                $corGarantia = '';
                            } else {
                                $vencGarantia = '';
                                $corGarantia = '';
                            }
                            if ($c->faturado == 1) {
                                $faturado = 'Sim';
                            } else {
                                $faturado = 'Não';
                            }
                            
                            switch ($c->status) {
                                case 'Aberto':
                                    $cor = '#00cd00';
                                    break;
                                case 'Em Andamento':
                                    $cor = '#436eee';
                                    break;
                                case 'Orçamento':
                                    $cor = '#CDB380';
                                    break;
                                case 'Negociação':
                                    $cor = '#AEB404';
                                    break;
                                case 'Cancelado':
                                    $cor = '#CD0000';
                                    break;
                                case 'Finalizado':
                                    $cor = '#256';
                                    break;
                                case 'Faturado':
                                    $cor = '#B266FF';
                                    break;
                                case 'Aguardando Peças':
                                    $cor = '#FF7F00';
                                    break;
                                case 'Aprovado':
                                    $cor = '#808080';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                                    break;
                            }
                            echo '<tr>';
                            echo '<td>' . $c->idVendas . '</td>';
                            echo '<td>' . $c->nome . '</td>';
                            echo '<td>' . date('d/m/Y', strtotime($c->dataVenda)) . '</td>';
                            echo '<td>' . $faturado . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $c->status . '</span> </td>';
                            echo '<td style="text-align:right">';
                            echo '<a href="' . base_url() . 'index.php/mine/visualizarCompra/' . $c->idVendas . '" class="btn"> <i class="fas fa-eye" ></i> </a> ';
                            echo '<a href="' . base_url() . 'index.php/mine/imprimirCompra/' . $c->idVendas . '" class="btn"> <i class="fas fa-print" ></i> </a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">Nenhuma venda encontrada.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mini Modal de Assinatura usando os IDs nativos do assinaturas.js -->
<div id="modalAssinatura" class="modal-assinatura">
    <div class="modal-assinatura-content">
        <h3>Assinar Ordem de Serviço</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Desenhe sua assinatura abaixo:</p>
        
        <!-- O id="assCliente-pad" é o que o assinaturas.js procura automaticamente -->
        <canvas id="assCliente-pad" width="600" height="300"></canvas>
        
        <div style="margin-top: 15px;">
            <!-- O id="nomeAssinatura" também é capturado pelo script nativo -->
            <input type="text" name="nomeAssinatura" id="nomeAssinatura" placeholder="Nome e Sobrenome*" style="width: 90%; padding: 6px;" class="text-center">
        </div>
        
        <div style="margin-top: 15px;" class="buttons-a">
            <!-- Os botões com ids limparAssCliente e salvarAssCliente já possuem eventos bindados no assinaturas.js -->
            <button type="button" id="limparAssCliente" class="btn btn-danger">Limpar</button>
            <button type="button" id="salvarAssCliente" class="btn btn-success">Enviar Assinatura</button>
            <button type="button" id="fecharAssModal" class="btn">Cancelar</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Abrir Mini Modal
        $(document).on('click', '.btn-abrir-assinatura', function() {
            // Ao clicar no lápis, atualiza a variável global window.idOs 
            // para que o assinaturas.js saiba qual OS está sendo assinada
            window.idOs = $(this).data('id');
            $('#modalAssinatura').css('display', 'flex');
        });

        // Fechar Mini Modal ao clicar em Cancelar ou fora do modal
        $('#fecharAssModal, #modalAssinatura').on('click', function(e) {
            if (e.target === this) {
                $('#modalAssinatura').hide();
            }
        });

        // Monitora as requisições AJAX do assinaturas.js
        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    // Verifica se a requisição retornou sucesso e se é a de assinatura
                    if (response.success === true && response.message.includes('Assinatura')) {
                        
                        // Oculta o modal de assinatura imediatamente
                        $('#modalAssinatura').hide(); 
                        
                        // Recarrega a página automaticamente após 1,5 segundos 
                        // para atualizar o status e remover o botão de lápis da OS que acabou de ser assinada
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } catch (e) {
                    // Ignora se o retorno não for um JSON válido
                }
            }
        });

    });
</script>
