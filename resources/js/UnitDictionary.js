// shared/UnitDictionary.js

export const UnitDictionary = {
    PORTION: { value: 'Portion', label: 'порц' },
    PIECE: { value: 'Piece', label: 'шт' },
    TABAC: { value: 'Tabac', label: 'табак' },
    DISH: { value: 'Dish', label: 'блюдо' },
    LITER: { value: 'Liter', label: 'л' },
    MILLILITER: { value: 'Milliliter', label: 'мл' },
    CENTILITER: { value: 'Centiliter', label: 'cl' },
    KILOGRAM: { value: 'Kilogram', label: 'кг' },
    GRAM: { value: 'Gram', label: 'гр' },
    METER: { value: 'Meter', label: 'м' },
    BOTTLE: { value: 'Bottle', label: 'бут' },
    PACK: { value: 'Pack', label: 'уп' },
    PACKET: { value: 'Packet', label: 'пач' },
};

// Функция для получения метки по значению
export function getUnitLabel(value) {
    const unit = Object.values(UnitDictionary).find(unit => unit.value === value);
    return unit ? unit.label : null;
}

export function getUnitValue(label) {
    const unit = Object.values(UnitDictionary).find(unit => unit.label === label);
    return unit ? unit.value : null;
    
}

// Функция для получения всех меток
export function getAllUnitLabels() {
    return Object.fromEntries(
        Object.values(UnitDictionary).map(unit => [unit.value, unit.label])
    );
}
