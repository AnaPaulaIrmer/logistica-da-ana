
<?php
session_start();
require_once "conexao.php";
$mensagem = $_SESSION['mensagem'] ?? "";
unset($_SESSION['mensagem']);

$secao_mensagem = $_SESSION['secao_mensagem'] ?? "";
unset($_SESSION['secao_mensagem']);

$produto_edicao = null;
$frete_edicao = null;
// CADASTRAR OU ATUALIZAR PRODUTO
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["cadastrar_produto"]) || isset($_POST["atualizar_produto"]))) {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $peso = floatval($_POST["peso"] ?? 0);
    $quantidade = intval($_POST["quantidade"] ?? 0);
    $valor = floatval($_POST["valor"] ?? 0);

    if ($nome == "") {

        $mensagem = "Informe o nome do produto.";

    } elseif (isset($_POST["atualizar_produto"])) {

        $id = intval($_POST["id"]);

        $sql = "UPDATE produtos
                SET nome=?, descricao=?, peso=?, quantidade=?, valor=?
                WHERE id=?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param(
            "ssdidi",
            $nome,
            $descricao,
            $peso,
            $quantidade,
            $valor,
            $id
        );

        if ($stmt->execute()) {
            $mensagem = "Produto atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar o produto.";
        }

        $stmt->close();

    } elseif (isset($_POST["cadastrar_produto"])) {

        $sql = "INSERT INTO produtos
                (nome, descricao, peso, quantidade, valor)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param(
            "ssdid",
            $nome,
            $descricao,
            $peso,
            $quantidade,
            $valor
        );

        if ($stmt->execute()) {
            $mensagem = "Produto cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar o produto.";
        }

        $stmt->close();
    }
}

// EXCLUIR PRODUTO
if (isset($_GET["excluir_produto"])) {

    $id = intval($_GET["excluir_produto"]);

    $verificar = $conexao->prepare(
        "SELECT id FROM frete_produtos WHERE produto_id = ?"
    );

    $verificar->bind_param("i", $id);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {

        $mensagem = "Não é possível excluir este produto, pois ele está vinculado a um frete.";

    } else {

        $stmt = $conexao->prepare(
            "DELETE FROM produtos WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensagem = "Produto excluído com sucesso!";
        } else {
            $mensagem = "Erro ao excluir o produto.";
        }

        $stmt->close();
    }

    $verificar->close();
    $secao_mensagem = "produtos";
}

// CADASTRAR TRANSPORTADOR
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_transportador"])) {

    $nome = trim($_POST["nome"] ?? "");
    $cnpj = trim($_POST["cnpj"] ?? "");
    $responsavel = trim($_POST["responsavel"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");
    $estado = trim($_POST["estado"] ?? "");

    $sql = "INSERT INTO transportadores
            (nome, cnpj, responsavel, telefone, email, cidade, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssss",
        $nome,
        $cnpj,
        $responsavel,
        $telefone,
        $email,
        $cidade,
        $estado
    );

    if ($stmt->execute()) {
        $mensagem = "Transportador cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar transportador.";
    }

    $stmt->close();
}
// ATUALIZAR TRANSPORTADOR
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["atualizar_transportador"])) {

    $id = intval($_POST["id_transportador"]);
    $nome = trim($_POST["nome"] ?? "");
    $cnpj = trim($_POST["cnpj"] ?? "");
    $responsavel = trim($_POST["responsavel"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");
    $estado = trim($_POST["estado"] ?? "");

    $sql = "UPDATE transportadores
            SET nome=?, cnpj=?, responsavel=?, telefone=?, email=?, cidade=?, estado=?
            WHERE id=?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssssi",
        $nome,
        $cnpj,
        $responsavel,
        $telefone,
        $email,
        $cidade,
        $estado,
        $id
    );

    if ($stmt->execute()) {
        $mensagem = "Transportador atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar o transportador.";
    }

    $stmt->close();
}
// ATUALIZAR ENDEREÇO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["atualizar_endereco"])) {

    $id = intval($_POST["id_endereco"]);

    $destinatario = trim($_POST["destinatario"] ?? "");
    $cep = trim($_POST["cep"] ?? "");
    $rua = trim($_POST["rua"] ?? "");
    $numero = trim($_POST["numero"] ?? "");
    $bairro = trim($_POST["bairro"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");
    $estado = trim($_POST["estado"] ?? "");

    $sql = "UPDATE enderecos
            SET destinatario=?, cep=?, rua=?, numero=?,
                bairro=?, cidade=?, estado=?
            WHERE id=?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssssi",
        $destinatario,
        $cep,
        $rua,
        $numero,
        $bairro,
        $cidade,
        $estado,
        $id
    );

    if ($stmt->execute()) {
        $mensagem = "Endereço atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar o endereço.";
    }

    $stmt->close();
}
// EXCLUIR ENDEREÇO
if (isset($_GET["excluir_endereco"])) {

    $id = intval($_GET["excluir_endereco"]);

    $stmt = $conexao->prepare(
        "DELETE FROM enderecos WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensagem = "Endereço excluído com sucesso!";
    } else {
        $mensagem = "Erro ao excluir o endereço.";
    }

    $stmt->close();
    $secao_mensagem = "enderecos";
}
// CADASTRAR ENDEREÇO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_endereco"])) {

    $destinatario = trim($_POST["destinatario"] ?? "");
    $cep = trim($_POST["cep"] ?? "");
    $rua = trim($_POST["rua"] ?? "");
    $numero = trim($_POST["numero"] ?? "");
    $bairro = trim($_POST["bairro"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");
    $estado = trim($_POST["estado"] ?? "");

    $sql = "INSERT INTO enderecos
            (destinatario, cep, rua, numero, bairro, cidade, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssss",
        $destinatario,
        $cep,
        $rua,
        $numero,
        $bairro,
        $cidade,
        $estado
    );

    if ($stmt->execute()) {
        $mensagem = "Endereço cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar endereço.";
    }

    $stmt->close();
}
// ATUALIZAR FRETE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["atualizar_frete"])) {

    $id = intval($_POST["frete_id"]);
    $codigo = trim($_POST["codigo"] ?? "");
    $origem = intval($_POST["endereco_origem"] ?? 0);
    $destino = intval($_POST["endereco_destino"] ?? 0);
    $transportador = intval($_POST["transportador_id"] ?? 0);
    $data_envio = $_POST["data_envio"] ?? "";
    $previsao_entrega = $_POST["previsao_entrega"] ?? "";
    $valor_frete = floatval($_POST["valor"] ?? 0);

    $conexao->begin_transaction();

    try {

        $sql = "UPDATE fretes SET
                codigo = ?,
                endereco_origem = ?,
                endereco_destino = ?,
                transportador_id = ?,
                data_envio = ?,
                previsao_entrega = ?,
                valor = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "siiissdi",
            $codigo,
            $origem,
            $destino,
            $transportador,
            $data_envio,
            $previsao_entrega,
            $valor_frete,
            $id
        );

        $stmt->execute();
        $stmt->close();

        // ATUALIZAR PRODUTOS VINCULADOS
        $stmt_produtos = $conexao->prepare(
            "DELETE FROM frete_produtos WHERE frete_id = ?"
        );

        $stmt_produtos->bind_param("i", $id);
        $stmt_produtos->execute();
        $stmt_produtos->close();

        $sql_produto = "INSERT INTO frete_produtos
                        (frete_id, produto_id, quantidade)
                        VALUES (?, ?, ?)";

        $stmt_produto = $conexao->prepare($sql_produto);

        foreach ($_POST["produtos"] as $produto_id) {

            $produto_id = intval($produto_id);
            $quantidade_produto = 1;

            $stmt_produto->bind_param(
                "iii",
                $id,
                $produto_id,
                $quantidade_produto
            );

            $stmt_produto->execute();
        }

        $stmt_produto->close();

        $conexao->commit();

        $mensagem = "Frete atualizado com sucesso!";

    } catch (Exception $e) {

        $conexao->rollback();

        $mensagem = "Erro ao atualizar o frete: " . $e->getMessage();
    }
}
// CADASTRAR FRETE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_frete"])) {

    $codigo = trim($_POST["codigo"] ?? "");
    $origem = intval($_POST["endereco_origem"] ?? 0);
    $destino = intval($_POST["endereco_destino"] ?? 0);
    $transportador = intval($_POST["transportador_id"] ?? 0);
    $data_envio = $_POST["data_envio"] ?? "";
    $previsao_entrega = $_POST["previsao_entrega"] ?? "";
    $valor_frete = floatval($_POST["valor"] ?? 0);
    $status = "Pendente";

    $conexao->begin_transaction();

    try {

        $sql = "INSERT INTO fretes
                (codigo, endereco_origem, endereco_destino,
                transportador_id, data_envio, previsao_entrega, valor, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "siiissds",
            $codigo,
            $origem,
            $destino,
            $transportador,
            $data_envio,
            $previsao_entrega,
            $valor_frete,
            $status
        );

        $stmt->execute();

        $frete_id = $conexao->insert_id;

        $stmt->close();

        // VINCULAR PRODUTOS AO FRETE
        $sql_produto = "INSERT INTO frete_produtos
                        (frete_id, produto_id, quantidade)
                        VALUES (?, ?, ?)";

        $stmt_produto = $conexao->prepare($sql_produto);

        foreach ($_POST["produtos"] as $produto_id) {

            $produto_id = intval($produto_id);
            $quantidade_produto = 1;

            $stmt_produto->bind_param(
                "iii",
                $frete_id,
                $produto_id,
                $quantidade_produto
            );

            $stmt_produto->execute();
        }

        $stmt_produto->close();

        $conexao->commit();

        $mensagem = "Frete cadastrado com sucesso!";

    } catch (Exception $e) {

        $conexao->rollback();

        $mensagem = "Erro ao cadastrar o frete: " . $e->getMessage();
    }
}
// CARREGAR FRETE PARA EDIÇÃO
if (isset($_GET["editar_frete"])) {

    $id = intval($_GET["editar_frete"]);

    $stmt = $conexao->prepare(
        "SELECT * FROM fretes WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $frete_edicao = $resultado->fetch_assoc();

    $stmt->close();
}
// EXCLUIR FRETE
if (isset($_GET["excluir_frete"])) {

    $id = intval($_GET["excluir_frete"]);

    $conexao->begin_transaction();

    try {

        // Excluir produtos vinculados ao frete
        $stmt_produtos = $conexao->prepare(
            "DELETE FROM frete_produtos WHERE frete_id = ?"
        );

        $stmt_produtos->bind_param("i", $id);
        $stmt_produtos->execute();
        $stmt_produtos->close();

        // Excluir o frete
        $stmt_frete = $conexao->prepare(
            "DELETE FROM fretes WHERE id = ?"
        );

        $stmt_frete->bind_param("i", $id);
        $stmt_frete->execute();
        $stmt_frete->close();

        $conexao->commit();

        $mensagem = "Frete excluído com sucesso!";

    } catch (Exception $e) {

        $conexao->rollback();

        $mensagem = "Erro ao excluir o frete: " . $e->getMessage();
    }
    $secao_mensagem = "fretes";
}
// EVITAR REENVIO DOS FORMULÁRIOS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($mensagem != "") {
    $_SESSION['mensagem'] = $mensagem;

    if (isset($_POST['cadastrar_produto']) || isset($_POST['atualizar_produto'])) {
        $_SESSION['secao_mensagem'] = "produtos";
    } elseif (isset($_POST['cadastrar_endereco']) || isset($_POST['atualizar_endereco'])) {
        $_SESSION['secao_mensagem'] = "enderecos";
    } elseif (isset($_POST['cadastrar_transportador']) || isset($_POST['atualizar_transportador'])) {
        $_SESSION['secao_mensagem'] = "transportadores";
    } elseif (isset($_POST['cadastrar_frete']) || isset($_POST['atualizar_frete'])) {
        $_SESSION['secao_mensagem'] = "fretes";
    }
}

   $destino = "index.php";

if (isset($_POST['cadastrar_produto']) || isset($_POST['atualizar_produto'])) {
    $destino .= "#produtos";
} elseif (isset($_POST['cadastrar_endereco']) || isset($_POST['atualizar_endereco'])) {
    $destino .= "#enderecos";
} elseif (isset($_POST['cadastrar_transportador']) || isset($_POST['atualizar_transportador'])) {
    $destino .= "#transportadores";
} elseif (isset($_POST['cadastrar_frete']) || isset($_POST['atualizar_frete'])) {
    $destino .= "#fretes";
}

header("Location: " . $destino);
    exit;
}
// LISTAR ENDEREÇOS CADASTRADOS
$sql_enderecos = "SELECT * FROM enderecos ORDER BY id DESC";
$resultado_enderecos = $conexao->query($sql_enderecos);
// LISTAR TRANSPORTADORES CADASTRADOS
$sql_transportadores = "SELECT * FROM transportadores ORDER BY id DESC";
$resultado_transportadores = $conexao->query($sql_transportadores);
// LISTAR PRODUTOS PARA OS FRETES
$sql_produtos_frete = "SELECT * FROM produtos ORDER BY nome ASC";
$resultado_produtos_frete = $conexao->query($sql_produtos_frete);
// BUSCAR ENDEREÇO PARA EDIÇÃO
if (isset($_GET["editar_endereco"])) {

    $id = intval($_GET["editar_endereco"]);

    $stmt = $conexao->prepare(
        "SELECT * FROM enderecos WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_endereco_edicao = $stmt->get_result();
    $endereco_edicao = $resultado_endereco_edicao->fetch_assoc();

    $stmt->close();
}
// BUSCAR TRANSPORTADOR PARA EDIÇÃO
if (isset($_GET["editar_transportador"])) {

    $id = intval($_GET["editar_transportador"]);

    $stmt = $conexao->prepare(
        "SELECT * FROM transportadores WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_transportador_edicao = $stmt->get_result();
    $transportador_edicao = $resultado_transportador_edicao->fetch_assoc();

    $stmt->close();
}
// EXCLUIR TRANSPORTADOR
if (isset($_GET["excluir_transportador"])) {

    $id = intval($_GET["excluir_transportador"]);

    $stmt = $conexao->prepare(
        "DELETE FROM transportadores WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensagem = "Transportador excluído com sucesso!";
    } else {
        $mensagem = "Erro ao excluir o transportador.";
    }

    $stmt->close();
    $secao_mensagem = "transportadores";
}
// BUSCAR PRODUTO PARA EDIÇÃO
if (isset($_GET["editar_produto"])) {

    $id = intval($_GET["editar_produto"]);

    $stmt = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_edicao = $stmt->get_result();
    $produto_edicao = $resultado_edicao->fetch_assoc();

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logística da Ana</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Menu lateral -->
    <aside class="menu-lateral">

        <h2>Logística da Ana</h2>

        <nav>
            <a href="#inicio">Início</a>
            <a href="#produtos">Produtos</a>
            <a href="#enderecos">Endereços</a>
            <a href="#transportadores">Transportadores</a>
            <a href="#fretes">Fretes</a>
            <a href="#relatorios">Relatórios</a>
        </nav>

    </aside>

    <!-- Conteúdo principal -->
    <main class="conteudo">

        <header>
            <h1>Bem-vinda à Logística da Ana!</h1>
            <p>Gerencie suas operações logísticas.</p>
        </header>
        <?php if (!empty($mensagem)) { ?>
    <div class="alert alert-info mt-3">
        <?php echo htmlspecialchars($mensagem); ?>
    </div>
<?php } ?>

       <!-- Início -->
<section id="inicio" class="inicio">

    <div class="text-center mb-4">
        <h2>Bem-vinda ao sistema Logística da Ana!</h2>
        <p>Gerencie seus produtos, entregas e transportadores.</p>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h3>📦 Produtos</h3>
                <p>Gerencie seus produtos cadastrados.</p>
                <a href="#produtos" class="btn btn-primary">Acessar</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h3>📍 Endereços</h3>
                <p>Organize origens e destinos.</p>
                <a href="#enderecos" class="btn btn-primary">Acessar</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h3>🚚 Transportadores</h3>
                <p>Consulte seus transportadores.</p>
                <a href="#transportadores" class="btn btn-primary">Acessar</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h3>🧾 Fretes</h3>
                <p>Gerencie suas entregas.</p>
                <a href="#fretes" class="btn btn-primary">Acessar</a>
            </div>
        </div>

    </div>

</section>

        <!-- Produtos -->
<section id="produtos">

    <h2>📦 Cadastro de Produtos</h2>
       <?php if (!empty($mensagem) && $secao_mensagem == "produtos") { ?>
        <div class="alert alert-info mt-3">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php } ?>
    

    <div class="card p-4 mt-4">

       <form method="POST" action="index.php#produtos">
            <?php if ($produto_edicao != null) { ?>
    <input type="hidden" name="id"
        value="<?php echo $produto_edicao['id']; ?>">
<?php } ?>

           <div class="mb-3">
    <label class="form-label">Nome do Produto</label>
  <input type="text" name="nome" class="form-control"
    placeholder="Digite o nome do produto"
    value="<?php echo htmlspecialchars($produto_edicao['nome'] ?? ''); ?>"
    required>
</div>

           <div class="mb-3">
    <label class="form-label">Descrição do Produto</label>
    <textarea name="descricao" class="form-control" rows="3"
    placeholder="Descreva o produto"><?php
    echo htmlspecialchars($produto_edicao['descricao'] ?? '');
?></textarea>
</div>

           <div class="row">

    <div class="col-md-4 mb-3">
        <label class="form-label">Peso (Kg)</label>
        <input type="number" name="peso" class="form-control"
    step="0.01" placeholder="Ex.: 2"
    value="<?php echo htmlspecialchars($produto_edicao['peso'] ?? ''); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Quantidade</label>
        <input type="number" name="quantidade" class="form-control"
    placeholder="Ex.: 10"
    value="<?php echo htmlspecialchars($produto_edicao['quantidade'] ?? ''); ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Valor (R$)</label>
        <input type="number" name="valor" class="form-control"
    step="0.01" placeholder="Ex.: 50,00"
    value="<?php echo htmlspecialchars($produto_edicao['valor'] ?? ''); ?>">
    </div>

</div>

            </div>

            <button type="submit"
    name="<?php echo $produto_edicao != null ? 'atualizar_produto' : 'cadastrar_produto'; ?>"
    class="btn btn-primary">
    <?php echo $produto_edicao != null ? 'Atualizar Produto' : 'Cadastrar'; ?>
</button>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>

        </form>

    </div>
<!-- Tabela de produtos cadastrados -->
<div class="card p-4 mt-4">

    <h3 class="mb-3">Produtos Cadastrados</h3>

    <div class="table-responsive">

        <table class="table table-striped table-hover">

            <thead class="table-dark">
                <tr>
                    <th>Produto</th>
                    <th>Peso (Kg)</th>
                    <th>Quantidade</th>
                    <th>Valor (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <tbody>

    <?php
    $resultado = $conexao->query("SELECT * FROM produtos ORDER BY id DESC");

    while ($produto = $resultado->fetch_assoc()) {
    ?>

        <tr>
            <td><?php echo htmlspecialchars($produto["nome"]); ?></td>
            <td><?php echo htmlspecialchars($produto["peso"]); ?> Kg</td>
            <td><?php echo htmlspecialchars($produto["quantidade"]); ?></td>
            <td>R$ <?php echo number_format($produto["valor"], 2, ",", "."); ?></td>
            <td>
    <a href="?editar_produto=<?php echo $produto['id']; ?>#produtos"
   class="btn btn-sm btn-warning">Editar</a>

<a href="?excluir_produto=<?php echo $produto['id']; ?>#produtos"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Tem certeza que deseja excluir este produto?')">
    Excluir
</a>
</td>
        </tr>

    <?php
    }
    ?>

</tbody>
            </tbody>

        </table>

    </div>

</div>
</section>

       <!-- Endereços -->
<section id="enderecos">

    <h2>📍 Cadastro de Endereços</h2>
        <?php if (!empty($mensagem) && $secao_mensagem == "enderecos") { ?>
        <div class="alert alert-info mt-3">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php } ?>
    
    <div class="card p-4 mt-4">

        <form method="POST" action="index.php#enderecos">
            <?php if (isset($endereco_edicao) && $endereco_edicao != null) { ?>
    <input type="hidden" name="id_endereco"
        value="<?php echo $endereco_edicao['id']; ?>">
<?php } ?>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome do Destinatário</label>
                   <input type="text" name="destinatario" class="form-control"
    placeholder="Digite o nome do destinatário"
    value="<?php echo htmlspecialchars($endereco_edicao['destinatario'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">CEP</label>
                  <input type="text" name="cep" class="form-control"
    placeholder="Digite o CEP"
    value="<?php echo htmlspecialchars($endereco_edicao['cep'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Rua</label>
                   <input type="text" name="rua" class="form-control"
    placeholder="Digite o nome da rua"
    value="<?php echo htmlspecialchars($endereco_edicao['rua'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Número</label>
                  <input type="text" name="numero" class="form-control"
    placeholder="Digite o número"
    value="<?php echo htmlspecialchars($endereco_edicao['numero'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Bairro</label>
                   <input type="text" name="bairro" class="form-control"
    placeholder="Digite o bairro"
    value="<?php echo htmlspecialchars($endereco_edicao['bairro'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Cidade</label>
                 <input type="text" name="cidade" class="form-control"
    placeholder="Digite a cidade"
    value="<?php echo htmlspecialchars($endereco_edicao['cidade'] ?? ''); ?>"> 
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado (UF)</label>
                    <select name="estado" class="form-select">
    <option value="">Selecione o estado</option>

    <option value="PR" <?php echo (($endereco_edicao['estado'] ?? '') == 'PR') ? 'selected' : ''; ?>>PR</option>

    <option value="SP" <?php echo (($endereco_edicao['estado'] ?? '') == 'SP') ? 'selected' : ''; ?>>SP</option>

    <option value="SC" <?php echo (($endereco_edicao['estado'] ?? '') == 'SC') ? 'selected' : ''; ?>>SC</option>

    <option value="RS" <?php echo (($endereco_edicao['estado'] ?? '') == 'RS') ? 'selected' : ''; ?>>RS</option>

    <option value="MS" <?php echo (($endereco_edicao['estado'] ?? '') == 'MS') ? 'selected' : ''; ?>>MS</option>
</select>
                </div>

            </div>

           <button type="submit"
    name="<?php echo isset($endereco_edicao) && $endereco_edicao != null ? 'atualizar_endereco' : 'cadastrar_endereco'; ?>"
    class="btn btn-primary">

    <?php echo isset($endereco_edicao) && $endereco_edicao != null ? 'Atualizar Endereço' : 'Cadastrar'; ?>

</button>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>

        </form>

    </div>
<!-- Endereços cadastrados -->
<div class="card p-4 mt-4">

    <h3 class="mb-3">Endereços Cadastrados</h3>

    <div class="table-responsive">

        <table class="table table-striped table-hover">

            <thead class="table-dark">
                <tr>
                    <th>Destinatário</th>
                    <th>CEP</th>
                    <th>Rua</th>
                    <th>Nº</th>
                    <th>Bairro</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
    <?php while ($endereco = $resultado_enderecos->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($endereco['destinatario']); ?></td>
            <td><?php echo htmlspecialchars($endereco['cep']); ?></td>
            <td><?php echo htmlspecialchars($endereco['rua']); ?></td>
            <td><?php echo htmlspecialchars($endereco['numero']); ?></td>
            <td><?php echo htmlspecialchars($endereco['bairro']); ?></td>
            <td><?php echo htmlspecialchars($endereco['cidade']); ?></td>
            <td><?php echo htmlspecialchars($endereco['estado']); ?></td>
           <td>
    <a href="?editar_endereco=<?php echo $endereco['id']; ?>#enderecos"
       class="btn btn-sm btn-warning">✏️ Editar</a>
       <a href="?excluir_endereco=<?php echo $endereco['id']; ?>#enderecos"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Tem certeza que deseja excluir este endereço?');">
   🗑️ Excluir
</a>
</td>
        </tr>
    <?php } ?>
</tbody>

        </table>

    </div>

</div>
</section>

       <!-- Transportadores -->
<section id="transportadores">

    <h2>🚚 Cadastro de Transportadores</h2>
        <?php if (!empty($mensagem) && $secao_mensagem == "transportadores") { ?>
        <div class="alert alert-info mt-3">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php } ?>

    <div class="card p-4 mt-4">

        <form method="POST" action="index.php#transportadores">
            <?php if (isset($transportador_edicao) && $transportador_edicao != null) { ?>
    <input type="hidden" name="id_transportador"
        value="<?php echo $transportador_edicao['id']; ?>">
<?php } ?>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome da Transportadora</label>
                   <input type="text" name="nome" class="form-control"
    placeholder="Digite o nome da transportadora"
    value="<?php echo htmlspecialchars($transportador_edicao['nome'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">CNPJ</label>
                    <input type="text" name="cnpj" class="form-control"
    placeholder="Digite o CNPJ"
    value="<?php echo htmlspecialchars($transportador_edicao['cnpj'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome do Responsável</label>
                    <input type="text" name="responsavel" class="form-control"
    placeholder="Digite o nome do responsável"
    value="<?php echo htmlspecialchars($transportador_edicao['responsavel'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control"
    placeholder="Digite o telefone"
    value="<?php echo htmlspecialchars($transportador_edicao['telefone'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control"
    placeholder="Digite o e-mail"
    value="<?php echo htmlspecialchars($transportador_edicao['email'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Cidade</label>
                   <input type="text" name="cidade" class="form-control"
    placeholder="Digite a cidade"
    value="<?php echo htmlspecialchars($transportador_edicao['cidade'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado (UF)</label>
                    <select name="estado" class="form-select">
    <option value="">Selecione o estado</option>
    <option value="PR" <?php echo (($transportador_edicao['estado'] ?? '') == 'PR') ? 'selected' : ''; ?>>PR</option>
    <option value="SP" <?php echo (($transportador_edicao['estado'] ?? '') == 'SP') ? 'selected' : ''; ?>>SP</option>
    <option value="SC" <?php echo (($transportador_edicao['estado'] ?? '') == 'SC') ? 'selected' : ''; ?>>SC</option>
    <option value="RS" <?php echo (($transportador_edicao['estado'] ?? '') == 'RS') ? 'selected' : ''; ?>>RS</option>
    <option value="MS" <?php echo (($transportador_edicao['estado'] ?? '') == 'MS') ? 'selected' : ''; ?>>MS</option>
</select>
                </div>

            </div>

           <button type="submit"
    name="<?php echo isset($transportador_edicao) && $transportador_edicao != null ? 'atualizar_transportador' : 'cadastrar_transportador'; ?>"
    class="btn btn-primary">
    <?php echo isset($transportador_edicao) && $transportador_edicao != null ? 'Atualizar Transportador' : 'Cadastrar'; ?>
</button>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>

        </form>

    </div>
<!-- Transportadores cadastrados -->
<div class="card p-4 mt-4">

    <h3 class="mb-3">Transportadores Cadastrados</h3>

    <div class="table-responsive">

        <table class="table table-striped table-hover">

            <thead class="table-dark">
                <tr>
                    <th>Transportadora</th>
                    <th>CNPJ</th>
                    <th>Responsável</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
    <?php while ($transportador = $resultado_transportadores->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($transportador['nome']); ?></td>
            <td><?php echo htmlspecialchars($transportador['cnpj']); ?></td>
            <td><?php echo htmlspecialchars($transportador['responsavel']); ?></td>
            <td><?php echo htmlspecialchars($transportador['telefone']); ?></td>
            <td><?php echo htmlspecialchars($transportador['email']); ?></td>
            <td><?php echo htmlspecialchars($transportador['cidade']); ?></td>
            <td><?php echo htmlspecialchars($transportador['estado']); ?></td>
            <td>
    <a href="?editar_transportador=<?php echo $transportador['id']; ?>#transportadores"
       class="btn btn-sm btn-warning">✏️ Editar</a>

    <a href="?excluir_transportador=<?php echo $transportador['id']; ?>#transportadores"
       class="btn btn-sm btn-danger"
       onclick="return confirm('Tem certeza que deseja excluir este transportador?');">
       🗑️ Excluir
    </a>
</td>
        </tr>
    <?php } ?>
</tbody>

        </table>

    </div>

</div>
</section>

      <!-- Fretes -->
<section id="fretes">

    <h2>🚚 Cadastro de Fretes</h2>
   <?php if (!empty($mensagem) && $secao_mensagem == "fretes") { ?>
    <div class="alert alert-info mt-3">
        <?php echo htmlspecialchars($mensagem); ?>
    </div>
<?php } ?>
    

    <div class="card p-4 mt-4">

       <form method="POST" action="index.php#fretes">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Código do Frete</label>
                  <input type="text" name="codigo" class="form-control"
    placeholder="Ex.: FR001"
    value="<?php echo htmlspecialchars($frete_edicao['codigo'] ?? ''); ?>"
    required>
                </div>

              
<div class="col-md-6 mb-3">
    <label class="form-label">Produto(s)</label>

    <div id="lista-produtos">
        <select name="produtos[]" class="form-select mb-2" multiple required>
            <?php
            $resultado_produtos_frete = $conexao->query(
                "SELECT * FROM produtos ORDER BY nome ASC"
            );

            while ($produto = $resultado_produtos_frete->fetch_assoc()) {
                $selecionado = '';

                if (!empty($frete_edicao)) {
                    $stmt_produto_selecionado = $conexao->prepare(
                        "SELECT produto_id FROM frete_produtos WHERE frete_id = ?"
                    );
                    $stmt_produto_selecionado->bind_param("i", $frete_edicao['id']);
                    $stmt_produto_selecionado->execute();
                    $produtos_vinculados = $stmt_produto_selecionado->get_result()->fetch_all(MYSQLI_ASSOC);
                    $stmt_produto_selecionado->close();

                    $ids_produtos = array_column($produtos_vinculados, 'produto_id');

                    if (in_array($produto['id'], $ids_produtos)) {
                        $selecionado = 'selected';
                    }
                }
            ?>
                <option value="<?php echo $produto['id']; ?>" <?php echo $selecionado; ?>>
                    <?php echo htmlspecialchars($produto['nome']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <button type="button" class="btn btn-outline-primary btn-sm"
        id="adicionar-produto">
        + Adicionar outra seleção
    </button>
</div>


               <div class="col-md-6 mb-3">
    <label class="form-label">Endereço de Origem</label>
    <select name="endereco_origem" class="form-select" required>
        <option value="">Selecione o endereço de origem</option>

        <?php
        $enderecos_origem = $conexao->query("SELECT * FROM enderecos ORDER BY destinatario ASC");

        while ($endereco = $enderecos_origem->fetch_assoc()) {
        ?>
            <option value="<?php echo $endereco['id']; ?>"
    <?php
    if (
        isset($frete_edicao['endereco_origem']) &&
        $frete_edicao['endereco_origem'] == $endereco['id']
    ) {
        echo 'selected';
    }
    ?>>
                <?php echo htmlspecialchars($endereco['destinatario'] . ' - ' . $endereco['cidade'] . '/' . $endereco['estado']); ?>
            </option>
        <?php } ?>
    </select>
</div>

                <div class="col-md-6 mb-3">
    <label class="form-label">Endereço de Destino</label>
    <select name="endereco_destino" class="form-select" required>
        <option value="">Selecione o endereço de destino</option>

        <?php
        $enderecos_destino = $conexao->query("SELECT * FROM enderecos ORDER BY destinatario ASC");

        while ($endereco = $enderecos_destino->fetch_assoc()) {
        ?>
            <option value="<?php echo $endereco['id']; ?>"
    <?php
    if (
        isset($frete_edicao['endereco_destino']) &&
        $frete_edicao['endereco_destino'] == $endereco['id']
    ) {
        echo 'selected';
    }
    ?>>
                <?php echo htmlspecialchars($endereco['destinatario'] . ' - ' . $endereco['cidade'] . '/' . $endereco['estado']); ?>
            </option>
        <?php } ?>
    </select>
</div>

               <div class="col-md-6 mb-3">
    <label class="form-label">Transportadora</label>
    <select name="transportador_id" class="form-select" required>
        <option value="">Selecione a transportadora</option>

        <?php
        $transportadores_frete = $conexao->query("SELECT * FROM transportadores ORDER BY nome ASC");

        while ($transportador = $transportadores_frete->fetch_assoc()) {
        ?>
            <option value="<?php echo $transportador['id']; ?>"
    <?php
    if (
        isset($frete_edicao['transportador_id']) &&
        $frete_edicao['transportador_id'] == $transportador['id']
    ) {
        echo 'selected';
    }
    ?>>
                <?php echo htmlspecialchars($transportador['nome']); ?>
            </option>
        <?php } ?>
    </select>
</div>

               <div class="col-md-6 mb-3">
    <label class="form-label">Data de Envio</label>
    <input type="date" name="data_envio" class="form-control"
    value="<?php echo htmlspecialchars($frete_edicao['data_envio'] ?? ''); ?>"
    required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Previsão de Entrega</label>
    <input type="date" name="previsao_entrega" class="form-control"
    value="<?php echo htmlspecialchars($frete_edicao['previsao_entrega'] ?? ''); ?>"
    required>
</div>

               <div class="col-md-6 mb-3">
    <label class="form-label">Valor do Frete (R$)</label>
    <input type="number" name="valor" class="form-control"
    step="0.01" min="0" placeholder="Ex.: 150,00"
    value="<?php echo htmlspecialchars($frete_edicao['valor'] ?? ''); ?>"
    required>
</div>

            </div>
            <?php if (!empty($frete_edicao)) { ?>
    <input type="hidden" name="frete_id"
        value="<?php echo $frete_edicao['id']; ?>">
<?php } ?>

          <button type="submit"
    name="<?php echo !empty($frete_edicao) ? 'atualizar_frete' : 'cadastrar_frete'; ?>"
    class="btn btn-primary">
    <?php echo !empty($frete_edicao) ? 'Atualizar Frete' : 'Cadastrar'; ?>
</button>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>

        </form>

    </div>
<!-- Fretes cadastrados -->
<div class="card p-4 mt-4">

    <h3 class="mb-3">Fretes Cadastrados</h3>

    <div class="table-responsive">

        <table class="table table-striped table-hover">

            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Transportadora</th>
                    <th>Data de Envio</th>
                    <th>Previsão de Entrega</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>

          <tbody>
    <?php
    $sql_fretes = "SELECT
        f.*,
        o.cidade AS cidade_origem,
        o.estado AS estado_origem,
        d.cidade AS cidade_destino,
        d.estado AS estado_destino,
        t.nome AS nome_transportador,
        GROUP_CONCAT(p.nome SEPARATOR ', ') AS nomes_produtos

        FROM fretes f

        LEFT JOIN enderecos o ON f.endereco_origem = o.id
        LEFT JOIN enderecos d ON f.endereco_destino = d.id
        LEFT JOIN transportadores t ON f.transportador_id = t.id
        LEFT JOIN frete_produtos fp ON f.id = fp.frete_id
        LEFT JOIN produtos p ON fp.produto_id = p.id

        GROUP BY f.id
        ORDER BY f.id DESC";

    $resultado_fretes = $conexao->query($sql_fretes);

    while ($frete = $resultado_fretes->fetch_assoc()) {
    ?>

        <tr>
            <td><?php echo htmlspecialchars($frete['codigo']); ?></td>
            <td><?php echo htmlspecialchars($frete['nomes_produtos'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($frete['cidade_origem'] . '/' . $frete['estado_origem']); ?></td>
            <td><?php echo htmlspecialchars($frete['cidade_destino'] . '/' . $frete['estado_destino']); ?></td>
            <td><?php echo htmlspecialchars($frete['nome_transportador']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($frete['data_envio'])); ?></td>
            <td><?php echo date('d/m/Y', strtotime($frete['previsao_entrega'])); ?></td>
            <td>R$ <?php echo number_format($frete['valor'], 2, ',', '.'); ?></td>
            <td>
    <a href="?editar_frete=<?php echo $frete['id']; ?>#fretes"
   class="btn btn-sm btn-warning">
   ✏️ Editar
</a>

<a href="?excluir_frete=<?php echo $frete['id']; ?>#fretes"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Tem certeza que deseja excluir este frete?');">
   🗑️ Excluir
</a>
</td>
        </tr>

    <?php } ?>
</tbody>

        </table>

    </div>

</div>
</section>

      
<!-- Relatórios -->
<section id="relatorios">

    <h2>📊 Relatórios</h2>

    <div class="card p-4 mt-4">

        <h3 class="mb-4">Resumo dos Fretes</h3>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="card p-3 text-center h-100">
                    <h5>Total de Fretes</h5>
                    <h2 class="text-primary">01</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 text-center h-100">
                    <h5>Fretes em Andamento</h5>
                    <h2 class="text-warning">00</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 text-center h-100">
                    <h5>Fretes Entregues</h5>
                    <h2 class="text-success">01</h2>
                </div>
            </div>

        </div>

    </div>

    <!-- Filtro do relatório -->
    <div class="card p-4 mt-4">

        <h3 class="mb-3">Filtrar Relatório</h3>

     <form method="GET" action="index.php#relatorios">

    <div class="row align-items-end">

       <div class="col-md-6 mb-3">
    <label class="form-label">Data do Relatório</label>
    <input type="date" name="data_relatorio"
        class="form-control"
        value="<?= htmlspecialchars($_GET['data_relatorio'] ?? '') ?>"
        required>
</div>

        <div class="col-md-6 mb-3">
            <button type="submit" name="filtrar_relatorio"
                class="btn btn-primary">
                Filtrar
            </button>

           <button type="submit" name="baixar_relatorio"
    formaction="relatorio.php"
    class="btn btn-success">
                Baixar Relatório
            </button>
        </div>

    </div>

</form>
    </div>

    <!-- Tabela de relatórios -->
    <div class="card p-4 mt-4">

        <h3 class="mb-3">Fretes do Relatório</h3>

        <div class="table-responsive">

            <table class="table table-striped table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Transportadora</th>
                        <th>Status</th>
                        <th>Última Atualização</th>
                    </tr>
                </thead>

               <tbody>
    <?php
    if (!empty($_GET["data_relatorio"])) {

        $data_relatorio = $_GET["data_relatorio"];

        $sql_relatorio = "SELECT
            f.codigo,
            GROUP_CONCAT(p.nome SEPARATOR ', ') AS produtos,
            t.nome AS transportadora,
            f.status,
            f.data_envio

            FROM fretes f

            LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
            LEFT JOIN produtos p ON p.id = fp.produto_id
            LEFT JOIN transportadores t ON t.id = f.transportador_id

            WHERE f.data_envio = ?

            GROUP BY f.id
            ORDER BY f.id DESC";

        $stmt_relatorio = $conexao->prepare($sql_relatorio);
        $stmt_relatorio->bind_param("s", $data_relatorio);
        $stmt_relatorio->execute();

        $resultado_relatorio = $stmt_relatorio->get_result();

        while ($frete = $resultado_relatorio->fetch_assoc()) {
    ?>
            <tr>
                <td><?php echo htmlspecialchars($frete["codigo"]); ?></td>
                <td><?php echo htmlspecialchars($frete["produtos"]); ?></td>
                <td><?php echo htmlspecialchars($frete["transportadora"]); ?></td>
                <td><?php echo htmlspecialchars($frete["status"]); ?></td>
                <td><?php echo date("d/m/Y", strtotime($frete["data_envio"])); ?></td>
            </tr>
    <?php
        }

        $stmt_relatorio->close();
    }
    ?>
</tbody>

            </table>

        </div>

    </div>

</section>


    </main>

    <!-- JavaScript -->
    <script src="/logistica_da_ana/js/script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
