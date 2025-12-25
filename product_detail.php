<?php
include 'includes/config.php';
include 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('products.php');
}

$product_id = intval($_GET['id']);
$product = getProduct($pdo, $product_id);

if (!$product) {
    redirect('products.php');
}

// 获取同类商品
$similar_products = getSimilarProducts($pdo, $product_id, $product['category_id']);
// 获取推荐商品
$recommended_products = getRecommendedProducts($pdo);
// 获取商品评价
$reviews = getProductReviews($pdo, $product_id);

// 计算平均评分
$avg_rating = 0;
if (count($reviews) > 0) {
    $total_rating = 0;
    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
    }
    $avg_rating = round($total_rating / count($reviews), 1);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <div class="product-detail-container">
        <div class="product-main">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo $product['image_url'] ?: 'images/placeholder.jpg'; ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <!-- 如果有多个图片，可以在这里添加缩略图 -->
            </div>

            <div class="product-info">
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-meta">
                    <span class="product-category">分类: <?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span class="product-rating">
                            评分:
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($avg_rating)): ?>
                                    ★
                                <?php elseif ($i - 0.5 <= $avg_rating): ?>
                                    ☆
                                <?php else: ?>
                                    ☆
                                <?php endif; ?>
                            <?php endfor; ?>
                            (<?php echo $avg_rating; ?>/5, <?php echo count($reviews); ?>条评价)
                        </span>
                </div>

                <div class="product-price">
                    <span class="current-price">¥<?php echo number_format($product['price'], 2); ?></span>
                </div>

                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="in-stock">有货 (库存: <?php echo $product['stock']; ?>)</span>
                    <?php else: ?>
                        <span class="out-of-stock">缺货</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <h3>商品描述</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="product-actions">
                    <?php if ($product['stock'] > 0): ?>
                        <div class="quantity-control">
                            <label for="quantity">数量:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control" style="width: 80px;">
                        </div>
                        <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">加入购物车</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>暂时缺货</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-sidebar">
            <div class="similar-products">
                <h3>同类商品</h3>
                <?php foreach($similar_products as $similar): ?>
                    <div class="sidebar-product">
                        <img src="<?php echo $similar['image_url'] ?: 'images/placeholder.jpg'; ?>"
                             alt="<?php echo htmlspecialchars($similar['name']); ?>" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="sidebar-product-info">
                            <a href="product_detail.php?id=<?php echo $similar['id']; ?>"><?php echo htmlspecialchars($similar['name']); ?></a>
                            <p class="price">¥<?php echo number_format($similar['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="recommended-products">
                <h3>推荐商品</h3>
                <?php foreach($recommended_products as $recommended): ?>
                    <div class="sidebar-product">
                        <img src="<?php echo $recommended['image_url'] ?: 'images/placeholder.jpg'; ?>"
                             alt="<?php echo htmlspecialchars($recommended['name']); ?>" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="sidebar-product-info">
                            <a href="product_detail.php?id=<?php echo $recommended['id']; ?>"><?php echo htmlspecialchars($recommended['name']); ?></a>
                            <p class="price">¥<?php echo number_format($recommended['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="reviews-section">
        <h3 class="section-title">商品评价</h3>

        <?php if (count($reviews) > 0): ?>
            <?php foreach($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-user"><?php echo htmlspecialchars($review['username']); ?></div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $review['rating']): ?>
                                    ★
                                <?php else: ?>
                                    ☆
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="review-date"><?php echo date('Y-m-d', strtotime($review['created_at'])); ?></div>
                    <div class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-reviews">暂无评价</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>