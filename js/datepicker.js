var $pickers = [];

$('.datepicker').each(function($i) {
    $pickers[$i] = new Pikaday({
        field:$(this)[0],
        firstDay:1,
        i18n: {
            previousMonth : 'Previous Month',
            nextMonth     : 'Next Month',
            months        : ['January','February','March','April','May','June','July','August','September','October','November','December'],
            weekdays      : ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            weekdaysShort : ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
            midnight      : '12 AM',
            noon          : '12 PM'
        },
        timeLabel: 'Time: ',
        theme:'dark-theme',
        format:'YYYY-MM-DD',
        showTime: false
    });
});