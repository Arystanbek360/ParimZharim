<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>
    <template #filter>
      <VueDatePicker
        ref="datepicker"
        v-model="date"
        locale="ru"
        format="dd.MM.yyyy"
        range
        multi-calendars
        @update:model-value="handleChange"
        @internal-model-change="handleInternal"
        @cleared="handleCleared"
        :enable-time-picker="false"
        :teleport="true"
        select-text="Применить"
        class="dp__component component__daterange-picker"
      >
        <template #left-sidebar>
          <ul>
            <li
              v-for="preset in presets"
              :key="preset.key"
              :class="{ active: activePreset === preset.key }"
              @click="selectPreset(preset.key)"
            >
              {{ preset.label }}
            </li>
          </ul>
        </template>
      </VueDatePicker>
    </template>
  </FilterContainer>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import dayjs from "dayjs";

export default {
  emits: ["change"],
  props: {
    resourceName: { type: String, required: true },
    filterKey: { type: String, required: true },
    lens: String,
  },
  data() {
    return {
      date: null,
      activePreset: null,
      presets: [
        { key: "today", label: "Сегодня" },
        { key: "yesterday", label: "Вчера" },
        { key: "last7Days", label: "Последние 7 дней" },
        { key: "thisMonth", label: "Текущий месяц" },
        { key: "lastMonth", label: "Последний месяц" },
        { key: "last6Months", label: "Последние 6 месяцев" },
        { key: "thisYear", label: "Этот год" },
        { key: "range", label: "Выбрать диапазон" },
      ],
    };
  },
  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey);
    },
  },
  watch: {
    'filter.currentValue'(newVal) {
      if (!newVal) {
        this.date = null;
        this.activePreset = null;
      } else {
        const [start, end] = newVal.split(' - ');
        if (start && end) {
          this.date = [new Date(start), new Date(end)];
        }
      }
    },
  },
  mounted() {
    if (this.filter.currentValue) {
      const [start, end] = this.filter.currentValue.split(' - ');
      if (start && end) {
        this.date = [new Date(start), new Date(end)];
      }
    }
  },
  methods: {
    handleChange(date) {
      if (date && date.length === 2) {
        const [startDate, endDate] = date;
        this.$store.commit(`${this.resourceName}/updateFilterState`, {
          filterClass: this.filterKey,
          value: `${dayjs(startDate).format('YYYY-MM-DD')} - ${dayjs(endDate || startDate).format('YYYY-MM-DD')}`,
        });
      } else {
        // Обновляем фильтр при очистке дат
        this.$store.commit(`${this.resourceName}/updateFilterState`, {
          filterClass: this.filterKey,
          value: null,
        });
        this.activePreset = null;
      }
      this.$emit("change");
    },
    handleCleared() {
      this.date = null;
      this.activePreset = null;
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: null,
      });
      this.$emit("change");
    },
    handleInternal(date) {
      if (date && date.length === 2) {
        const matchedPreset = this.presets.find((preset) => {
          if (preset.key === 'range') return false;
          const { startDate, endDate } = this.getPresetDates(preset.key);
          return (
            dayjs(date[0]).isSame(startDate, 'day') &&
            dayjs(date[1]).isSame(endDate, 'day')
          );
        });
        this.activePreset = matchedPreset ? matchedPreset.key : 'range';
      } else {
        this.activePreset = null;
      }
    },
    selectPreset(presetKey) {
      if (presetKey === 'range') {
        this.date = null;
        this.$nextTick(() => this.$refs.datepicker.openMenu());
        return;
      }
      const { startDate, endDate } = this.getPresetDates(presetKey);
      this.date = [startDate.toDate(), endDate.toDate()];
      this.handleChange(this.date);
      this.handleInternal(this.date);
      this.scrollToSelectedDate(startDate);
    },
    getPresetDates(presetKey) {
      const now = dayjs();
      let startDate, endDate;
      switch (presetKey) {
        case 'today':
          startDate = now.startOf('day');
          endDate = now.endOf('day');
          break;
        case 'yesterday':
          startDate = now.subtract(1, 'day').startOf('day');
          endDate = now.subtract(1, 'day').endOf('day');
          break;
        case 'last7Days':
          startDate = now.subtract(6, 'day').startOf('day');
          endDate = now.endOf('day');
          break;
        case 'thisMonth':
          startDate = now.startOf('month');
          endDate = now.endOf('month');
          break;
        case 'lastMonth':
          startDate = now.subtract(1, 'month').startOf('month');
          endDate = now.subtract(1, 'month').endOf('month');
          break;
        case 'last6Months':
          startDate = now.subtract(5, 'month').startOf('month');
          endDate = now.endOf('month');
          break;
        case 'thisYear':
          startDate = now.startOf('year');
          endDate = now.endOf('year');
          break;
        default:
          startDate = now.startOf('day');
          endDate = now.endOf('day');
      }
      return { startDate, endDate };
    },
    scrollToSelectedDate(date) {
      this.$refs.datepicker.setMonthYear({
        month: date.month(),
        year: date.year(),
      });
    },
  },
};
</script>
