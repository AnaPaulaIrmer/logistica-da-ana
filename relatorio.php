<?php
require_once "conexao.php";
if (empty($_GET["data_relatorio"])) {
    die("Selecione uma data para gerar o relatório.");
}

$data = $_GET["data_relatorio"];
$sql = "SELECT
    f.codigo,
    p.nome AS produto,
    o.cidade AS origem,
    d.cidade AS destino,
    t.nome AS transportadora,
    f.data_envio,
    f.previsao_entrega,
    f.valor,
    f.status

    FROM fretes f

    LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
    LEFT JOIN produtos p ON p.id = fp.produto_id
    LEFT JOIN enderecos o ON o.id = f.endereco_origem
    LEFT JOIN enderecos d ON d.id = f.endereco_destino
    LEFT JOIN transportadores t ON t.id = f.transportador_id

    WHERE f.data_envio = ?

    ORDER BY f.id";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $data);
$stmt->execute();

$resultado = $stmt->get_result();
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=relatorio_fretes_$data.csv");

$output = fopen("php://output", "w");

fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, [
    "Código",
    "Produto",
    "Origem",
    "Destino",
    "Transportadora",
    "Data de Envio",
    "Previsão de Entrega",
    "Valor",
    "Status"
], ";");
while ($linha = $resultado->fetch_assoc()) {

    fputcsv($output, array_values($linha), ";");

}
fclose($output);

$stmt->close();

exit;

?>