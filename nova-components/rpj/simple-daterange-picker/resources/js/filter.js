import Filter from './components/Filter';
import VueDatePicker from '@vuepic/vue-datepicker';

Nova.booting((app, store) => {
  app.component('daterangepicker', Filter);
  app.component('VueDatePicker', VueDatePicker);
});
