import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-datetime-wo-timezone', IndexField)
  app.component('detail-datetime-wo-timezone', DetailField)
  app.component('form-datetime-wo-timezone', FormField)
})
