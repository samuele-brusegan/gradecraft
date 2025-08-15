<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($response)) { $response = ['error' => '']; }
if ( isset($response['error'])) { echo "<pre>"; print_r($response); echo "</pre>"; }

?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php include COMMON_HTML_HEAD; ?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
        <title>Gradecraft - Agenda</title>
    </head>
    <style>
        #calendar {
            --fc-page-bg-color: var(--background-color);
            & a {
                color: var(--text-color);
                text-decoration: none;
            }

            height: 90%;
            width: 100%;
        }
        .calendar-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }
    </style>
    <body>
        <div class="container">
            <?php
            session_wall();
    //        echo "<pre>"; print_r($response); echo "</pre>";
            ?>
            <div class="calendar-container">
                <div id='calendar'></div>
            </div>
        </div>
        <?php include COMMON_HTML_FOOT; ?>
    </body>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '2025-03-01',
                events: [
                    <?php
                    foreach ($response['agenda'] as $event) {
                        if (in_array($event['evtCode'], ['AGNT', 'AGCR'])) continue;
                        echo "{\n";
                        echo "    title:  '{$event['subjectDesc']}',\n";
                        echo "    start:  '{$event['evtDatetimeBegin']}',\n";
                        echo "    end:    '{$event['evtDatetimeEnd']}',\n";
                        echo "    author: '{$event['authorName']}',\n";
                        echo "    notes:  `".htmlspecialchars($event['notes'])."`,\n";
                        echo "    id:     '{$event['evtId']}',\n";
                        echo "    code:   '{$event['evtCode']}'\n";
                        echo "},";
                    }
                    ?>
                ],
                firstDay: 1,
                dayMaxEventsRows: true,
                views: {
                    dayGridMonth: {
                        dayMaxEvents: 4,
                    }
                },
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'timeGridDay,timeGridWeek,dayGridMonth,multiMonthYear'
                },
                businessHours: {
                    daysOfWeek: [ 1, 2, 3, 4, 5, 6 ],

                    startTime: '8:00',
                    endTime: '15:00',
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false,
                },
                hiddenDays: [ 0 ],
                themeSystem: 'bootstrap5',
                slotMinTime: '08:00',
                slotMaxTime: '15:00',
                allDaySlot: false,

                eventClick: function(info) {
                    alert("Id: "+info.event.id)



                    // info.el.style.borderColor = 'red';
                },
            });
            calendar.render();
        });

    </script>
</html>