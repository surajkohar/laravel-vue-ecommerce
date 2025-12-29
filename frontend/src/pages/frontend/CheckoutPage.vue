<template>
  <FrontendLayout>
    <div class="checkout-page">
      <div class="container">
        <!-- Progress Indicator -->
        <div class="checkout-progress">
          <div class="progress-steps">
            <div class="step active" :class="{ completed: currentStep > 1 }">
              <div class="step-number">1</div>
              <span class="step-label">Information</span>
            </div>
            <div class="step" :class="{ active: currentStep === 2, completed: currentStep > 2 }">
              <div class="step-number">2</div>
              <span class="step-label">Shipping</span>
            </div>
            <div class="step" :class="{ active: currentStep === 3, completed: currentStep > 3 }">
              <div class="step-number">3</div>
              <span class="step-label">Payment</span>
            </div>
            <div class="step" :class="{ active: currentStep === 4 }">
              <div class="step-number">4</div>
              <span class="step-label">Confirmation</span>
            </div>
          </div>
        </div>

        <div class="checkout-layout">
          <!-- Main Checkout Form -->
          <div class="checkout-main">
            <!-- Step 1: Customer Information -->
            <div v-if="currentStep === 1" class="checkout-step">
              <div class="step-header">
                <h2>Contact Information</h2>
                <p>Already have an account? <a href="#" @click.prevent="handleLogin">Log in</a></p>
              </div>

              <form @submit.prevent="goToStep(2)" class="checkout-form">
                <div class="form-section">
                  <h3>Contact Details</h3>
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="email">Email Address *</label>
                      <input 
                        type="email" 
                        id="email" 
                        v-model="checkoutData.email" 
                        required 
                        placeholder="your@email.com"
                      >
                    </div>
                    <div class="form-group">
                      <label for="phone">Phone Number *</label>
                      <input 
                        type="tel" 
                        id="phone" 
                        v-model="checkoutData.phone" 
                        required 
                        placeholder="+91 98765 43210"
                      >
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <h3>Billing Address</h3>
                  <div class="form-actions">
                    <button type="button" class="btn btn-outline" @click="useSameAsShipping = !useSameAsShipping">
                      {{ useSameAsShipping ? 'Use different billing address' : 'Same as shipping address' }}
                    </button>
                  </div>
                  
                  <div v-if="!useSameAsShipping" class="form-grid">
                    <div class="form-group">
                      <label for="billingFirstName">First Name *</label>
                      <input 
                        type="text" 
                        id="billingFirstName" 
                        v-model="checkoutData.billingAddress.firstName" 
                        required
                      >
                    </div>
                    <div class="form-group">
                      <label for="billingLastName">Last Name *</label>
                      <input 
                        type="text" 
                        id="billingLastName" 
                        v-model="checkoutData.billingAddress.lastName" 
                        required
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="billingCompany">Company (Optional)</label>
                      <input 
                        type="text" 
                        id="billingCompany" 
                        v-model="checkoutData.billingAddress.company"
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="billingAddress1">Address Line 1 *</label>
                      <input 
                        type="text" 
                        id="billingAddress1" 
                        v-model="checkoutData.billingAddress.address1" 
                        required
                        placeholder="House No., Street, Area"
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="billingAddress2">Address Line 2 (Optional)</label>
                      <input 
                        type="text" 
                        id="billingAddress2" 
                        v-model="checkoutData.billingAddress.address2"
                        placeholder="Landmark, Building Name"
                      >
                    </div>
                    <div class="form-group">
                      <label for="billingCity">City *</label>
                      <input 
                        type="text" 
                        id="billingCity" 
                        v-model="checkoutData.billingAddress.city" 
                        required
                      >
                    </div>
                    <div class="form-group">
                      <label for="billingPostcode">Pincode *</label>
                      <input 
                        type="text" 
                        id="billingPostcode" 
                        v-model="checkoutData.billingAddress.postcode" 
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                      >
                    </div>
                    <div class="form-group">
                      <label for="billingCountry">Country *</label>
                      <select 
                        id="billingCountry" 
                        v-model="checkoutData.billingAddress.country" 
                        required
                      >
                        <option value="IN">India</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-primary btn-continue">
                    Continue to Shipping
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </form>
            </div>

            <!-- Step 2: Shipping Method -->
            <div v-if="currentStep === 2" class="checkout-step">
              <div class="step-header">
                <h2>Shipping Method</h2>
                <p>Choose how you want to get your order</p>
              </div>

              <form @submit.prevent="goToStep(3)" class="checkout-form">
                <div class="form-section">
                  <h3>Shipping Address</h3>
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="shippingFirstName">First Name *</label>
                      <input 
                        type="text" 
                        id="shippingFirstName" 
                        v-model="checkoutData.shippingAddress.firstName" 
                        required
                      >
                    </div>
                    <div class="form-group">
                      <label for="shippingLastName">Last Name *</label>
                      <input 
                        type="text" 
                        id="shippingLastName" 
                        v-model="checkoutData.shippingAddress.lastName" 
                        required
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="shippingCompany">Company (Optional)</label>
                      <input 
                        type="text" 
                        id="shippingCompany" 
                        v-model="checkoutData.shippingAddress.company"
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="shippingAddress1">Address Line 1 *</label>
                      <input 
                        type="text" 
                        id="shippingAddress1" 
                        v-model="checkoutData.shippingAddress.address1" 
                        required
                        placeholder="House No., Street, Area"
                      >
                    </div>
                    <div class="form-group full-width">
                      <label for="shippingAddress2">Address Line 2 (Optional)</label>
                      <input 
                        type="text" 
                        id="shippingAddress2" 
                        v-model="checkoutData.shippingAddress.address2"
                        placeholder="Landmark, Building Name"
                      >
                    </div>
                    <div class="form-group">
                      <label for="shippingCity">City *</label>
                      <input 
                        type="text" 
                        id="shippingCity" 
                        v-model="checkoutData.shippingAddress.city" 
                        required
                      >
                    </div>
                    <div class="form-group">
                      <label for="shippingPostcode">Pincode *</label>
                      <input 
                        type="text" 
                        id="shippingPostcode" 
                        v-model="checkoutData.shippingAddress.postcode" 
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                      >
                    </div>
                    <div class="form-group">
                      <label for="shippingCountry">Country *</label>
                      <select 
                        id="shippingCountry" 
                        v-model="checkoutData.shippingAddress.country" 
                        required
                      >
                        <option value="IN">India</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <h3>Shipping Options</h3>
                  <div class="shipping-options">
                    <div 
                      v-for="option in shippingOptions" 
                      :key="option.id"
                      class="shipping-option"
                      :class="{ selected: checkoutData.shippingMethod === option.id }"
                      @click="checkoutData.shippingMethod = option.id"
                    >
                      <div class="option-radio">
                        <input 
                          type="radio" 
                          :id="`shipping-${option.id}`"
                          :value="option.id"
                          v-model="checkoutData.shippingMethod"
                          class="radio-input"
                        >
                        <label :for="`shipping-${option.id}`" class="radio-label"></label>
                      </div>
                      <div class="option-details">
                        <div class="option-name">{{ option.name }}</div>
                        <div class="option-description">{{ option.description }}</div>
                      </div>
                      <div class="option-price">
                        {{ formatShipping(option.price) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="button" class="btn btn-outline" @click="goToStep(1)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back to Information
                  </button>
                  <button type="submit" class="btn btn-primary btn-continue">
                    Continue to Payment
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </form>
            </div>

            <!-- Step 3: Payment -->
            <div v-if="currentStep === 3" class="checkout-step">
              <div class="step-header">
                <h2>Payment Method</h2>
                <p>Choose how you want to pay</p>
              </div>

              <!-- Payment Config Info -->
              <div class="payment-config-info">
                <div class="config-badge" v-if="settingsStore.testMode">
                  <span class="test-mode-badge">TEST MODE</span>
                  <small>No real payment required for testing</small>
                </div>
                <div class="config-warning" v-if="!settingsStore.stripeEnabled && !settingsStore.testMode && checkoutData.paymentMethod === 'card'">
                  <span>⚠️ Card payments are currently disabled</span>
                </div>
                <div class="config-info" v-if="settingsStore.testMode && checkoutData.paymentMethod === 'card'">
                  <span>✅ In test mode, card payments will be simulated without real transaction</span>
                </div>
              </div>

              <form @submit.prevent="processPayment" class="checkout-form">
                <div class="form-section">
                  <h3>Payment Options</h3>
                  <div class="payment-options">
                    <div 
                      v-for="method in paymentMethods" 
                      :key="method.id"
                      class="payment-method"
                      :class="{ 
                        selected: checkoutData.paymentMethod === method.id,
                        disabled: method.disabled 
                      }"
                      @click="selectPaymentMethod(method)"
                    >
                      <div class="method-radio">
                        <input 
                          type="radio" 
                          :id="`payment-${method.id}`"
                          :value="method.id"
                          v-model="checkoutData.paymentMethod"
                          class="radio-input"
                          :disabled="method.disabled"
                        >
                        <label :for="`payment-${method.id}`" class="radio-label"></label>
                      </div>
                      <div class="method-icon">
                        <span v-html="method.icon"></span>
                      </div>
                      <div class="method-name">{{ method.name }}</div>
                      <div class="method-description" v-if="method.description">
                        {{ method.description }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- COD Information -->
                <div v-if="checkoutData.paymentMethod === 'cod'" class="cod-info-section">
                  <div class="cod-info">
                    <div class="cod-icon">💰</div>
                    <div class="cod-details">
                      <h4>Cash on Delivery</h4>
                      <p>Pay when you receive your order. Please note:</p>
                      <ul>
                        <li>Exact change is preferred</li>
                        <li>Available for orders under {{ formatPrice(codMaxAmount) }}</li>
                        <li>Available only in India</li>
                        <li>Additional verification may be required</li>
                        <li v-if="!isCodAvailableForCountry">
                          <strong>Note:</strong> COD not available for your selected country
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Credit Card Form (Only show if Stripe is enabled or in test mode) -->
                <div v-if="checkoutData.paymentMethod === 'card' && (settingsStore.stripeEnabled || settingsStore.testMode)" class="form-section">
                  <h3>Card Details</h3>
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label for="cardNumber">Card Number *</label>
                      <input 
                        type="text" 
                        id="cardNumber" 
                        v-model="checkoutData.card.number" 
                        :required="!settingsStore.testMode"
                        placeholder="1234 5678 9012 3456"
                        maxlength="19"
                        :disabled="settingsStore.testMode"
                      >
                      <small v-if="settingsStore.testMode" class="test-mode-hint">Test mode: Any card number works</small>
                    </div>
                    <div class="form-group">
                      <label for="cardName">Name on Card *</label>
                      <input 
                        type="text" 
                        id="cardName" 
                        v-model="checkoutData.card.name" 
                        :required="!settingsStore.testMode"
                        placeholder="John Doe"
                        :disabled="settingsStore.testMode"
                      >
                    </div>
                    <div class="form-group">
                      <label for="cardExpiry">Expiry Date *</label>
                      <input 
                        type="text" 
                        id="cardExpiry" 
                        v-model="checkoutData.card.expiry" 
                        :required="!settingsStore.testMode"
                        placeholder="MM/YY"
                        maxlength="5"
                        :disabled="settingsStore.testMode"
                      >
                    </div>
                    <div class="form-group">
                      <label for="cardCVC">CVC *</label>
                      <input 
                        type="text" 
                        id="cardCVC" 
                        v-model="checkoutData.card.cvc" 
                        :required="!settingsStore.testMode"
                        placeholder="123"
                        maxlength="4"
                        :disabled="settingsStore.testMode"
                      >
                    </div>
                  </div>
                </div>

                <!-- PayPal Button -->
                <div v-if="checkoutData.paymentMethod === 'paypal'" class="paypal-section">
                  <div class="paypal-info">
                    <p>You will be redirected to PayPal to complete your payment securely.</p>
                    <button type="button" class="btn btn-paypal" @click="processPayPal">
                      Continue with PayPal
                    </button>
                  </div>
                </div>

                <div class="form-section">
                  <h3>Order Notes</h3>
                  <div class="form-group full-width">
                    <label for="orderNotes">Special instructions for your order (optional)</label>
                    <textarea 
                      id="orderNotes" 
                      v-model="checkoutData.notes" 
                      rows="4" 
                      placeholder="Any special delivery instructions or notes about your order..."
                    ></textarea>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="button" class="btn btn-outline" @click="goToStep(2)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back to Shipping
                  </button>
                  <button 
                    type="submit" 
                    class="btn btn-primary btn-pay"
                    :disabled="processingPayment || isCodAmountExceeded"
                  >
                    <span v-if="processingPayment" class="spinner-small"></span>
                    <span v-else-if="settingsStore.testMode">Complete Order (Test Mode) - {{ formattedTotal }}</span>
                    <span v-else-if="isCodAmountExceeded">Order exceeds COD limit</span>
                    <span v-else>Complete Order - {{ formattedTotal }}</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- Step 4: Confirmation -->
            <div v-if="currentStep === 4" class="checkout-step confirmation-step">
              <div class="confirmation-content">
                <div class="confirmation-icon">
                  <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" 
                          stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01L9 11.01" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <h2>Order Confirmed!</h2>
                <p class="confirmation-subtitle">Thank you for your purchase</p>
                
                <!-- Test Mode Indicator in Confirmation -->
                <div v-if="settingsStore.testMode" class="test-mode-confirmation">
                  <span class="test-mode-badge">TEST ORDER</span>
                  <p>This order was placed in test mode. No real payment was processed.</p>
                </div>

                <!-- COD Confirmation Note -->
                <div v-if="checkoutData.paymentMethod === 'cod'" class="cod-confirmation-note">
                  <span class="cod-badge">CASH ON DELIVERY</span>
                  <p>Your order will be shipped and payment will be collected upon delivery.</p>
                </div>
                
                <div class="order-summary">
                  <div class="order-info">
                    <div class="info-item">
                      <span class="info-label">Order Number:</span>
                      <span class="info-value">#{{ orderNumber }}</span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">Order Date:</span>
                      <span class="info-value">{{ orderDate }}</span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">Email:</span>
                      <span class="info-value">{{ checkoutData.email }}</span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">Total Amount:</span>
                      <span class="info-value highlight">{{ formattedTotal }}</span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">Payment Method:</span>
                      <span class="info-value">{{ getPaymentMethodName(checkoutData.paymentMethod) }}</span>
                    </div>
                    <div class="info-item" v-if="settingsStore.testMode">
                      <span class="info-label">Payment Status:</span>
                      <span class="info-value test-status">Test Mode - Simulated</span>
                    </div>
                    <div class="info-item" v-else-if="checkoutData.paymentMethod === 'cod'">
                      <span class="info-label">Payment Status:</span>
                      <span class="info-value cod-status">Pending (Cash on Delivery)</span>
                    </div>
                  </div>
                </div>

                <div class="confirmation-actions">
                  <router-link to="/orders" class="btn btn-primary">
                    View Order Details
                  </router-link>
                  <router-link to="/products" class="btn btn-outline">
                    Continue Shopping
                  </router-link>
                </div>

                <div class="confirmation-note">
                  <p v-if="checkoutData.paymentMethod === 'cod'">
                    <strong>Cash on Delivery Order:</strong> Please have exact change ready. 
                    Our delivery agent will collect payment when your order is delivered.
                  </p>
                  <p v-else>
                    We've sent a confirmation email to <strong>{{ checkoutData.email }}</strong>. 
                    You'll receive a shipping confirmation email when your order ships.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary Sidebar -->
          <div class="checkout-sidebar">
            <div class="order-summary-card">
              <h3>Order Summary</h3>
              
              <!-- Show direct checkout item if applicable -->
              <div v-if="isDirectCheckout && directCheckoutProduct" class="direct-checkout-item">
                <div class="order-items">
                  <div class="order-item">
                    <div class="item-image">
                      <img :src="directCheckoutProduct.image" :alt="directCheckoutProduct.name">
                      <span class="item-quantity">{{ directCheckoutProduct.quantity }}</span>
                    </div>
                    <div class="item-details">
                      <h4 class="item-name">{{ directCheckoutProduct.name }}</h4>
                      <div class="item-variants" v-if="directCheckoutProduct.variant_name || directCheckoutProduct.size_title">
                        <span class="variant">{{ directCheckoutProduct.variant_name }}</span>
                        <span class="variant" v-if="directCheckoutProduct.size_title">Size: {{ directCheckoutProduct.size_title }}</span>
                      </div>
                    </div>
                    <div class="item-price">{{ formatItemPrice(directCheckoutProduct.price, directCheckoutProduct.quantity) }}</div>
                  </div>
                </div>
              </div>
              
              <!-- Show cart items for regular checkout -->
              <div v-else class="order-items">
                <div v-for="item in cartStore.items" :key="`${item.id}-${item.variant_id}`" class="order-item">
                  <div class="item-image">
                    <img :src="item.image" :alt="item.name">
                    <span class="item-quantity">{{ item.quantity }}</span>
                  </div>
                  <div class="item-details">
                    <h4 class="item-name">{{ item.name }}</h4>
                    <div class="item-variants" v-if="item.variant_name || item.size">
                      <span class="variant">{{ item.variant_name }}</span>
                      <span class="variant" v-if="item.size">Size: {{ item.size }}</span>
                    </div>
                  </div>
                  <div class="item-price">{{ formatItemPrice(item.price, item.quantity) }}</div>
                </div>
              </div>

              <div class="order-totals">
                <div class="total-row">
                  <span>Subtotal</span>
                  <span>{{ formatPrice(subtotal) }}</span>
                </div>
                <div class="total-row">
                  <span>Shipping</span>
                  <span>{{ formatShipping(selectedShippingPrice) }}</span>
                </div>
                <div class="total-row">
                  <span>GST ({{ settingsStore.taxRate }}%)</span>
                  <span>{{ formatPrice(vatAmount) }}</span>
                </div>
                <div class="total-divider"></div>
                <div class="total-row grand-total">
                  <span>Total</span>
                  <span>{{ formattedTotal }}</span>
                </div>
              </div>

              <!-- COD Warning -->
              <div v-if="checkoutData.paymentMethod === 'cod' && totalAmount > codMaxAmount" class="cod-warning">
                <div class="warning-icon">⚠️</div>
                <p>Cash on Delivery not available for orders above {{ formatPrice(codMaxAmount) }}</p>
              </div>

              <!-- Free shipping notification -->
              <div v-if="settingsStore.freeShippingThreshold > 0 && subtotal < settingsStore.freeShippingThreshold" class="free-shipping-info">
                <div class="progress-bar">
                  <div 
                    class="progress-fill" 
                    :style="{ width: Math.min((subtotal / settingsStore.freeShippingThreshold) * 100, 100) + '%' }"
                  ></div>
                </div>
                <p>
                  Add {{ formatPrice(settingsStore.freeShippingThreshold - subtotal) }} 
                  more for free shipping!
                </p>
              </div>

              <div class="security-badges">
                <div class="security-item">
                  <div class="security-icon">🔒</div>
                  <span>Secure SSL Encryption</span>
                </div>
                <div class="security-item">
                  <div class="security-icon">💰</div>
                  <span>Money Back Guarantee</span>
                </div>
              </div>
            </div>

            <!-- Help Section -->
            <div class="help-card">
              <h4>Need Help?</h4>
              <p>Our customer service team is here to help with any questions.</p>
              <div class="contact-info">
                <div class="contact-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M22 16.92V19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V16.92M22 16.92C21.674 16.945 21.347 16.96 21 16.96C17.134 16.96 13.57 15.51 10.889 13.111C8.208 10.712 6.573 7.468 6.134 4M22 16.92C21.329 13.92 19.532 11.354 17 9.75" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span>{{ settingsStore.settings.site.contact_phone }}</span>
                </div>
                <div class="contact-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span>{{ settingsStore.settings.site.contact_email }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@/store/cart'
import { useSettingsStore } from '@/store/settings'
import { useRouter, useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import api from '@/services/api'

const cartStore = useCartStore()
const settingsStore = useSettingsStore()
const router = useRouter()
const route = useRoute()

const currentStep = ref(1)
const processingPayment = ref(false)
const useSameAsShipping = ref(true)
const isDirectCheckout = ref(false)
const directCheckoutProduct = ref(null)

// COD Settings (Hardcoded for India)
const codMaxAmount = 1000 // Maximum amount for COD in INR
const codEnabled = true // COD is enabled
const codCountries = ['IN'] // Only available in India

// Checkout data
const checkoutData = ref({
  email: '',
  phone: '',
  shippingAddress: {
    firstName: '',
    lastName: '',
    company: '',
    address1: '',
    address2: '',
    city: '',
    postcode: '',
    country: 'IN' // Default to India
  },
  billingAddress: {
    firstName: '',
    lastName: '',
    company: '',
    address1: '',
    address2: '',
    city: '',
    postcode: '',
    country: 'IN' // Default to India
  },
  shippingMethod: 'standard',
  paymentMethod: 'cod', // Default to COD for India
  card: {
    number: '',
    name: '',
    expiry: '',
    cvc: ''
  },
  notes: ''
})

// Order info
const orderNumber = ref('PIND' + Math.floor(Math.random() * 1000000))
const orderDate = ref(new Date().toLocaleDateString('en-GB'))

// Computed properties
const subtotal = computed(() => {
  if (isDirectCheckout.value && directCheckoutProduct.value) {
    return directCheckoutProduct.value.price * directCheckoutProduct.value.quantity
  }
  return cartStore.subtotal
})

const shippingOptions = computed(() => {
  const defaultShippingCost = settingsStore.settings.shipping?.default_cost || 99 // Default 99 INR for India
  
  const options = [
    { 
      id: 'standard', 
      name: 'Standard Delivery', 
      description: '5-7 business days', 
      price: defaultShippingCost 
    },
    { 
      id: 'express', 
      name: 'Express Delivery', 
      description: '2-3 business days', 
      price: 199 
    },
    { 
      id: 'next-day', 
      name: 'Next Day Delivery', 
      description: 'Next working day', 
      price: 299 
    }
  ]
  
  // Add free shipping option if cart qualifies
  if (settingsStore.isFreeShipping(subtotal.value)) {
    options.push({
      id: 'free',
      name: 'Free Delivery',
      description: '7-10 business days',
      price: 0
    })
  }
  
  return options
})

const paymentMethods = computed(() => {
  const methods = [
    { id: 'cod', name: 'Cash on Delivery', icon: '💰' },
    { id: 'card', name: 'Credit/Debit Card', icon: '💳' },
    { id: 'paypal', name: 'PayPal', icon: '🔵' },
    { id: 'google-pay', name: 'Google Pay', icon: '📱' }
  ]
  
  return methods.map(method => {
    let disabled = false
    let description = ''
    
    // If COD is disabled
    if (method.id === 'cod' && !codEnabled) {
      disabled = true
      description = 'Currently unavailable'
    }
    
    // If Stripe is disabled and not in test mode, disable card/GPay
    if (!settingsStore.stripeEnabled && !settingsStore.testMode && 
        ['card', 'google-pay'].includes(method.id)) {
      disabled = true
      description = 'Currently unavailable'
    }
    
    // If COD amount exceeded
    if (method.id === 'cod' && totalAmount.value > codMaxAmount) {
      disabled = true
      description = `Available only for orders under ${formatPrice(codMaxAmount)}`
    }
    
    // Check country restriction for COD
    if (method.id === 'cod' && checkoutData.value.shippingAddress.country !== 'IN') {
      disabled = true
      description = 'Available only in India'
    }
    
    return { ...method, disabled, description }
  })
})

const selectedShipping = computed(() => {
  return shippingOptions.value.find(option => option.id === checkoutData.value.shippingMethod)
})

const selectedShippingPrice = computed(() => {
  return selectedShipping.value?.price || 0
})

// Calculate tax using settings
const vatAmount = computed(() => {
  return settingsStore.calculateTax(subtotal.value)
})

const totalAmount = computed(() => {
  return subtotal.value + selectedShippingPrice.value + vatAmount.value
})

// Format total amount
const formattedTotal = computed(() => {
  return settingsStore.formatPrice(totalAmount.value)
})

// COD specific computed properties
const isCodAvailableForCountry = computed(() => {
  return checkoutData.value.shippingAddress.country === 'IN'
})

const isCodAmountExceeded = computed(() => {
  if (checkoutData.value.paymentMethod !== 'cod') return false
  return totalAmount.value > codMaxAmount
})

// Helper functions
const formatPrice = (amount) => {
  return settingsStore.formatPrice(amount)
}

const formatShipping = (cost) => {
  return settingsStore.formatShipping(cost)
}

const formatItemPrice = (price, quantity = 1) => {
  const total = price * quantity
  return settingsStore.formatPrice(total)
}

const getPaymentMethodName = (methodId) => {
  const method = paymentMethods.value.find(m => m.id === methodId)
  return method ? method.name : methodId
}

// Helper function to get Stripe payment method types
const getPaymentMethodTypes = (paymentMethod) => {
  switch (paymentMethod) {
    case 'google-pay':
      return ['card', 'google_pay']
    case 'apple-pay':
      return ['card', 'apple_pay']
    case 'card':
      return ['card']
    default:
      return ['card']
  }
}

// Initialize checkout data with test values in test mode
const initializeTestData = () => {
  if (settingsStore.testMode) {
    checkoutData.value.email = 'test@example.com'
    checkoutData.value.phone = '+91 98765 43210'
    checkoutData.value.shippingAddress = {
      firstName: 'Raj',
      lastName: 'Sharma',
      company: '',
      address1: '123 Main Street',
      address2: '',
      city: 'Mumbai',
      postcode: '400001',
      country: 'IN'
    }
    checkoutData.value.card = {
      number: '4242 4242 4242 4242',
      name: 'Raj Sharma',
      expiry: '12/30',
      cvc: '123'
    }
  }
}

// Methods
const goToStep = (step) => {
  currentStep.value = step
}

const handleLogin = () => {
  // Handle login logic
  console.log('Login clicked')
}

const selectPaymentMethod = (method) => {
  if (method.disabled) {
    return
  }
  checkoutData.value.paymentMethod = method.id
}

const validateForm = () => {
  // Basic validation
  if (!checkoutData.value.email || !checkoutData.value.phone) {
    toast.error('Please fill in all required fields')
    return false
  }
  
  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(checkoutData.value.email)) {
    toast.error('Please enter a valid email address')
    return false
  }
  
  // Phone validation for India
  const phoneRegex = /^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/
  if (!phoneRegex.test(checkoutData.value.phone.replace(/\s/g, ''))) {
    toast.error('Please enter a valid Indian phone number')
    return false
  }
  
  // Shipping address validation
  if (!checkoutData.value.shippingAddress.firstName || !checkoutData.value.shippingAddress.lastName) {
    toast.error('Please enter your shipping name')
    return false
  }
  
  if (!checkoutData.value.shippingAddress.address1 || !checkoutData.value.shippingAddress.city || 
      !checkoutData.value.shippingAddress.postcode) {
    toast.error('Please complete your shipping address')
    return false
  }
  
  // Pincode validation for India (6 digits)
  const pincodeRegex = /^[1-9][0-9]{5}$/
  if (!pincodeRegex.test(checkoutData.value.shippingAddress.postcode)) {
    toast.error('Please enter a valid 6-digit pincode')
    return false
  }
  
  // If not using same as shipping, validate billing address
  if (!useSameAsShipping.value) {
    if (!checkoutData.value.billingAddress.firstName || !checkoutData.value.billingAddress.lastName) {
      toast.error('Please enter your billing name')
      return false
    }
    
    if (!checkoutData.value.billingAddress.address1 || !checkoutData.value.billingAddress.city || 
        !checkoutData.value.billingAddress.postcode) {
      toast.error('Please complete your billing address')
      return false
    }
    
    if (!pincodeRegex.test(checkoutData.value.billingAddress.postcode)) {
      toast.error('Please enter a valid 6-digit pincode for billing address')
      return false
    }
  }
  
  // COD specific validation
  if (checkoutData.value.paymentMethod === 'cod') {
    // Check amount limit
    if (totalAmount.value > codMaxAmount) {
      toast.error(`Cash on Delivery is only available for orders under ${formatPrice(codMaxAmount)}`)
      return false
    }
    
    // Check country
    if (checkoutData.value.shippingAddress.country !== 'IN') {
      toast.error('Cash on Delivery is only available in India')
      return false
    }
  }
  
  return true
}

const processPayment = async () => {
  // Validate form
  if (!validateForm()) {
    return
  }
  
  // Special validation for COD
  if (checkoutData.value.paymentMethod === 'cod') {
    // Double check amount limit
    if (totalAmount.value > codMaxAmount) {
      toast.error(`Cash on Delivery is only available for orders under ${formatPrice(codMaxAmount)}`)
      return
    }
    
    // Double check country
    if (checkoutData.value.shippingAddress.country !== 'IN') {
      toast.error('Cash on Delivery is only available in India')
      return
    }
  }
  
  processingPayment.value = true
  
  try {
    // Prepare order data
    const orderData = {
      checkout_type: isDirectCheckout.value ? 'direct' : 'cart',
      email: checkoutData.value.email,
      phone: checkoutData.value.phone,
      shipping_address: checkoutData.value.shippingAddress,
      billing_address: useSameAsShipping.value ? 
        checkoutData.value.shippingAddress : 
        checkoutData.value.billingAddress,
      shipping_method: checkoutData.value.shippingMethod,
      shipping_cost: selectedShippingPrice.value,
      payment_method: checkoutData.value.paymentMethod,
      notes: checkoutData.value.notes,
      subtotal: subtotal.value,
      tax_amount: vatAmount.value,
      total_amount: totalAmount.value,
      test_mode: settingsStore.testMode,
      // For direct checkout
      ...(isDirectCheckout.value && directCheckoutProduct.value ? {
        product_id: directCheckoutProduct.value.product_id,
        variant_id: directCheckoutProduct.value.variant_id,
        size_id: directCheckoutProduct.value.size_id,
        quantity: directCheckoutProduct.value.quantity
      } : {})
    }
    
    // Set payment status based on payment method
    if (settingsStore.testMode) {
      console.log('🔧 Test mode: Simulating payment processing...')
      
      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 1500))
      
      // For test mode, all payments are successful
      orderData.payment_status = checkoutData.value.paymentMethod === 'cod' ? 'pending' : 'paid'
      orderData.stripe_payment_intent_id = 'test_' + Date.now()
      
    } else if (checkoutData.value.paymentMethod === 'cod') {
      // Cash on Delivery - payment is pending
      orderData.payment_status = 'pending'
      orderData.stripe_payment_intent_id = null
      
    } else if (['card', 'google-pay'].includes(checkoutData.value.paymentMethod) && settingsStore.stripeEnabled) {
      // Real Stripe payment processing
      try {
        // Create payment intent
        const paymentIntentResponse = await api.post('/frontend/checkout/create-payment-intent', {
          amount: totalAmount.value,
          currency: 'inr', // INR for India
          payment_method_types: getPaymentMethodTypes(checkoutData.value.paymentMethod)
        })
        
        if (paymentIntentResponse.data.status) {
          orderData.stripe_payment_intent_id = paymentIntentResponse.data.data.payment_intent_id
          orderData.client_secret = paymentIntentResponse.data.data.client_secret
          // For Stripe payments, we'll update status after successful payment
          orderData.payment_status = 'pending'
        } else {
          throw new Error('Failed to create payment intent')
        }
        
        // Here you would integrate with Stripe Elements to confirm payment
        // This is a simplified version
        
      } catch (error) {
        toast.error('Payment processing failed: ' + (error.message || 'Unknown error'))
        processingPayment.value = false
        return
      }
    } else if (checkoutData.value.paymentMethod === 'paypal') {
      // PayPal processing
      orderData.payment_status = 'pending'
      orderData.stripe_payment_intent_id = null
    }
    
    // Submit order to backend
    const endpoint = isDirectCheckout.value ? '/frontend/checkout/direct' : '/frontend/checkout/cart'
    
    const orderResponse = await api.post(endpoint, orderData)
    
    if (orderResponse.data.status) {
      // Store order info for confirmation
      orderNumber.value = orderResponse.data.data.order_number || orderNumber.value
      
      // Clear cart if not direct checkout
      if (!isDirectCheckout.value) {
        cartStore.clearCart()
      }
      
      // Clear direct checkout data
      if (isDirectCheckout.value) {
        sessionStorage.removeItem('direct_checkout_product')
      }
      
      // Go to confirmation step
      goToStep(4)
      
      toast.success('Order placed successfully!')
    } else {
      throw new Error(orderResponse.data.message || 'Failed to place order')
    }
    
  } catch (error) {
    console.error('Order processing error:', error)
    toast.error('Failed to process order: ' + (error.message || 'Unknown error'))
  } finally {
    processingPayment.value = false
  }
}

const processPayPal = () => {
  // Handle PayPal payment
  toast.info('PayPal integration would be implemented here')
  // For now, just process as normal payment
  processPayment()
}

const loadDirectCheckoutProduct = () => {
  const query = route.query
  const storedProduct = sessionStorage.getItem('direct_checkout_product')
  
  if (query.type === 'direct' && storedProduct) {
    isDirectCheckout.value = true
    directCheckoutProduct.value = JSON.parse(storedProduct)
  }
}

// Lifecycle
onMounted(async () => {
  // Ensure settings are loaded
  if (!settingsStore.isLoaded && !settingsStore.isLoading) {
    await settingsStore.fetchSettings()
  }
  
  // Initialize test data if in test mode
  if (settingsStore.testMode) {
    initializeTestData()
  }
  
  // Load direct checkout product if applicable
  loadDirectCheckoutProduct()
  
  // Check if we have items to checkout
  if (!isDirectCheckout.value && !cartStore.hasItems) {
    router.push('/cart')
    return
  }
  
  // Auto-select free shipping if applicable
  if (settingsStore.isFreeShipping(subtotal.value)) {
    checkoutData.value.shippingMethod = 'free'
  }
})
</script>

<style scoped>
/* Only add these new CSS classes for COD */

/* COD Info Section */
.cod-info-section {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 1rem;
  margin: 1rem 0;
}

.cod-info {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.cod-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.cod-details h4 {
  margin: 0 0 0.5rem 0;
  color: #166534;
}

.cod-details p {
  margin: 0 0 0.5rem 0;
  color: #15803d;
}

.cod-details ul {
  margin: 0.5rem 0 0 0;
  padding-left: 1.2rem;
  color: #166534;
}

.cod-details li {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

/* COD Warning in Sidebar */
.cod-warning {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 8px;
  padding: 0.75rem;
  margin: 1rem 0;
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
}

.warning-icon {
  font-size: 1.2rem;
  flex-shrink: 0;
}

.cod-warning p {
  margin: 0;
  color: #92400e;
  font-size: 0.9rem;
}

/* COD Confirmation Note */
.cod-confirmation-note {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.cod-badge {
  background: #22c55e;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
  margin-bottom: 0.5rem;
}

.cod-confirmation-note p {
  margin: 0;
  color: #166534;
}

/* COD Status in Order Info */
.cod-status {
  color: #d97706;
  font-weight: 600;
}

/* Payment config info styles */
.payment-config-info {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
}

.config-badge {
  display: flex;
  align-items: center;
  gap: 8px;
}

.test-mode-badge {
  background: #f59e0b;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.config-warning {
  color: #dc2626;
  font-size: 14px;
  margin-top: 8px;
}

.config-info {
  color: #059669;
  font-size: 14px;
  margin-top: 8px;
}

.payment-method.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.method-description {
  font-size: 12px;
  color: #6b7280;
  margin-top: 4px;
}

.test-mode-hint {
  font-size: 0.8rem;
  color: #666;
  margin-top: 4px;
}

/* Test mode confirmation */
.test-mode-confirmation {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
}

.test-status {
  color: #d97706;
  font-weight: 600;
}

/* Free shipping info */
.free-shipping-info {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: #10b981;
  transition: width 0.3s ease;
}

/* Spinner */
.spinner-small {
  width: 18px;
  height: 18px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Payment config info */
.payment-config-info {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
}

.config-badge {
  display: flex;
  align-items: center;
  gap: 8px;
}

.test-mode-badge {
  background: #f59e0b;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.config-warning {
  color: #dc2626;
  font-size: 14px;
  margin-top: 8px;
}

.config-info {
  color: #059669;
  font-size: 14px;
  margin-top: 8px;
}

.payment-method.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.method-description {
  font-size: 12px;
  color: #6b7280;
  margin-top: 4px;
}

.test-mode-hint {
  font-size: 0.8rem;
  color: #666;
  margin-top: 4px;
}

/* Test mode confirmation */
.test-mode-confirmation {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
}

.test-status {
  color: #d97706;
  font-weight: 600;
}

/* Free shipping info */
.free-shipping-info {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: #10b981;
  transition: width 0.3s ease;
}

.text-green-600 {
  color: #059669;
}

/* Direct checkout item */
.direct-checkout-item {
  margin-bottom: 1rem;
}

.checkout-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--gray-light) 0%, #f1f5f9 100%);
  padding: 2rem 0 4rem;
}

/* Progress Indicator */
.checkout-progress {
  margin-bottom: 3rem;
}

.progress-steps {
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 600px;
  margin: 0 auto;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 20px;
  right: -50%;
  width: 100%;
  height: 2px;
  background: var(--border-color);
  z-index: 1;
}

.step.completed:not(:last-child)::after {
  background: var(--primary-red);
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary-white);
  border: 2px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 0.5rem;
  position: relative;
  z-index: 2;
  transition: all 0.3s ease;
}

.step.active .step-number {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: var(--primary-white);
}

.step.completed .step-number {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: var(--primary-white);
}

.step-label {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--gray-medium);
  text-align: center;
}

.step.active .step-label {
  color: var(--primary-red);
  font-weight: 600;
}

/* Checkout Layout */
.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 3rem;
  align-items: start;
}

.checkout-main {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  overflow: hidden;
}

.checkout-step {
  padding: 2rem;
}

.confirmation-step {
  text-align: center;
  padding: 4rem 2rem;
}

/* Step Header */
.step-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.step-header h2 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
  font-size: 1.5rem;
}

.step-header p {
  margin: 0;
  color: var(--gray-medium);
}

.step-header a {
  color: var(--primary-red);
  text-decoration: none;
}

.step-header a:hover {
  text-decoration: underline;
}

/* Forms */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-section h3 {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.1rem;
  font-weight: 600;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 500;
  color: var(--primary-black);
  font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-red);
}

/* Remove any hover-only styles that might be hiding the button */
.form-actions {
  display: flex;
  justify-content: flex-start;
  /* Align to left instead of space-between */
  align-items: center;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
  margin-top: 1rem;
}

/* Ensure the button is always visible */
.form-actions .btn-outline {
  opacity: 1 !important;
  visibility: visible !important;
  display: inline-flex !important;
}

/* If there's any parent container hiding it, fix that too */
.form-section .form-actions {
  display: flex !important;
  visibility: visible !important;
}

/* Make sure the button has proper styling */
.btn-outline {
  background: transparent;
  border: 2px solid var(--primary-red);
  color: var(--primary-red);
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-outline:hover {
  background: var(--primary-red);
  color: white;
}

/* If the button is inside a specific container that might be hiding it */
.form-section>.form-actions {
  margin-bottom: 1.5rem;
}

/* Add this if the button is still not showing */
.billing-address-toggle {
  display: block !important;
  margin-bottom: 1.5rem;
}

/* Force show the button container */
[class*="billing"]~.form-actions,
[class*="address"]~.form-actions {
  display: flex !important;
}

.btn-continue,
.btn-pay {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Shipping Options */
.shipping-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.shipping-option {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.shipping-option:hover {
  border-color: var(--primary-red);
}

.shipping-option.selected {
  border-color: var(--primary-red);
  background: #fef2f2;
}

.option-radio {
  display: flex;
  align-items: center;
}

.radio-input {
  display: none;
}

.radio-label {
  width: 20px;
  height: 20px;
  border: 2px solid var(--border-color);
  border-radius: 50%;
  position: relative;
  cursor: pointer;
  transition: all 0.3s ease;
}

.radio-input:checked+.radio-label {
  border-color: var(--primary-red);
  background: var(--primary-red);
}

.radio-input:checked+.radio-label::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  background: var(--primary-white);
  border-radius: 50%;
}

.option-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.option-name {
  font-weight: 600;
  color: var(--primary-black);
}

.option-description {
  font-size: 0.85rem;
  color: var(--gray-medium);
}

.option-price {
  font-weight: 600;
  color: var(--primary-red);
}

/* Payment Options */
.payment-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.payment-method {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 1.5rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
}

.payment-method:hover {
  border-color: var(--primary-red);
}

.payment-method.selected {
  border-color: var(--primary-red);
  background: #fef2f2;
}

.method-radio {
  order: 3;
}

.method-icon {
  font-size: 2rem;
  order: 1;
}

.method-name {
  font-weight: 500;
  color: var(--primary-black);
  order: 2;
}

/* PayPal Section */
.paypal-section {
  padding: 2rem;
  background: #f8fafc;
  border-radius: 8px;
  text-align: center;
}

.paypal-info p {
  margin-bottom: 1.5rem;
  color: var(--gray-medium);
}

.btn-paypal {
  background: #FFC439;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 auto;
}

.btn-paypal:hover {
  background: #f2b432;
}

.paypal-logo {
  height: 20px;
}

/* Confirmation */
.confirmation-content {
  max-width: 500px;
  margin: 0 auto;
}

.confirmation-icon {
  margin-bottom: 1.5rem;
}

.confirmation-icon svg {
  color: #22c55e;
}

.confirmation-step h2 {
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  font-size: 2rem;
}

.confirmation-subtitle {
  color: var(--gray-medium);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.order-summary {
  background: #f8fafc;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.info-label {
  color: var(--gray-medium);
  font-weight: 500;
}

.info-value {
  color: var(--primary-black);
  font-weight: 600;
}

.info-value.highlight {
  color: var(--primary-red);
  font-size: 1.1rem;
}

.confirmation-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 2rem;
}

.confirmation-note {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 1.5rem;
  color: #166534;
}

.confirmation-note p {
  margin: 0;
}

/* Sidebar */
.checkout-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: sticky;
  top: 120px;
}

.order-summary-card {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  padding: 2rem;
}

.order-summary-card h3 {
  margin: 0 0 1.5rem 0;
  color: var(--primary-black);
  font-size: 1.3rem;
  text-align: center;
}

.order-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
  max-height: 300px;
  overflow-y: auto;
}

.order-item {
  display: grid;
  grid-template-columns: 50px 1fr auto;
  gap: 0.75rem;
  align-items: start;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f5f5f5;
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  position: relative;
  width: 50px;
  height: 50px;
  border-radius: 8px;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-quantity {
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--primary-red);
  color: var(--primary-white);
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.item-name {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-black);
  line-height: 1.3;
}

.item-variants {
  display: flex;
  gap: 0.5rem;
}

.variant {
  font-size: 0.75rem;
  color: var(--gray-medium);
  background: var(--gray-light);
  padding: 2px 6px;
  border-radius: 4px;
}

.item-price {
  font-weight: 600;
  color: var(--primary-red);
  font-size: 0.9rem;
}

.order-totals {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--gray-dark);
}

.total-row.grand-total {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-black);
}

.total-divider {
  height: 1px;
  background: var(--border-color);
  margin: 0.5rem 0;
}

.security-badges {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.security-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.85rem;
  color: var(--gray-medium);
}

.security-icon {
  font-size: 1.2rem;
}

/* Help Card */
.help-card {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  padding: 1.5rem;
}

.help-card h4 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
}

.help-card p {
  margin: 0 0 1rem 0;
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.9rem;
  color: var(--gray-dark);
}

/* Spinner */
.spinner-small {
  width: 18px;
  height: 18px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .checkout-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .checkout-sidebar {
    position: static;
  }
}

@media (max-width: 768px) {
  .progress-steps {
    flex-direction: column;
    gap: 1rem;
  }

  .step:not(:last-child)::after {
    display: none;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .payment-options {
    grid-template-columns: 1fr;
  }

  .confirmation-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .checkout-step {
    padding: 1.5rem;
  }

  .confirmation-step {
    padding: 2rem 1.5rem;
  }

  .order-summary-card {
    padding: 1.5rem;
  }
}

/* Add this to your global or component CSS */
.btn {
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
  min-height: 40px;
  min-width: 100px;
  opacity: 1 !important;
  visibility: visible !important;
}
</style>