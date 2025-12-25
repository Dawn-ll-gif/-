<?php
include 'includes/config.php';
include 'includes/functions.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    redirect('login.php');
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 获取订单信息
$sql = "SELECT o.*, u.username FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('orders.php');
}

// 获取订单商品
$sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 状态文本映射
$status_text = [
    'pending' => '待处理',
    'confirmed' => '已确认',
    'shipped' => '已发货',
    'delivered' => '已完成',
    'cancelled' => '已取消'
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单提交成功 - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .order-success-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .success-header {
            text-align: center;
            background: white;
            padding: 3rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }

        .success-header h1 {
            color: var(--success-color);
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .success-header p {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .order-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .order-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-items-card, .order-summary-card {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .card-title {
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
        }

        .order-item {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0.5rem;
            border-bottom: 1px solid var(--gray-light);
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            overflow: hidden;
            border-radius: var(--border-radius);
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info h4 {
            margin: 0 0 0.5rem 0;
            color: var(--primary-dark);
        }

        .item-info p {
            margin: 0.25rem 0;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .item-total {
            text-align: right;
        }

        .item-price {
            font-weight: 600;
            color: var(--secondary-dark);
        }

        .order-info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .order-info-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray-light);
        }

        .order-info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--gray);
        }

        .info-value {
            font-weight: 600;
            color: var(--primary-dark);
        }

        .order-total {
            font-size: 1.3rem;
            color: var(--secondary-dark);
            font-weight: 700;
        }

        .next-steps {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .step-card {
            text-align: center;
            padding: 1.5rem;
            background: var(--bg-light);
            border-radius: var(--border-radius);
        }

        .step-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .step-card h4 {
            margin: 0 0 0.5rem 0;
            color: var(--primary-dark);
        }

        .step-card p {
            margin: 0;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .success-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .order-details-grid {
                grid-template-columns: 1fr;
            }

            .order-item {
                grid-template-columns: 60px 1fr;
                grid-template-areas:
                "image info"
                "image total";
                gap: 0.5rem;
            }

            .item-image {
                grid-area: image;
            }

            .item-info {
                grid-area: info;
            }

            .item-total {
                grid-area: total;
                text-align: left;
            }

            .steps-grid {
                grid-template-columns: 1fr;
            }

            .success-actions {
                flex-direction: column;
            }

            .btn-large {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <div class="order-success-container">
        <!-- 成功头部 -->
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>订单提交成功！</h1>
            <p>感谢您的购买，我们已收到您的订单</p>
            <p>订单号：<span class="order-number">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span></p>
        </div>

        <!-- 订单详情 -->
        <div class="order-details-grid">
            <!-- 商品列表 -->
            <div class="order-items-card">
                <h3 class="card-title">订单商品</h3>
                <div class="order-items-list">
                    <?php foreach($order_items as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo $item['image_url'] ?: 'images/placeholder.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>数量：<?php echo $item['quantity']; ?></p>
                                <p class="item-price">单价：¥<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="item-total">
                                <p class="item-price">¥<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 订单摘要 -->
            <div class="order-summary-card">
                <h3 class="card-title">订单信息</h3>
                <ul class="order-info-list">
                    <li class="order-info-item">
                        <span class="info-label">订单状态：</span>
                        <span class="info-value status-<?php echo $order['status']; ?>">
                                <?php echo $status_text[$order['status']]; ?>
                            </span>
                    </li>
                    <li class="order-info-item">
                        <span class="info-label">订单金额：</span>
                        <span class="info-value">¥<?php echo number_format($order['total_amount'], 2); ?></span>
                    </li>
                    <li class="order-info-item">
                        <span class="info-label">下单时间：</span>
                        <span class="info-value"><?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></span>
                    </li>
                    <li class="order-info-item">
                        <span class="info-label">支付方式：</span>
                        <span class="info-value">在线支付</span>
                    </li>
                    <li class="order-info-item order-total">
                        <span class="info-label">实付金额：</span>
                        <span class="info-value">¥<?php echo number_format($order['total_amount'], 2); ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 操作按钮 -->
        <div class="success-actions">
            <a href="orders.php" class="btn btn-primary btn-large">查看我的订单</a>
            <a href="products.php" class="btn btn-secondary btn-large">继续购物</a>
            <a href="index.php" class="btn btn-secondary btn-large">返回首页</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>