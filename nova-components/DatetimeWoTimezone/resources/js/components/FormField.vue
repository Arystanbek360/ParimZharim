<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <div class="flex items-center">
                <input
                    type="datetime-local"
                    class="form-control form-input form-control-bordered"
                    ref="dateTimePicker"
                    :id="currentField.uniqueKey"
                    :dusk="field.attribute"
                    :name="field.name"
                    :value="formattedDate"
                    :class="errorClasses"
                    :disabled="currentlyIsReadonly"
                    @change="handleChange"
                    :min="currentField.min"
                    :max="currentField.max"
                    :step="currentField.step"
                />

            </div>
        </template>
    </DefaultField>
</template>

<script>
import isNil from 'lodash/isNil'
import { DateTime } from 'luxon'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'
import filled from '@/util/filled'

export default {
    mixins: [HandlesValidationErrors, DependentFormField],

    data: () => ({
        formattedDate: '',
    }),

    methods: {
        /*
         * Set the initial value for the field
         */
        setInitialValue() {
            if (!isNil(this.currentField.value)) {
                let isoDate = DateTime.fromISO(this.currentField.value || this.value, {
                    zone: ('UTC'),
                })

                this.value = isoDate.toString()

                isoDate = isoDate.setZone(this.timezone)

                this.formattedDate = [
                    isoDate.toISODate(),
                    isoDate.toFormat(this.timeFormat),
                ].join('T')
            }
        },

        /**
         * On save, populate our form data
         */
        fill(formData) {
            this.fillIfVisible(formData, this.fieldAttribute, this.value || '')

            if (this.currentlyIsVisible && filled(this.value)) {
                let isoDate = DateTime.fromISO(this.value, { zone: this.timezone })

                this.formattedDate = [
                    isoDate.toISODate(),
                    isoDate.toFormat(this.timeFormat),
                ].join('T')
            }
        },

        /**
         * Update the field's internal value
         */
        handleChange(event) {
            let value = event?.target?.value ?? event;

            if (filled(value)) {
                let isoDate = DateTime.fromISO(value, { zone: this.timezone });

                // Если шаг равен 3600 (один час), обнуляем минуты и секунды
                if (this.currentField.step === 3600) {
                    isoDate = isoDate.set({ minute: 0, second: 0 });
                }

                // Если шаг равен 1800 (полчаса), округляем минуты до ближайшего получаса вниз
                if (this.currentField.step === 1800) {
                    let currentMinute = isoDate.get('minute');
                    let roundedMinute = Math.floor(currentMinute / 30) * 30;
                    isoDate = isoDate.set({ minute: roundedMinute, second: 0 });
                }

                this.value = isoDate.setZone('UTC').toString();
                this.formattedDate = [
                    isoDate.toISODate(),
                    isoDate.toFormat(this.timeFormat),
                ].join('T');

                // Обновляем DOM с новым значением
                this.$nextTick(() => {
                    this.$refs.dateTimePicker.value = this.formattedDate;
                });
            } else {
                this.value = this.fieldDefaultValue();
            }

            if (this.field) {
                this.emitFieldValueChange(this.fieldAttribute, this.value);
            }
        },
    },

    computed: {
        timeFormat() {
            return this.currentField.step % 60 === 0 ? 'HH:mm' : 'HH:mm:ss'
        },

        timezone() {
            return ('UTC')
        },
    },
}
</script>
