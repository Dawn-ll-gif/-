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

// 处理订单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // 检查库存
    $out_of_stock = false;
    foreach ($cart_items as $item) {
        if ($item['stock'] < $item['quantity']) {
            $out_of_stock = true;
            break;
        }
    }

    if ($out_of_stock) {
        $error = '部分商品库存不足，请调整数量后重新下单';
    } else {
        // 创建订单
        $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // 添加订单项
        foreach ($cart_items as $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

            // 更新商品库存
            $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        // 清空购物车
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

        // 重定向到订单确认页面
        redirect('order_success.php?id=' . $order_id);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>结算 - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <h2 class="section-title">订单结算</h2>

    <?php if (count($cart_items) > 0): ?>
        <div class="checkout-container">
            <div class="checkout-summary">
                <h3>订单详情</h3>
                <div class="order-items">
                    <?php foreach($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo $item['image_url'] ?: 'images/placeholder.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>数量: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="item-price">
                                ¥<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-total">
                    <h3>总计: ¥<?php echo number_format($total_amount, 2); ?></h3>
                </div>
            </div>

            <div class="checkout-form">
                <h3>收货信息</h3>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="name">收货人姓名</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">联系电话</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">收货地址</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">备注</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="place_order" class="btn btn-primary btn-block">提交订单</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>购物车为空，无法结算</p>
            <a href="products.php" class="btn btn-primary">继续购物</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>