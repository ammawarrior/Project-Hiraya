<?php
include 'layout/header.php';
?>

<style>
/* Course Card Animations */
.uk-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.uk-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

/* Image Hover Effects */
.uk-card-media-top img {
    transition: transform 0.3s ease;
}
.uk-card:hover .uk-card-media-top img {
    transform: scale(1.05);
}

/* Banner Icons Animation */
[data-uk-icon] {
    transition: transform 0.3s ease;
}
.uk-flex:hover [data-uk-icon] {
    transform: scale(1.1);
}

/* Course Category Badge */
.course-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(39, 191, 67, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8em;
    z-index: 1;
}

/* Search Bar Styling */
.course-search {
    background: white;
    border-radius: 25px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Course Stats */
.course-stats {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    font-size: 0.9em;
    color: #666;
}

.course-stats span {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Featured Course Highlight */
.featured-course {
    border: 2px solid #27bf43;
    position: relative;
}

.featured-course::before {
    content: "Featured";
    position: absolute;
    top: 10px;
    left: 10px;
    background: #27bf43;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8em;
    z-index: 1;
}
</style>

<div class="uk-container uk-container-large">
  <div class="uk-position-relative uk-visible-toggle uk-light uk-box-shadow-small uk-overflow-hidden uk-border-rounded-large"
       tabindex="-1" data-uk-slider="autoplay: true; autoplayInterval: 5000">
    <ul class="uk-slider-items uk-child-width-1-1">
      <li>
        <img src="img/farming.jpg" alt="Slide">
        <div class="uk-position-cover uk-overlay-light uk-flex uk-flex-middle uk-padding-slide">
          <div class="uk-width-3-4@m">
            <h2 class="uk-heading-medium uk-letter-spacing-medium uk-text-bold">Transform Your Future</h2>
            <p class="uk-text-large uk-text-bold">Master in-demand skills with expert-led courses</p>
            <a href="#courses" class="uk-button uk-button-primary uk-margin-small-top">Explore Courses</a>
          </div>
        </div>
      </li>
      <li>
        <img src="img/doctor.jpg" alt="Slide">
        <div class="uk-position-cover uk-overlay-light uk-flex uk-flex-middle uk-padding-slide">
          <div class="uk-width-1-2@m">
            <h1 class="uk-heading-medium uk-letter-spacing-medium uk-text-bold">Learn at Your Pace</h1>
            <p class="uk-text-large uk-text-bold">Flexible learning paths designed for your success</p>
            <a href="#courses" class="uk-button uk-button-primary uk-margin-small-top">Start Learning</a>
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
  <div class="uk-background-primary-dark uk-light uk-border-rounded-large uk-position-relative uk-position-z-index uk-header-banner uk-header-banner-courses uk-box-shadow-small">
    <div class="uk-child-width-1-3@m uk-grid-small" data-uk-grid>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: users; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Expert-Led Courses</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Learn from industry professionals and certified instructors</p>
          </div>
        </div>
      </div>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: clock; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Self-Paced Learning</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Study anytime, anywhere with lifetime access</p>
          </div>
        </div>
      </div>
      <div class="uk-flex uk-flex-middle">
        <div class="uk-grid-small" data-uk-grid>
          <div class="uk-width-auto uk-flex uk-flex-middle">
            <span data-uk-icon="icon: future; ratio: 2"></span>
          </div>
          <div class="uk-width-expand">
            <h4 class="uk-margin-remove">Career-Ready Skills</h4>
            <p class="uk-margin-remove uk-text-muted uk-text-small">Gain practical skills that employers are looking for</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="uk-section uk-margin-top" id="courses">
  <div class="uk-container">
    <!-- Search and Filter Section -->
    <div class="course-search">
      <div class="uk-grid-small" data-uk-grid>
        <div class="uk-width-3-4@m">
          <input class="uk-input" type="text" placeholder="Search courses...">
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

    <div class="uk-grid-small uk-flex uk-flex-middle" data-uk-grid>
      <div class="uk-width-expand@m">
        <h2>Featured Courses</h2>
      </div>
    </div>

    <div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-match uk-margin-medium-top" data-uk-grid>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden featured-course">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=1">
              <img src="img/agriculture.jpg" alt="Sustainable Agriculture">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
            <div class="course-badge">New</div>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Sustainable Agriculture</h3>
            <p class="uk-text-muted uk-text-small">Master modern farming techniques and eco-friendly practices</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 1.2k Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.8</span>
              <span><span data-uk-icon="icon: clock"></span> 8 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=1" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=2">
              <img src="img/healthcare.jpg" alt="Health & Wellness">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Health & Wellness</h3>
            <p class="uk-text-muted uk-text-small">Courses on medical technology, wellness, and healthcare innovation</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 2.5k Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.9</span>
              <span><span data-uk-icon="icon: clock"></span> 12 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=2" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=3">
              <img src="img/energy.jpg" alt="Renewable Energy">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Renewable Energy</h3>
            <p class="uk-text-muted uk-text-small">Learn about solar, wind, and sustainable energy solutions</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 1.8k Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.7</span>
              <span><span data-uk-icon="icon: clock"></span> 10 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=3" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=4">
              <img src="img/construction.jpg" alt="Smart Construction">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Smart Construction</h3>
            <p class="uk-text-muted uk-text-small">Explore advanced building methods and infrastructure</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 950 Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.6</span>
              <span><span data-uk-icon="icon: clock"></span> 8 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=4" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=5">
              <img src="img/consumer.jpg" alt="Product Design">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Product Design</h3>
            <p class="uk-text-muted uk-text-small">Innovate and create products for everyday life</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 1.5k Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.8</span>
              <span><span data-uk-icon="icon: clock"></span> 6 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=5" class="uk-position-cover"></a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <a href="course.php?category=6">
              <img src="img/ict.jpg" alt="Information Technology">
              <div class="uk-position-cover uk-overlay-xlight"></div>
            </a>
          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom">Information Technology</h3>
            <p class="uk-text-muted uk-text-small">Courses on programming, networking, and digital transformation</p>
            <div class="course-stats">
              <span><span data-uk-icon="icon: users"></span> 3.2k Students</span>
              <span><span data-uk-icon="icon: star"></span> 4.9</span>
              <span><span data-uk-icon="icon: clock"></span> 16 Weeks</span>
            </div>
          </div>
          <a href="course.php?category=6" class="uk-position-cover"></a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'layout/footer.php'; ?> 