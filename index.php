<?php
include 'includes/config.php';
include 'includes/functions.php';

// 获取特色商品
$featured_products = getFeaturedProducts($pdo);
// 获取最新商品
$new_products = getNewProducts($pdo);
// 获取分类
$categories = getCategories($pdo);
// 获取轮播图
$banners = getActiveBanners($pdo);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>茗茶在线商城 - 品味生活，从一杯好茶开始</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="container">
    <!-- 轮播图区域 -->
    <section class="hero-carousel">
        <div class="carousel-inner">
            <?php foreach($banners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>"
                     style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?php echo $banner['image_url']; ?>');">
                    <div class="carousel-content">
                        <h2><?php echo htmlspecialchars($banner['title']); ?></h2>
                        <p><?php echo htmlspecialchars($banner['description']); ?></p>
                        <a href="<?php echo $banner['link_url'] ?: '#'; ?>" class="btn btn-primary">立即查看</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 指示器 -->
        <div class="carousel-indicators">
            <?php foreach($banners as $index => $banner): ?>
                <div class="carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>"
                     data-slide-to="<?php echo $index; ?>"></div>
            <?php endforeach; ?>
        </div>

        <!-- 控制按钮 -->
        <div class="carousel-controls">
            <button class="carousel-control prev">‹</button>
            <button class="carousel-control next">›</button>
        </div>
    </section>

    <section class="featured-products">
        <h2 class="section-title">特色推荐</h2>
        <div class="products-grid">
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $product['image_url'] ?: 'images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">¥<?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-small">查看详情</a>
                            <button class="btn btn-primary btn-small add-to-cart" data-product-id="<?php echo $product['id']; ?>">加入购物车</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="new-products">
        <h2 class="section-title">新品上市</h2>
        <div class="products-grid">
            <?php foreach($new_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $product['image_url'] ?: 'images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">¥<?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-small">查看详情</a>
                            <button class="btn btn-primary btn-small add-to-cart" data-product-id="<?php echo $product['id']; ?>">加入购物车</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
<script>
    // 轮播图功能
    class Carousel {
        constructor(container) {
            this.container = container;
            this.items = container.querySelectorAll('.carousel-item');
            this.indicators = container.querySelectorAll('.carousel-indicator');
            this.prevBtn = container.querySelector('.carousel-control.prev');
            this.nextBtn = container.querySelector('.carousel-control.next');
            this.currentIndex = 0;
            this.interval = null;
            this.autoPlayDelay = 5000; // 5秒自动切换

            this.init();
        }

        init() {
            // 绑定事件
            this.prevBtn.addEventListener('click', () => this.prev());
            this.nextBtn.addEventListener('click', () => this.next());

            // 指示器点击事件
            this.indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => this.goToSlide(index));
            });

            // 自动播放
            this.startAutoPlay();

            // 鼠标悬停暂停自动播放
            this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
            this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        }

        goToSlide(index) {
            this.items[this.currentIndex].classList.remove('active');
            this.indicators[this.currentIndex].classList.remove('active');

            this.currentIndex = index;

            this.items[this.currentIndex].classList.add('active');
            this.indicators[this.currentIndex].classList.add('active');
        }

        next() {
            const nextIndex = (this.currentIndex + 1) % this.items.length;
            this.goToSlide(nextIndex);
        }

        prev() {
            const prevIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
            this.goToSlide(prevIndex);
        }

        startAutoPlay() {
            this.stopAutoPlay();
            this.interval = setInterval(() => this.next(), this.autoPlayDelay);
        }

        stopAutoPlay() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        }
    }

    // 初始化轮播图
    document.addEventListener('DOMContentLoaded', function() {
        const carouselContainer = document.querySelector('.hero-carousel');
        if (carouselContainer) {
            new Carousel(carouselContainer);
        }
    });
</script>
</body>
</html>