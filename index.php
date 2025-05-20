<?php include 'layout/header.php'; ?>

<div class="uk-container uk-container-large">
  <div class="uk-position-relative uk-visible-toggle uk-light uk-box-shadow-small uk-overflow-hidden uk-border-rounded-large"
       tabindex="-1" data-uk-slider="autoplay: true; autoplayInterval: 5000">
    <ul class="uk-slider-items uk-child-width-1-1">
      <li>
        <img src="img/farming.jpg" alt="Slide">
        <div class="uk-position-cover uk-overlay-light uk-flex uk-flex-middle uk-padding-slide">
          <div class="uk-width-3-4@m">
            <h2 class="uk-heading-medium uk-letter-spacing-medium uk-text-bold">Discover Tomorrow's Technologies</h2>
            <p class="uk-text-large uk-text-bold">Explore groundbreaking innovations shaping the future</p>
            <a href="products.php" class="uk-button uk-button-primary uk-margin-small-top">Browse Innovations</a>
          </div>
        </div>
      </li>
      <li>
        <img src="img/doctor.jpg" alt="Slide">
        <div class="uk-position-cover uk-overlay-light uk-flex uk-flex-middle uk-padding-slide">
          <div class="uk-width-1-2@m">
            <h1 class="uk-heading-medium uk-letter-spacing-medium uk-text-bold">Invest in Innovation</h1>
            <p class="uk-text-large uk-text-bold">Support visionary ideas and emerging technologies</p>
            <a href="products.php" class="uk-button uk-button-primary uk-margin-small-top">Start Investing</a>
          </div>
        </div>
      </li>
    </ul>
    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-previous
       data-uk-slider-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-next
       data-uk-slider-item="next"></a>
  </div>
</div>

<div class="uk-container">
  <div class="uk-background-primary-dark uk-light uk-border-rounded-large uk-position-relative uk-position-z-index uk-header-banner uk-box-shadow-small">
    <div class="uk-child-width-1-3@m uk-grid-small" data-uk-grid>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: bolt; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Innovative Solutions</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Discover cutting-edge technologies and innovative products</p>
          </div>
        </div>
      </div>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: users; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Connect with Innovators</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Network with industry leaders and tech pioneers</p>
          </div>
        </div>
      </div>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: credit-card; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Invest in Innovation</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Support and invest in groundbreaking technologies</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="uk-section uk-margin-top">
  <div class="uk-container">
    <!-- Search and Filter Section -->
    <div class="search-bar">
      <div class="uk-grid-small" data-uk-grid>
        <div class="uk-width-3-4@m">
          <input class="uk-input" type="text" placeholder="Search innovations...">
        </div>
        <div class="uk-width-1-4@m">
          <select class="uk-select">
            <option>All Categories</option>
            <option>Agriculture</option>
            <option>Healthcare</option>
            <option>Energy</option>
            <option>Construction</option>
            <option>Product Design</option>
            <option>Information Technology</option>
          </select>
        </div>
      </div>
    </div>

    <div class="section-header">
      <h2>Featured Categories</h2>
    </div>

    <div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-match" data-uk-grid>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden featured-item">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=1">
              <img src="img/agriculture.jpg" alt="Agriculture">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
            <div class="category-badge">Popular</div>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Agriculture</h3>
            <p class="uk-text-muted uk-text-small">Explore sustainable farming technologies and modern agricultural tools.</p>
          </div>
          <a href="products.php?category=1" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=2">
              <img src="img/healthcare.jpg" alt="Healthcare">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Healthcare</h3>
            <p class="uk-text-muted uk-text-small">Innovative health solutions and medical technologies.</p>
          </div>
          <a href="products.php?category=2" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=3">
              <img src="img/energy.jpg" alt="Energy">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Energy</h3>
            <p class="uk-text-muted uk-text-small">Renewable energy solutions and efficient power systems.</p>
          </div>
          <a href="products.php?category=3" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=4">
              <img src="img/construction.jpg" alt="Construction">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Construction & Infrastructure</h3>
            <p class="uk-text-muted uk-text-small">Advanced building materials and infrastructure technologies.</p>
          </div>
          <a href="products.php?category=4" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=5">
              <img src="img/consumer.jpg" alt="Consumer Products">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Consumer Products</h3>
            <p class="uk-text-muted uk-text-small">Innovative everyday products for modern living.</p>
          </div>
          <a href="products.php?category=5" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="products.php?category=6">
              <img src="img/ict.jpg" alt="ICT">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title">Information & Communication Technology</h3>
            <p class="uk-text-muted uk-text-small">Tech solutions for data communication and digital transformation.</p>
          </div>
          <a href="products.php?category=6" class="uk-position-cover"></a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'layout/footer.php'; ?>


</body>


<!--   19 Nov 2019 03:39:33 GMT -->
</html>