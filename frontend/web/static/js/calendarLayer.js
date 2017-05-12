
function refreshCalendar() {
    console.log('refresh');
    $('#calendar').fullCalendar('refetchEvents');
}


var steps = [
    {
        title: 'Question 1',
        text: 'Chaining swal2 modals is easy'
    },
    'Question 2',
    'Question 3'
];
//swal.queue(steps);

//setInterval(refreshCalendar, 3000);


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
    editable: true,
    eventLimit: true,

    eventSources: [{
        //url: 'layer-events',
        url: 'layer-events',
        /*
        success: function(data) {
            var alerts = [];
            var eventId = [];
            var title =[];
            var step = [];

            for (var k in data) {

                if(data[k].isExpired){
                    alert(data[k].title);
                    console.log(data[k].id);
                    eventId.push(data[k].id);
                    title.push(data[k].title);
                    step.push(data[k].description);
                    //alerts.push(data[k]);
                }
            }
            alerts.push(eventId,title,step);
            console.log(alerts);

        },
        */
    },

    ],


    eventAfterAllRender: function(){
        console.log($('#calendar').fullCalendar('getEventSources'));

            console.log('allrender');

    },

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
    eventDrop: function(event, delta, revertFunc) {
        console.log(event.allDay);
        if (event.isNews && event.allDay) {
            $.get('updatenews', {
                    "id": event.id,
                    "start": event.start.format(),
                    "end": event.start.format()
                },
                function(data) {});
        } else if (!event.isNews && !event.allDay) {
            $.get('update', {
                    "id": event.id,
                    "start": event.start.format()
                },
                function(data) {});
        } else {
            swal(
                'Niedozwolone!',
                'Nie można mieszać różnego rodzaju zdarzeń',
                'error'
            );
            revertFunc();
            $('#calendar').fullCalendar('undrop');
        }
    },
    eventRender: function(event, element) {

        element.bind('dblclick', function() {
            swal({
                    title: "Jesteś pewien, że chcesz to usunąć?",
                    text: "Informacja: " + event.title,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Tak",
                    cancelButtonText: "Nie",
                    closeOnConfirm: false
                },
                function() {
                    var event_id = event.id;
                    swal("Usunięto!", "Twoja notatka została usunięta", "success");
                    $.post('remove', {
                            "event_id": event_id
                        },
                        function(data) {
                            console.log(data);
                            $('#calendar').fullCalendar("removeEvents", (data));
                            $('#calendar').fullCalendar("rerenderEvents");

                        });
                });
        });
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
