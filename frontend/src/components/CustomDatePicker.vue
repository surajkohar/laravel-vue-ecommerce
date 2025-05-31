<template>
  <div class="custom-datepicker" ref="dropdownRef">
    <div class="datepicker-input" @click="toggleDatePicker">
      <span class="input-text">{{ formattedDate || placeholder }}</span>
      <i class="fas fa-calendar-alt calendar-icon"></i>
    </div>

    <div class="datepicker-dropdown" v-if="showPicker">
      <div class="datepicker-header">
        <button @click="prevMonth" class="nav-btn">
          <i class="fas fa-chevron-left"></i>
        </button>

        <select v-model="selectedMonth" class="month-select">
          <option v-for="(m, i) in months" :value="i" :key="i">{{ m }}</option>
        </select>

        <select v-model="selectedYear" class="year-select">
          <option v-for="y in yearRange" :key="y">{{ y }}</option>
        </select>

        <button @click="nextMonth" class="nav-btn">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

      <div class="datepicker-weekdays">
        <div v-for="day in weekdays" :key="day" class="weekday">{{ day }}</div>
      </div>

      <div class="datepicker-days">
        <div
          v-for="(day, index) in daysInMonth"
          :key="index"
          :class="[
            'day',
            { selected: isSelected(day), today: isToday(day), 'other-month': day.other }
          ]"
          @click="selectDate(day)"
        >
          {{ day.date }}
        </div>
      </div>

      <div class="datepicker-footer">
        <button @click="selectToday" class="footer-btn">Today</button>
        <button @click="clearDate" class="footer-btn">Clear</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { onClickOutside } from '@vueuse/core';

const props = defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: 'Select date',
  },
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);
const selectedDate = ref(null);
const dropdownRef = ref(null);

const today = new Date();
const currentMonth = ref(today.getMonth());
const currentYear = ref(today.getFullYear());

const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];
const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const selectedMonth = computed({
  get: () => currentMonth.value,
  set: (val) => (currentMonth.value = val)
});

const selectedYear = computed({
  get: () => currentYear.value,
  set: (val) => (currentYear.value = val)
});

const yearRange = computed(() => {
  const years = [];
  const current = new Date().getFullYear();
  for (let i = current - 50; i <= current + 10; i++) {
    years.push(i);
  }
  return years;
});

const formattedDate = computed(() => {
  if (!selectedDate.value) return '';
  const d = new Date(selectedDate.value);
  return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
});

function toggleDatePicker() {
  showPicker.value = !showPicker.value;
}

function prevMonth() {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value -= 1;
  } else {
    currentMonth.value -= 1;
  }
}

function nextMonth() {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value += 1;
  } else {
    currentMonth.value += 1;
  }
}

const daysInMonth = computed(() => {
  const days = [];
  const year = currentYear.value;
  const month = currentMonth.value;

  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();

  // previous month padding
  const prevMonthDays = new Date(year, month, 0).getDate();
  for (let i = firstDay - 1; i >= 0; i--) {
    days.push({ date: prevMonthDays - i, other: true });
  }

  // current month
  for (let i = 1; i <= totalDays; i++) {
    days.push({ date: i, other: false });
  }

  // next month padding
  const remaining = 42 - days.length;
  for (let i = 1; i <= remaining; i++) {
    days.push({ date: i, other: true });
  }

  return days;
});

function selectDate(day) {
  if (day.other) return;
  
  // Create date at noon local time to avoid timezone issues
  const date = new Date(currentYear.value, currentMonth.value, day.date, 12);
  selectedDate.value = date.toISOString().split('T')[0];
  emit('update:modelValue', selectedDate.value);
  showPicker.value = false;
}

function isSelected(day) {
  if (!selectedDate.value || day.other) return false;
  const d = new Date(selectedDate.value);
  return (
    d.getDate() === day.date &&
    d.getMonth() === currentMonth.value &&
    d.getFullYear() === currentYear.value
  );
}

function isToday(day) {
  return (
    day.date === today.getDate() &&
    currentMonth.value === today.getMonth() &&
    currentYear.value === today.getFullYear() &&
    !day.other
  );
}

function selectToday() {
  selectedDate.value = today.toISOString().split('T')[0];
  currentMonth.value = today.getMonth();
  currentYear.value = today.getFullYear();
  emit('update:modelValue', selectedDate.value);
  showPicker.value = false;
}

function clearDate() {
  selectedDate.value = null;
  emit('update:modelValue', null);
  showPicker.value = false;
}

// Watch for external reset
watch(
  () => props.modelValue,
  (newVal) => {
    if (!newVal) {
      selectedDate.value = null;
    } else {
      selectedDate.value = newVal;
      const parsed = new Date(newVal);
      currentMonth.value = parsed.getMonth();
      currentYear.value = parsed.getFullYear();
    }
  },
  { immediate: true }
);

// Hide dropdown on outside click
onClickOutside(dropdownRef, () => {
  showPicker.value = false;
});
</script>

<style scoped>
.custom-datepicker {
  position: relative;
  display: inline-block;
  width: 100%;
}

.datepicker-input {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 1px solid #ccc;
  padding: 8px 12px;
  border-radius: 4px;
  background: white;
  cursor: pointer;
}

.input-text {
  flex-grow: 1;
}

.calendar-icon {
  margin-left: 10px;
  color: #777;
}

.datepicker-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 10;
  width: 224px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-top: 5px;
  padding: 10px;
}

.datepicker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.nav-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.month-select, .year-select {
  padding: 0px;
  font-size: 0.9rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.datepicker-weekdays, .datepicker-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  margin-bottom: 5px;
}

.weekday {
  font-weight: bold;
  font-size: 0.8rem;
}

.day {
  padding: 6px;
  cursor: pointer;
  border-radius: 3px;
}

.day:hover {
  background-color: #f0f0f0;
}

.day.selected {
  background-color: #45A049;
  color: #fff;
}

.day.today {
  border: 1px solid #45A049;
}

.day.other-month {
  color: #bbb;
}

.datepicker-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.footer-btn {
  background: white;
  border: 1px solid #ccc;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.footer-btn:hover {
  background: #f1f1f1;
}
</style>
