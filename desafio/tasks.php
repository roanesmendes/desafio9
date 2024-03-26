<?php
$servername = "localhost";
$port = 7306;
$username = "root";
$password = "";
$dbname = "banco_de_dados";

try {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

function displayEstoque($conn) {
    $sql = "SELECT * FROM estoque";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo "<table>";
    echo "<tr><th>ID</th><th>Nome do Produto</th><th>Quantidade Mínima</th><th>Quantidade Atual</th><th>Ações</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome_produto']}</td>";
        echo "<td>{$row['quantidade_min']}</td>";
        echo "<td>{$row['quantidade_atual']}</td>";
        echo "<td>";
        echo "<button style='margin-right: 5px;' onclick='openModal(\"{$row['id']}\", \"{$row['nome_produto']}\", \"{$row['quantidade_min']}\", \"{$row['quantidade_atual']}\")'>Editar</button>";
        echo "<form method='post' action='index.php'>";
        echo "<input type='hidden' name='delete_id' value='{$row['id']}'>";
        echo "<button type='submit' name='delete'>Excluir</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function addProduto($conn, $nome_produto, $quantidade_min, $quantidade_atual) {
    //Código aqui
    $sql = "INSERT INTO estoque (nome_produto, quantidade_min, quantidade_atual) VALUES (:nome_produto, :quantidade_min, :quantidade_atual)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome_produto', $nome_produto);
    $stmt->bindParam(':quantidade_min', $quantidade_min);
    $stmt->bindParam(':quantidade_atual', $quantidade_atual);
    $stmt->execute();
}   

function updateProduto($conn, $id, $nome_produto, $quantidade_min, $quantidade_atual) {
    //Código aqui
    $sql = "UPDATE estoque SET nome_produto = :nome_produto, quantidade_min = :quantidade_min = :quantidade_atual = :quantidade_atual WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome_produto', $nome_produto);
    $stmt->bindParam(':quantidade_min', $quantidade_min);
    $stmt->bindParam(':quantidade_atual', $quantidade_atual);
    $stmt->execute();
}

function deleteProduto($conn, $id) {
   //Código aqui
   $sql = "DELETE FROM estoque WHERE id = :id";
   $stmt = $conn->prepare($sql);
   $stmt->bindParam(':id', $id);
   $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nome_produto = $_POST['nome_produto'];
    $quantidade_min = $_POST['quantidade_min'];
    $quantidade_atual = $_POST['quantidade_atual'];
    addProduto($conn, $nome_produto, $quantidade_min, $quantidade_atual);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['produto_id'];
    $nome_produto = $_POST['nome_produto'];
    $quantidade_min = $_POST['quantidade_min'];
    $quantidade_atual = $_POST['quantidade_atual'];
    updateProduto($conn, $id, $nome_produto, $quantidade_min, $quantidade_atual);
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['delete_id'];
    deleteProduto($conn, $id);
    header("Location: index.php");
}

displayEstoque($conn);

$conn = null;
?>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Editar Produto</h3>
        <form method="post" action="index.php">
            <input type="hidden" id="edit_produto_id" name="produto_id">
            <input type="text" id="edit_nome_produto" name="nome_produto" placeholder="Nome do Produto" required>
            <input type="number" id="edit_quantidade_min" name="quantidade_min" placeholder="Quantidade Mínima" required>
            <input type="number" id="edit_quantidade_atual" name="quantidade_atual" placeholder="Quantidade Atual" required>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="nome_produto" placeholder="Nome do Produto" required>
    <input type="number" name="quantidade_min" placeholder="Quantidade Mínima" required>
    <input type="number" name="quantidade_atual" placeholder="Quantidade Atual" required>
    <button type="submit" name="add">Adicionar</button>
</form>

<script>
    function openModal(id, nome_produto, quantidade_min, quantidade_atual) {
        document.getElementById('edit_produto_id').value = id;
        document.getElementById('edit_nome_produto').value = nome_produto;
        document.getElementById('edit_quantidade_min').value = quantidade_min;
        document.getElementById('edit_quantidade_atual').value = quantidade_atual;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>