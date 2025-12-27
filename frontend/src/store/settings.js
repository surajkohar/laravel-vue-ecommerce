// resources/js/store/settings.js

import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'; // Import your centralized API service

export const useSettingsStore = defineStore('settings', () => {
  // State
  const settings = ref({
    currency: {
      currency_symbol: '£',
      currency_position: 'left',
      decimal_separator: '.',
      thousand_separator: ',',
    },
    payment: {
      stripe_enabled: false,
      stripe_public_key: '',
      stripe_secret_key: '',
      stripe_webhook_secret: '',
      test_mode: true,
      currency_code: 'INR',
      currency_symbol: '₹',
      tax_rate: 20,
      free_shipping_threshold: 0,
    },
    site: {
      name: 'Pinders',
      logo: '',
      contact_email: '',
      contact_phone: '',
    },
    shipping: {
      default_cost: 4.99,
      free_threshold: 0,
    }
  })
  
  const isLoading = ref(false)
  const isLoaded = ref(false)
  const error = ref(null)

  // Computed properties for easy access
  const currencySymbol = computed(() => settings.value.currency.currency_symbol || settings.value.payment.currency_symbol)
  const currencyCode = computed(() => settings.value.payment.currency_code || settings.value.currency.currency)
  const currencyPosition = computed(() => settings.value.currency.currency_position || 'left')
  const stripeEnabled = computed(() => settings.value.payment.stripe_enabled)
  const testMode = computed(() => settings.value.payment.test_mode)
  const taxRate = computed(() => settings.value.payment.tax_rate)
  const siteName = computed(() => settings.value.site.name)
  const siteLogo = computed(() => settings.value.site.logo)
  const freeShippingThreshold = computed(() => settings.value.payment.free_shipping_threshold || settings.value.shipping.free_threshold)

  // Helper function to format price
  const formatPrice = (amount, showSymbol = true) => {
    const currency = settings.value.currency
    const amountNum = parseFloat(amount) || 0
    
    // Format number with correct decimal places
    const formatted = amountNum.toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
      useGrouping: false // We'll handle grouping manually
    })
    
    // Split integer and decimal parts
    const [integerPart, decimalPart] = formatted.split('.')
    
    // Add thousand separators to integer part
    let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, currency.thousand_separator)
    
    // Combine with decimal part
    let result = decimalPart ? 
      formattedInteger + currency.decimal_separator + decimalPart : 
      formattedInteger
    
    // Add currency symbol if needed
    if (showSymbol) {
      const symbol = currencySymbol.value
      if (currencyPosition.value === 'left') {
        result = symbol + result
      } else {
        result = result + ' ' + symbol
      }
    }
    
    return result
  }

  // Format price without symbol (just number)
  const formatNumber = (amount) => {
    return formatPrice(amount, false)
  }

  // Calculate tax amount
  const calculateTax = (amount) => {
    const rate = settings.value.payment.tax_rate / 100
    return parseFloat((amount * rate).toFixed(2))
  }

  // Check if free shipping applies
  const isFreeShipping = (subtotal) => {
    const threshold = freeShippingThreshold.value
    return threshold > 0 && subtotal >= threshold
  }

  // Get shipping cost based on subtotal
  const getShippingCost = (subtotal) => {
    if (isFreeShipping(subtotal)) {
      return 0
    }
    return settings.value.shipping.default_cost || 4.99
  }

  // Format shipping display
  const formatShipping = (cost) => {
    if (cost === 0) {
      return 'Free'
    }
    return formatPrice(cost)
  }

  // Fetch all settings from API
  const fetchSettings = async () => {
    // Return cached settings if already loaded
    if (isLoaded.value) {
      return settings.value
    }
    
    isLoading.value = true
    error.value = null
    
    try {
      const response = await api.get('/frontend/settings')
      
      if (response.data.status) {
        settings.value = response.data.data
        isLoaded.value = true
        console.log('Settings loaded:', settings.value)
        return settings.value
      } else {
        throw new Error(response.data.message || 'Failed to load settings')
      }
    } catch (err) {
      error.value = err.message
      console.error('Failed to fetch settings:', err)
      // Don't throw error here, use defaults
      return settings.value
    } finally {
      isLoading.value = false
    }
  }

  // Initialize settings on app load
  const initialize = async () => {
    if (!isLoaded.value && !isLoading.value) {
      await fetchSettings()
    }
  }

  // Clear settings (for logout)
  const clear = () => {
    settings.value = {
      currency: {
        currency: 'GBP',
        currency_symbol: '£',
        currency_position: 'left',
        decimal_separator: '.',
        thousand_separator: ',',
      },
      payment: {
        stripe_enabled: false,
        stripe_public_key: '',
        stripe_secret_key: '',
        stripe_webhook_secret: '',
        test_mode: true,
        currency_code: 'INR',
        currency_symbol: '₹',
        tax_rate: 20,
        free_shipping_threshold: 0,
      },
      site: {
        name: 'Pinders',
        logo: '',
        contact_email: '',
        contact_phone: '',
      },
      shipping: {
        default_cost: 4.99,
        free_threshold: 0,
      }
    }
    isLoaded.value = false
  }

  return {
    // State
    settings,
    isLoading,
    isLoaded,
    error,
    
    // Computed
    currencySymbol,
    currencyCode,
    currencyPosition,
    stripeEnabled,
    testMode,
    taxRate,
    siteName,
    siteLogo,
    freeShippingThreshold,
    
    // Methods
    fetchSettings,
    formatPrice,
    formatNumber,
    calculateTax,
    isFreeShipping,
    getShippingCost,
    formatShipping,
    initialize,
    clear,
  }
})