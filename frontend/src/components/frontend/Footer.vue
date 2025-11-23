<!-- components/frontend/Footer.vue -->
<template>
  <footer class="footer">
    <!-- Newsletter Section -->
    <section class="newsletter-section">
      <div class="container">
        <div class="newsletter-content">
          <div class="newsletter-text">
            <h3>STAY CONNECTED</h3>
            <p>Subscribe to our newsletter for the latest products, promotions and workwear tips</p>
          </div>
          <form class="newsletter-form" @submit.prevent="subscribeNewsletter">
            <input 
              type="email" 
              v-model="email" 
              placeholder="Enter your email address" 
              required
              class="newsletter-input"
            >
            <button type="submit" class="btn btn-primary">Subscribe</button>
          </form>
        </div>
      </div>
    </section>

    <!-- Main Footer -->
    <div class="main-footer">
      <div class="container">
        <div class="footer-grid">
          <!-- Contact Info -->
          <div class="footer-column">
            <h4>CONTACT US</h4>
            <div class="contact-info">
              <div class="contact-item">
                <strong>📞 Telephone</strong>
                <p>0800 298 9230</p>
              </div>
              <div class="contact-item">
                <strong>✉️ Email</strong>
                <p>sales@pinders.com</p>
              </div>
              <div class="contact-item">
                <strong>📍 Address</strong>
                <p>Pinders Workwear<br>Unit 5, Industrial Estate<br>Bradford, BD10 8TY</p>
              </div>
            </div>
          </div>

          <!-- Customer Service -->
          <div class="footer-column">
            <h4>CUSTOMER SERVICE</h4>
            <ul class="footer-links">
              <li><router-link to="/contact">Contact Us</router-link></li>
              <li><router-link to="/delivery">Delivery Information</router-link></li>
              <li><router-link to="/returns">Returns & Exchanges</router-link></li>
              <li><router-link to="/size-guide">Size Guide</router-link></li>
              <li><router-link to="/faq">FAQ</router-link></li>
              <li><router-link to="/terms">Terms & Conditions</router-link></li>
              <li><router-link to="/privacy">Privacy Policy</router-link></li>
            </ul>
          </div>

          <!-- My Account -->
          <div class="footer-column">
            <h4>MY ACCOUNT</h4>
            <ul class="footer-links">
              <li><router-link to="/login">Sign In</router-link></li>
              <li><router-link to="/register">Register</router-link></li>
              <li><router-link to="/order-history">Order History</router-link></li>
              <li><router-link to="/wishlist">Wishlist</router-link></li>
              <li><router-link to="/track-order">Track Order</router-link></li>
            </ul>
          </div>

          <!-- Information -->
          <div class="footer-column">
            <h4>INFORMATION</h4>
            <ul class="footer-links">
              <li><router-link to="/about">About Us</router-link></li>
              <li><router-link to="/corporate">Corporate Accounts</router-link></li>
              <li><router-link to="/bespoke">Bespoke Workwear</router-link></li>
              <li><router-link to="/blog">Workwear Blog</router-link></li>
              <li><router-link to="/sitemap">Site Map</router-link></li>
            </ul>
          </div>

          <!-- Payment & Security -->
          <div class="footer-column">
            <h4>PAYMENT & SECURITY</h4>
            <div class="payment-methods">
              <div class="payment-icons">
                <span class="payment-icon">💳</span>
                <span class="payment-icon">🔒</span>
                <span class="payment-icon">🛡️</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="bottom-footer">
      <div class="container">
        <div class="bottom-footer-content">
          <div class="copyright">
            <p>&copy; 2024 Pinders Workwear. All rights reserved.</p>
          </div>
          <div class="social-links">
            <a href="#" class="social-link">📘</a>
            <a href="#" class="social-link">🐦</a>
            <a href="#" class="social-link">📷</a>
            <a href="#" class="social-link">💼</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')

const subscribeNewsletter = () => {
  if (email.value) {
    console.log('Subscribed:', email.value)
    email.value = ''
    // Add toast notification here
  }
}
</script>

<style scoped>
.footer {
  background: var(--primary-black);
  color: var(--primary-white);
}

/* Newsletter Section */
.newsletter-section {
  background: var(--primary-red);
  padding: 50px 0;
}

.newsletter-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 40px;
}

.newsletter-text h3 {
  font-size: 24px;
  margin-bottom: 10px;
  font-weight: 700;
}

.newsletter-text p {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
}

.newsletter-form {
  display: flex;
  gap: 10px;
  flex: 1;
  max-width: 500px;
}

.newsletter-input {
  flex: 1;
  padding: 12px 20px;
  border: none;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
}

.newsletter-input:focus {
  outline: none;
}

/* Main Footer */
.main-footer {
  padding: 60px 0 40px;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
}

.footer-column h4 {
  color: var(--primary-red);
  margin-bottom: 20px;
  font-size: 18px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Contact Info */
.contact-item {
  margin-bottom: 20px;
}

.contact-item strong {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
}

.contact-item p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

/* Footer Links */
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 12px;
}

.footer-links a {
  color: var(--primary-white);
  text-decoration: none;
  font-size: 14px;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.footer-links a:hover {
  opacity: 1;
  color: var(--primary-red);
}

/* Payment Methods */
.payment-icons {
  display: flex;
  gap: 10px;
}

.payment-icon {
  font-size: 24px;
  opacity: 0.8;
}

/* Bottom Footer */
.bottom-footer {
  border-top: 1px solid #333;
  padding: 20px 0;
  background: #111;
}

.bottom-footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.copyright p {
  margin: 0;
  font-size: 14px;
  opacity: 0.7;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-link {
  color: var(--primary-white);
  text-decoration: none;
  font-size: 18px;
  opacity: 0.7;
  transition: opacity 0.3s ease;
}

.social-link:hover {
  opacity: 1;
  color: var(--primary-red);
}

/* Responsive */
@media (max-width: 768px) {
  .newsletter-content {
    flex-direction: column;
    text-align: center;
    gap: 20px;
  }

  .newsletter-form {
    width: 100%;
  }

  .footer-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }

  .bottom-footer-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
}
</style>