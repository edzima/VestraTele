
moment.locale('pl');

function refreshCalendar() {
    $('#calendar').fullCalendar('refetchEvents');
}

//refresh event at 20 min
setInterval(refreshCalendar, 1000*60*20);

//in cookie store showAlerts
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


//set event Change checkAlerts to save in Cookie
var showAlerts;
//get coookie data and set in checkBox
showAlerts= Boolean(parseInt(getCookie('showAlerts')));



$toggle = $('#toggle');
$toggle.change(function(){
    showAlerts= $(this).prop("checked");
    if(showAlerts) document.cookie= "showAlerts=1";
    else document.cookie= "showAlerts=0";

    console.log(showAlerts);
    refreshCalendar();
});


if(!showAlerts) $toggle.bootstrapToggle('off');


console.log(showAlerts);


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
            //only user check showAlerts
            if(showAlerts){
                var alerts =[];
                swal.setDefaults({
                    //input: 'text',
                    confirmButtonText: 'Otwórz w nowej karcie',
                    type: 'info',
                    showCancelButton: false,
                    animation: false,
                    preConfirm: function () {
                        return new Promise(function (resolve) {
                            setTimeout(function() {
                                console.log(alerts[swal.getQueueStep()]);
                                window.open(alerts[swal.getQueueStep()].url,'_blank');
                                resolve()
                            }, 20)
                        })
                    },

                    //progressSteps: ['1', '2', '3']
                });

                var step = [];
                var progressSteps = [];
                for (var k in data) {
                    if(data[k].isExpired){
                        alerts.push(data[k]);
                        var start = moment(data[k].start).format("MMM Do");
                        progressSteps.push(start);
                        step.push({
                            'title':data[k].title,
                            'text' :data[k].description,
                        });
                    }
                }
                swal.setDefaults({
                    progressSteps: progressSteps
                });

                swal.queue(step).then('',function(){
                    console.log('close');
                })
            }

            },


        },
        {
            url: 'layer-news'
        }

    ],

    select: function(start, end, allDay) {
        var allDay = !start.hasTime() && !end.hasTime();
        // console.log(end);
        if (!allDay) {

            var causeDate = moment(start).format('YYYY-MM-DD HH:mm');

            $('#cause-date').val(start.format());

            $('#modal').modal({keyboard: false, backdrop: 'static'})
                .find('#modalContent')
                .load('create-ajax', function (status, start) {
                    // console.log(causeDate);
                    $('#cause-date').val(causeDate);

                });
            //dynamiclly set the header for the modal
            document.getElementById('modalHeader').innerHTML = '<h4> Dodanie sprawy</h4>';
        }

    },
    eventClick: function(event) {
        if (event.url) {
            window.open(event.url);
            return false;
        }
    },
    eventRender: function(event, element) {

        element.bind('dblclick', function() {
            if(confirm("blaba")){
                $.post('/calendar/remove', {
                        "event_id": event.id
                    },
                    function(data) {
                        console.log(data);
                        $('#calendar').fullCalendar("removeEvents", (data));
                        $('#calendar').fullCalendar("rerenderEvents");

                    });
            }
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


