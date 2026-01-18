document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav_item');
    const elementDecor = document.querySelector('.element_decor');

    function toggleDecor() {
        if (document.querySelector('.nav_item.nav_item_main.active')) {
            elementDecor.classList.add('show');
        } else {
            elementDecor.classList.remove('show');
        }
    }
    function handleClick(item) {
        item.classList.add('active');
        navItems.forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.classList.remove('active');
            }
        });
        toggleDecor();
    }
    toggleDecor();  
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            handleClick(item);
        });
    });
});



 
// const resetButton = document.querySelector('.param_reset');
// const timeInput_custom = document.querySelector('.time-input');
// let isResetting = false;



// const datepicker =  new AirDatepicker('#input_datepicker', {
//  classes: 'wide-datepicker',
//  isMobile: true,
//  autoClose: false,
     
//     onSelect: () => {
//         if (resetButton && !isResetting) {
//             resetButton.classList.add('show');
//         }
//     }
// });

// resetButton.addEventListener('click', () => {
//     isResetting = true; 
//     timeInput_custom.value = '';
//     datepicker.clear();
//     // timepicker.clear();
//     setTimeout(() => {
//         resetButton.classList.remove("show");
//         isResetting = false;  
//     }, 10); // Достаточно короткая задержка для завершения всех внутренних обновлений
// });
 

const resetButton = document.querySelector('.param_reset');
const timeInput_custom = document.querySelector('.time-input');
let isResetting = false;

const datepicker = new AirDatepicker('#input_datepicker', {
    classes: 'wide-datepicker',
    isMobile: true,
    autoClose: false, // Оставляем календарь открытым после выбора даты
    buttons: [ // Добавляем кнопки в интерфейс datepicker
        {
            content: dp => dp.opts.isMobile ? 'Подтвердить' : 'Подтвердить выбор',
            tagName: 'button',
            className: 'confirm-date-btn',
            onClick: dp => {
                dp.hide(); // Скрываем календарь
                resetButton.classList.add('show'); // Показываем кнопку сброса если нужно
            },
            attrs: {
                type: 'button'
            }
        }
    ],
    onSelect: ({date, formattedDate, datepicker}) => {
        if (resetButton && !isResetting) {
            resetButton.classList.add('show');
        }
    }
});

resetButton.addEventListener('click', () => {
    isResetting = true;
    timeInput_custom.value = '';
    datepicker.clear();
    setTimeout(() => {
        resetButton.classList.remove("show");
        isResetting = false;
    }, 10); // Короткая задержка для внутренних обновлений
});