<template>
  <div class="reservations-tool">
    <div class="filters">
      <div class="filter-category">
        <label>Категория объекта</label>
        <div class="checkboxes-wrapper">
          <div v-for="category in categories" :key="category.id" class="checkbox-wrapper">
            <input
              type="checkbox"
              :id="`category-${category.id}`"
              v-model="filters.categories"
              :value="category.id"
              class="styled-checkbox"
            />
            <label :for="`category-${category.id}`">{{ category.name }}</label>
          </div>
        </div>
      </div>
      <div class="filter-date">
        <label for="date">Дата заказа</label>
        <input type="date" id="date" v-model="filters.date" class="styled-input" />
      </div>
    </div>
    <div class="reservations-table">
      <div class="table-container">
        <Loader :show="loading" />
        <table class="styled-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Название</th>
              <th>Свободные слоты</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="reservation in sortedReservations" :key="reservation.id">
              <td>{{ reservation.id }}</td>
              <td>
                <a
                  :href="`/nova/resources/orderable-service-object-admin-resources/${reservation.id}`"
                  target="_blank"
                  >{{ reservation.name }}</a
                >
              </td>
              <td v-html="formatSlots(reservation.merged_free_time_slots)"></td>
              <td class="actions">
                <a
                  :href="`/nova/resources/order-admin-resources/new?viaResource=orderable-service-object-admin-resources&viaResourceId=${reservation.id}`"
                  class="btn"
                  target="_blank"
                  >Забронировать</a
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import "moment/locale/ru";
import "moment-timezone";
import Loader from "./Loader.vue"; // Подключение компонента Loader

export default {
  components: {
    Loader,
  },
  data() {
    return {
      filters: {
        categories: [],
        date: "",
      },
      categories: [],
      reservations: [],
      loading: false,
      refreshInterval: null, // Handle for the refresh interval
      abortController: null,
    };
  },
  mounted() {
    moment.locale("ru"); // Set localization for Moment.js
    this.loadData();
    this.autoRefresh();
  },
  beforeDestroy() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval); // Clear interval when component is destroyed
    }
  },
  computed: {
    sortedReservations() {
      return this.reservations.slice().sort((a, b) => {
        const aNextSlot = this.getNextSlot(a.merged_free_time_slots);
        const bNextSlot = this.getNextSlot(b.merged_free_time_slots);
        return aNextSlot - bNextSlot;
      });
    },
  },
  methods: {
    loadData(force = false) {

      if(this.loading && !force){
        return;
      }

      this.loading = true;
      if (this.abortController) {
        this.abortController.abort(); // Отмена предыдущего запроса
      }
      this.abortController = new AbortController();

      Promise.all([this.getCategories(), this.getReservations()])
        .then(() => {
          this.loading = false;
        })
        .catch((error) => {
          if (error.code !== "ERR_CANCELED") {
            // Если ошибка не связана с отменой, обработайте ее как обычно
            console.error(error);
          }
        });
    },
    getCategories() {
      return Nova.request()
        .get("/nova-vendor/reservations-tool/categories", {
          signal: this.abortController.signal,
        })
        .then((response) => {
          this.categories = response.data;
        });
    },
    getReservations() {
      return Nova.request()
        .get("/nova-vendor/reservations-tool/reservations", {
          signal: this.abortController.signal,
          params: {
            categories: this.filters.categories,
            date: this.filters.date,
          },
        })
        .then((response) => {
          this.reservations = response.data;
        });
    },

    getNextSlot(slots) {
      const now = moment().tz("Asia/Almaty");
      if (!slots || slots.length === 0) {
        return Number.MAX_VALUE;
      }
      let nextSlotTime = Number.MAX_VALUE;
      slots.forEach((slot) => {
        const startTime = moment(slot.start);
        if (startTime.isAfter(now) && startTime.valueOf() < nextSlotTime) {
          nextSlotTime = startTime.valueOf();
        }
      });
      return nextSlotTime;
    },
    formatSlots(slots) {
      if (!slots || slots.length === 0) {
        return "Нет свободных слотов";
      }
      return slots
        .map((slot) => {
          let startTime = moment(slot.start);
          let endTime = moment(slot.end);

          const formattedStart = startTime.format("dd, D MMMM HH:mm");
          const formattedEnd = startTime.isSame(endTime, "day")
            ? endTime.format("HH:mm")
            : endTime.format("dd, D MMMM HH:mm");

          return `<div class="badge" style="background-color: #f6f6f6; border: 1px solid #64748b73;  margin: 2px 5px;   display: inline-block; max-width:max-content; color: #64748B; border: 1px solid #c2c6cc; padding: 5px 10px; border-radius: 5px;">${formattedStart} - ${formattedEnd}</div>`;
        })
        .join("");
    },
    autoRefresh() {
      this.refreshInterval = setInterval(() => {
        const currentPath = window.location.pathname; // Directly access the browser's path
        if (currentPath.includes("/nova/reservations-tool")) {
          this.loadData(false);
        } else {
          clearInterval(this.refreshInterval);
        }
      }, 30000);
    },
  },
  watch: {
    filters: {
      handler() {
        this.loadData(true);
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.reservations-tool {
  padding: 20px;
  background-color: #f4f4f9;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filters label{
    font-size: 15px;
}
.filters {
  align-items: flex-start;
  display: flex;
  justify-content: normal;
  gap: 30px;
  margin-bottom: 20px;
}

.filter-date {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.filter-category {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
}
.checkboxes-wrapper {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.styled-checkbox {
  margin-right: 10px;
  transform: scale(1.2);
}
th:last-child{
text-align: center;
}
td.actions{
    text-align: right;
}

.styled-input {
  padding: 5px;
  border-radius: 4px;
  border: 1px solid #ddd;
  max-width: 200px;
}

.reservations-table a {
  color: #0ea5e9;
  font-weight: 700;
  white-space: nowrap;
}
.reservations-table {
  position: relative;
  width: 100%;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-container {
  position: relative;
  min-height: 150px; /* ensure the container has some height */
}

.styled-table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
}

.styled-table thead {
  background-color: #f8f9fa;
  text-align: left;
  font-weight: 400;
  border-bottom: 1px solid #dddddd;
}

.styled-table th,
.styled-table td {
  padding: 12px 15px;
  position: relative;
  z-index: 2;
  font-weight: 500;
}

.styled-table tbody tr {
  border-bottom: 1px solid #ddd;
}

.styled-table tbody tr:nth-of-type(even) {
  background-color: #f8f9fa;
}

.styled-table tbody tr:hover {
  background-color: #eef1f4;
  cursor: pointer;
  transition: background-color 0.3s;
}

.actions .btn {
  background-color: #0ea5e9;
  color: white;
  padding: 5px 10px;
  text-decoration: none;
  border-radius: 4px;
  font-weight: 500;
  display: inline-block;
  transition: background-color 0.3s;
}

.actions .btn:hover {
  background-color: #0287c4;
}


</style>
