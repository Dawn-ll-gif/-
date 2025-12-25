<?php
include 'includes/config.php';
include 'includes/functions.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user_id = $_SESSION['user_id'];

// 获取购物车商品
$sql = "SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.image_url, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 计算总金额
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// 处理删除操作
if (isset($_POST['remove_item'])) {
    $cart_id = intval($_POST['cart_id']);
    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cart_id, $user_id]);
    redirect('cart.php');
}

// 处理更新数量
if (isset($_POST['update_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantity, $cart_id, $user_id]);
    }
    redirect('cart.php');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>购物车 - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <h2 class="section-title">我的购物车</h2>

    <?php if (count($cart_items) > 0): ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php foreach($cart_items as $item): ?>
                    <div class="cart-item" data-item-id="<?php echo $item['cart_id']; ?>">
                        <div class="cart-item-image">
                            <img src="<?php echo $item['image_url'] ?: 'images/placeholder.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>

                        <div class="cart-item-name">
                            <a href="product_detail.php?id=<?php echo $item['id']; ?>">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </a>
                        </div>

                        <div class="cart-item-price">
                            ¥<?php echo number_format($item['price'], 2); ?>
                        </div>

                        <div class="quantity-control">
                            <form method="POST" class="quantity-form">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="button" class="quantity-btn quantity-minus">-</button>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                       min="1" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                <button type="button" class="quantity-btn quantity-plus">+</button>
                                <button type="submit" name="update_quantity" class="btn btn-small" style="margin-left: 10px;">更新</button>
                            </form>
                        </div>

                        <div class="cart-item-actions">
                            <form method="POST" onsubmit="return confirm('确定要删除这个商品吗？');">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger btn-small">删除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="cart-total">
                    总计: ¥<?php echo number_format($total_amount, 2); ?>
                </div>
                <a href="checkout.php" class="btn btn-primary">去结算</a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>购物车为空</p>
            <a href="products.php" class="btn btn-primary">继续购物</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>