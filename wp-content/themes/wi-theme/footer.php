<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const scrollBtn = document.querySelector('.scroll-top-btn');
    if (scrollBtn) {
      scrollBtn.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
  });
</script>

<footer class="site-footer">
  <div>
    <div>
      <div class="footer-menu">
        <div class="footer-menu-wrapper">
          <?php
            wp_nav_menu(array(
              'theme_location' => 'footermenu',
              'container' => false, 
              'menu_class' => 'footer-links',
              'fallback_cb' => false
            ));
          ?>
        </div>
      </div>      
      <div class="footer-top">
        <div class="footer-kreative-social">
          <a class="footer-hashtag" 
            href="https://www.instagram.com/explore/tags/werbeinsel/" 
            target="_blank" 
            rel="noopener noreferrer">
            #werbeinsel
          </a>
          <div class="social-icons">
            <a href="https://www.tiktok.com" target="_blank" aria-label="TikTok">
              <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://www.instagram.com/werbeinsel" target="_blank" aria-label="Instagram">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook">
              <i class="fab fa-facebook"></i>
            </a>
          </div>
          <a href="#" class="scroll-top-btn">
            <svg xmlns="http://www.w3.org/2000/svg" 
              width="24" 
              height="24" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              class="lucide lucide-rocket w-7 h-7 text-black transform rotate-[-45deg]" 
              aria-hidden="true">
              <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
              <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
              <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
              <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
            </svg>
          </a>    
        </div>
      </div>


    </div>
  </div>

  <!--
  <div class="footer-copyright">
    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
  </div>
  -->

  <?php wp_footer(); ?>
</footer>
