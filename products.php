<?php
include 'includes/config.php';
include 'includes/functions.php';

// 获取查询参数
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// 构建查询
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

// 获取总数
$count_sql = "SELECT COUNT(*) as total FROM ($sql) as count_table";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_products / $limit);

// 添加排序和分页
$sql .= " ORDER BY p.created_at DESC LIMIT {$limit} OFFSET {$offset}";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 获取分类
$categories = getCategories($pdo);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品列表 - 茗茶在线商城</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <div class="products-page">
        <div class="products-sidebar">
            <div class="categories-sidebar">
                <h3>商品分类</h3>
                <ul class="categories-list">
                    <li><a href="products.php" class="<?php echo $category_id == 0 ? 'active' : ''; ?>">所有分类</a></li>
                    <?php foreach($categories as $category): ?>
                        <li>
                            <a href="products.php?category=<?php echo $category['id']; ?>"
                               class="<?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="products-main">
            <div class="products-header">
                <h2 class="section-title">
                    <?php
                    if ($category_id > 0) {
                        $category_name = '';
                        foreach($categories as $cat) {
                            if ($cat['id'] == $category_id) {
                                $category_name = $cat['name'];
                                break;
                            }
                        }
                        echo htmlspecialchars($category_name);
                    } elseif (!empty($search)) {
                        echo "搜索: " . htmlspecialchars($search);
                    } else {
                        echo "所有商品";
                    }
                    ?>
                </h2>

                <div class="products-search">
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="搜索商品..."
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                        <?php if ($category_id > 0): ?>
                            <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary" style="width: 100px">搜索</button>
                    </form>
                </div>
            </div>

            <?php if (count($products) > 0): ?>
                <div class="products-grid">
                    <?php foreach($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $product['image_url'] ?: 'images/placeholder.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                <p class="product-price">¥<?php echo number_format($product['price'], 2); ?></p>
                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-small">查看详情</a>
                                    <button class="btn btn-primary btn-small add-to-cart" data-product-id="<?php echo $product['id']; ?>">加入购物车</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 分页 -->
                <?php if ($total_pages > 1): ?>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">上一页</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">下一页</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-products">
                    <p>没有找到相关商品</p>
                    <a href="products.php" class="btn btn-primary">查看所有商品</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>