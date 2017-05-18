
function refreshCalendar() {
    console.log('refresh');
    $('#calendar').fullCalendar('refetchEvents');
}


setInterval(refreshCalendar, 1000*60);

var alerts =[];


$('#calendar').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    defaultView: 'agendaWeek',
    views: {
        month: {
            aspectRatio: 0.5,
        },
        agendaWeek: {
            height: 'auto',
        }
    },
    minTime: '6:00:00',
    maxTime: '23:00:00',
    defaultTimedEventDuration: '0:30:00',
    allDayText: 'Zalecenia',
    //  defaultDate: '2017-02-15',
    selectable: true,
    locale: "pl",
    lang: "pl",
    //selectHelper: true,
    editable: false,
    eventLimit: true,

    eventSources: [{
        //url: 'layer-events',
        url: 'layer-events',

        success: function(data) {

            swal.setDefaults({
                //input: 'text',
                confirmButtonText: 'Otwórz',
                type: 'warning',
                showCancelButton: true,
                animation: false,
                preConfirm: function (html) {
                    return new Promise(function (resolve, reject) {
                        setTimeout(function() {
                            console.log(alerts[swal.getQueueStep()]);
                            window.open(alerts[swal.getQueueStep()].url,'_blank');
                            resolve()
                        }, 20)
                    })
                },

                //progressSteps: ['1', '2', '3']
            })
            var eventId = [];
            var title =[];
            var step = [];

            for (var k in data) {

                if(data[k].isExpired){
                    alerts.push(data[k]);
                    step.push({
                        'title':data[k].title,
                        'text' :data[k].description,

                    });
                    eventId.push(data[k].id);
                    title.push(data[k].title);
                    //step.push(data[k].description);
                    //alerts.push(data[k]);
                }
            }

            //alerts.push(eventId,title,step);
            console.log(step);
            swal.queue(step)

        },

    },

    ],

    select: function(start, end, allDay) {

        var causeDate = moment(start).format('YYYY-MM-DD HH:mm');

        $('#cause-date').val(start.format());

        $('#modal').modal({keyboard: false, backdrop : 'static'})
            .find('#modalContent')
            .load('create-ajax', function(  status, start ) {
               // console.log(causeDate);
                $('#cause-date').val(causeDate);

        });
        //dynamiclly set the header for the modal
        document.getElementById('modalHeader').innerHTML = '<h4> Dodanie sprawy</h4>';

    },
    eventClick: function(event) {

        if (event.url) {
            window.open(event.url);
            return false;
        }
    },
    eventRender: function(event, element) {
        element.popover({
            title: event.title,
            placement: 'bottom',
            trigger: 'hover',
            content: event.description,
            // container: '#calendar',
        });

        //element.find('div.fc-title').html(element.find('div.fc-title').text())	;
    },


});


