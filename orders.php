<?php
include 'includes/config.php';
include 'includes/functions.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 获取订单总数
$sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_orders / $limit);

// 获取订单列表
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 处理评价提交
$review_success = '';
$review_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // 验证输入
    if (empty($order_id) || empty($product_id) || empty($rating) || empty($comment)) {
        $review_error = '请填写所有必填字段';
    } elseif ($rating < 1 || $rating > 5) {
        $review_error = '评分必须在1-5星之间';
    } elseif (strlen($comment) < 10) {
        $review_error = '评价内容至少需要10个字符';
    } else {
        // 检查订单是否存在且属于当前用户
        $sql = "SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'delivered'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id, $user_id]);

        if ($stmt->rowCount() === 0) {
            $review_error = '订单不存在或未完成，无法评价';
        } else {
            // 检查商品是否属于该订单
            $sql = "SELECT id FROM order_items WHERE order_id = ? AND product_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$order_id, $product_id]);

            if ($stmt->rowCount() === 0) {
                $review_error = '该商品不属于此订单';
            } else {
                // 检查是否已经评价过
                $sql = "SELECT id FROM reviews WHERE user_id = ? AND order_id = ? AND product_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $order_id, $product_id]);

                if ($stmt->rowCount() > 0) {
                    $review_error = '您已经评价过此商品';
                } else {
                    // 添加评价
                    $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$user_id, $product_id, $order_id, $rating, $comment])) {
                        $review_success = '评价提交成功！感谢您的反馈。';

                        // 清空表单数据
                        $_POST = array();
                    } else {
                        $review_error = '评价提交失败，请稍后重试';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的订单 - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 评分星星样式 */
        .rating-stars {
            margin: 0.5rem 0;
        }

        .stars-container {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.1s ease;
            user-select: none;
        }

        .star:hover {
            transform: scale(1.1);
        }

        .star.active {
            color: #ffc107;
        }

        .star.hover {
            color: #ffdb70;
        }

        .rating-text {
            font-size: 0.9rem;
            color: var(--gray);
            text-align: center;
            min-height: 1.5rem;
            align-content: center;
        }

        /* 评价相关样式 */
        .reviewed {
            color: var(--success-color);
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            background: var(--bg-light);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }

        .review-comment-full {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .review-comment-full p {
            margin: 0;
            line-height: 1.6;
            color: var(--text-dark);
        }

        /* 模态框样式优化 */
        .modal-content {
            max-width: 500px;
        }

        .form-control-static {
            padding: 0.75rem;
            background: var(--bg-light);
            border-radius: var(--border-radius);
            font-weight: 500;
            color: var(--primary-dark);
            border: 1px solid var(--gray-light);
        }

        /* 响应式调整 */
        @media (max-width: 768px) {
            .star {
                font-size: 1.8rem;
            }

            .modal-content {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
        }

        @media (max-width: 480px) {
            .star {
                font-size: 1.6rem;
            }

            .stars-container {
                gap: 0.15rem;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <h2 class="section-title">我的订单</h2>

    <?php if (count($orders) > 0): ?>
        <div class="orders-container">
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>订单号: #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h3>
                            <p>下单时间: <?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-status">
                        <span class="status-<?php echo $order['status']; ?>">
                            <?php
                            $status_text = [
                                'pending' => '待处理',
                                'confirmed' => '已确认',
                                'shipped' => '已发货',
                                'delivered' => '已完成',
                                'cancelled' => '已取消'
                            ];
                            echo $status_text[$order['status']];
                            ?>
                        </span>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php
                        // 获取订单商品
                        $sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$order['id']]);
                        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php foreach($order_items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?php echo $item['image_url'] ?: 'images/placeholder.jpg'; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>数量: <?php echo $item['quantity']; ?> × ¥<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="item-price">
                                    ¥<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                                <div class="item-actions">
                                    <?php if ($order['status'] == 'delivered'): ?>
                                        <?php
                                        // 检查是否已经评价
                                        $sql = "SELECT id, rating, comment, created_at FROM reviews 
                                        WHERE user_id = ? AND order_id = ? AND product_id = ?";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([$user_id, $order['id'], $item['product_id']]);
                                        $review = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $has_reviewed = $stmt->rowCount() > 0;
                                        ?>

                                        <?php if ($has_reviewed): ?>
                                            <div class="review-info">
                                                <span class="reviewed">已评价</span>
                                                <div class="review-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php if ($i <= $review['rating']): ?>
                                                            ★
                                                        <?php else: ?>
                                                            ☆
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                                <?php if (!empty($review['comment'])): ?>
                                                    <div class="review-comment-full">
                                                        <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                                        <small>评价时间: <?php echo date('Y-m-d', strtotime($review['created_at'])); ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-small open-review-modal"
                                                    data-order-id="<?php echo $order['id']; ?>"
                                                    data-product-id="<?php echo $item['product_id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($item['name']); ?>">
                                                评价商品
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            总计: ¥<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- 分页 -->
            <?php if ($total_pages > 1): ?>
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li><a href="?page=<?php echo $page - 1; ?>">上一页</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li><a href="?page=<?php echo $page + 1; ?>">下一页</a></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-orders">
            <p>您还没有任何订单</p>
            <a href="products.php" class="btn btn-primary">去购物</a>
        </div>
    <?php endif; ?>
</main>

<!-- 评价模态框 -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">评价商品</h3>
            <button class="modal-close" onclick="closeModal('reviewModal')">×</button>
        </div>
        <div class="modal-body">
            <?php if ($review_error): ?>
                <div class="alert alert-danger"><?php echo $review_error; ?></div>
            <?php endif; ?>

            <?php if ($review_success): ?>
                <div class="alert alert-success"><?php echo $review_success; ?></div>
            <?php endif; ?>

            <form id="reviewForm" method="POST">
                <input type="hidden" name="order_id" id="review_order_id">
                <input type="hidden" name="product_id" id="review_product_id">

                <div class="form-group">
                    <label class="form-label">商品名称</label>
                    <p id="review_product_name" class="form-control-static"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">评分 <span class="required">*</span></label>
                    <div class="rating-stars" id="ratingStars">
                        <input type="hidden" name="rating" id="selected_rating" value="5" required>
                        <div class="stars-container">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star" data-rating="<?php echo $i; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-text">
                            <span id="ratingText">非常好 (5星)</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="review_comment">评价内容 <span class="required">*</span></label>
                    <textarea class="form-control" id="review_comment" name="comment" rows="4" required
                              placeholder="请分享您对这款商品的真实体验（至少10个字符）..."><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : ''; ?></textarea>
                    <small class="form-text">您的评价将帮助其他用户做出更好的选择</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('reviewModal')">取消</button>
                    <button type="submit" id="submit_review" name="submit_review" value="submit_review" class="btn btn-primary">提交评价</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
<script>
    // 评分选择功能
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const selectedRatingInput = document.getElementById('selected_rating');
        const ratingText = document.getElementById('ratingText');

        // 评分文本映射
        const ratingTexts = {
            1: '很差 (1星)',
            2: '较差 (2星)',
            3: '一般 (3星)',
            4: '较好 (4星)',
            5: '非常好 (5星)'
        };

        // 初始化评分
        let currentRating = 5;
        updateStars(currentRating);

        // 为每个星星添加事件监听
        stars.forEach(star => {
            // 点击事件
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                currentRating = rating;
                selectedRatingInput.value = rating;
                updateStars(rating);
                updateRatingText(rating);
            });

            // 鼠标悬停事件
            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.getAttribute('data-rating'));
                highlightStars(hoverRating);
                updateRatingText(hoverRating, true);
            });

            // 鼠标离开事件
            star.addEventListener('mouseleave', function() {
                updateStars(currentRating);
                updateRatingText(currentRating);
            });
        });

        // 更新星星显示
        function updateStars(rating) {
            stars.forEach((star, index) => {
                const starRating = index + 1;
                if (starRating <= rating) {
                    star.classList.add('active');
                    star.classList.remove('hover');
                } else {
                    star.classList.remove('active');
                    star.classList.remove('hover');
                }
            });
        }

        // 高亮星星（悬停时）
        function highlightStars(rating) {
            stars.forEach((star, index) => {
                const starRating = index + 1;
                if (starRating <= rating) {
                    star.classList.add('hover');
                } else {
                    star.classList.remove('hover');
                }
            });
        }

        // 更新评分文本
        function updateRatingText(rating, isHover = false) {
            if (ratingTexts[rating]) {
                let text = ratingTexts[rating];
                if (isHover) {
                    text += ' (悬停预览)';
                }
                ratingText.textContent = text;
            }
        }
    });

    // 打开评价模态框
    document.querySelectorAll('.open-review-modal').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');

            document.getElementById('review_order_id').value = orderId;
            document.getElementById('review_product_id').value = productId;
            document.getElementById('review_product_name').textContent = productName;

            // 重置表单
            document.getElementById('review_comment').value = '';
            document.getElementById('selected_rating').value = '5';

            // 重置星星显示
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < 5) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
                star.classList.remove('hover');
            });
            document.getElementById('ratingText').textContent = '非常好 (5星)';

            // 清空之前的消息
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => alert.remove());

            openModal('reviewModal');
        });
    });

    // 表单提交验证
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        const rating = document.getElementById('selected_rating').value;
        const comment = document.getElementById('review_comment').value.trim();

        if (!rating) {
            e.preventDefault();
            alert('请选择评分');
            return false;
        }

        if (!comment) {
            e.preventDefault();
            alert('请填写评价内容');
            return false;
        }

        if (comment.length < 5) {
            e.preventDefault();
            alert('评价内容至少需要5个字符');
            return false;
        }

        // 显示提交中状态
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading-spinner">提交中...</span>';
        submitBtn.disabled = true;

        // 3秒后恢复按钮状态（防止重复提交）
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });

    // 如果提交成功，3秒后自动关闭模态框
    <?php if ($review_success): ?>
    setTimeout(() => {
        closeModal('reviewModal');
        // 刷新页面以更新评价状态
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }, 2000);
    <?php endif; ?>
</script>
</body>
</html>