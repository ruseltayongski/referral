@extends('layouts.app')

@section('css')
    <!-- fullCalendar 2.2.5-->
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.print.css') }}" media="print">

    <style>
        :root {
           /* --red: #ef233c;
            --darkred: #c00424;
            --platinum: #e5e5e5;
            --black: #2b2d42;
            --blue: #3379A1;*/
            --green: #59AB91;
            --white: #fff;
            --thumb: #edf2f4;
        }
        .scroll-container {
            display: flex;
            list-style: none;
            overflow-x: scroll;
            scroll-snap-type: x mandatory;
        }
        .scroll-container::-webkit-scrollbar {
            height: 12px;
        }
        .scroll-container::-webkit-scrollbar-thumb,
        .scroll-container::-webkit-scrollbar-track {
            border-radius: 92px;
        }
        .scroll-container::-webkit-scrollbar-thumb {
            background: var(--green);
        }
        .scroll-container::-webkit-scrollbar-track {
            background: var(--thumb);
        }
        .scroll-item {
            display: flex;
            flex-direction: column;
            flex: 0 0 100%;
            background: var(--white);
            border-radius: 4px;
            scroll-snap-align: start;
            transition: all 0.2s;
        }
        .scroll-item .widget-user {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 15%);
        }
        .scroll-item:not(:last-child) {
            margin-right: 10px;
        }
        .scroll-item .widget-user-header {
            background: var(--green);
            color: white;
        }
        .widget-user-header .widget-user-desc {
            max-width: 65%;
            font-size: 14px;
        }
        .widget-user-header .widget-user-username {
            font-size: 21px;
        }
        .widget-user .widget-user-image {
            position: absolute;
            top: 65px;
            left: 75%;
            margin-left: -45px;
        }
        .with-badge {
            position: relative;
            display: inline-block;
        }
        .with-badge::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50px;
            height: 50px;
            background: linear-gradient(to bottom left, transparent 30%, #ef233c 4%, #ef233c 50.5%, transparent 50.5%);
            transform-origin: bottom right;
            z-index: 1;
        }
        .with-badge::after {
            content: 'Government';
            position: absolute;
            top: 9%;
            right: 5%;
            transform: translate(50%, -50%) rotate(45deg);
            color: white;
            font-size: 8px;
            font-weight: bold;
            z-index: 2;
        }

        .widget-user-image {
            overflow: hidden;
        }
        .widget-user-image img {
            transition: transform .3s ease-in-out;
        }
        .scroll-item:hover .widget-user-image img {
            /*transform: scale(.9);*/
            width: 100px;
        }
        .available-slot-event {
            cursor: pointer;
        }
        /*===============================================================*/



        /*===============================================================*/
        @media (min-width: 500px) {
            .scroll-item {
                flex-basis: calc(50% - 10px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 20px;
            }
        }
        @media (min-width: 700px) {
            .scroll-item {
                flex-basis: calc(calc(100% / 3) - 20px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 30px;
            }
        }
        @media (min-width: 1100px) {
            .scroll-item {
                flex-basis: calc(25% - 30px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 1px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">Appointment Calendar</h3>

            <div class="scroll-container">
                @if(isset($appointment_sched))
                    @foreach($appointment_sched as $row)
                        <div class="col-md-4 scroll-item">
                            <!-- Widget: user widget style 1 -->
                            <div class="box box-widget widget-user with-badge">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header">
                                    <h3 class="widget-user-username">
                                        <?php
                                        $facility_name = \App\Facility::find($row->facility_id)->name;
                                        echo $facility_name;
                                        ?></h3>
                                    <h5 class="widget-user-desc">
                                        <?php
                                        $address = \App\Facility::find($row->facility_id)->address;
                                        echo $address;
                                        ?>
                                    </h5>
                                </div>
                                <div class="widget-user-image">
                                    <img src="<?php echo e(asset('resources/img/video/doh-logo.png')); ?>" class="img-circle" alt="User Avatar"/>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                {{--<h5 class="description-header">3,200</h5>--}}
                                                <h5 class="description-header"><?php
                                                    $slot = \App\AppointmentSchedule::where('facility_id', $row->facility_id)->get();
                                                    if($slot){
                                                        $count =0;
                                                        foreach ($slot as $ind){
                                                            $count = $count + $ind->slot;
                                                        }
                                                    }
                                                    echo $count;?></h5>
                                                <span class="description-text">Slot</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">13,000</h5>
                                                <span class="description-text">Available</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                            <button class="btn btn-block btn-success btn-select" onclick="calendar_display({{$row->facility_id}})" id="selected_data" name="selected_data"> Select</button>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                </div>
                            </div><!-- /.widget-user -->
                        </div><!-- /.col -->
                    @endforeach
                @endif
            </div>


            {{--------------------------------------------------------}}
            <div class="calendar-container">
                <div class="row">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="box box-primary">
                                    <div class="box-body no-padding">

                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                    </div><!-- /.box-body -->


                                </div><!-- /. box -->
                            </div><!-- /.col -->
                            <div class="col-md-3">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Clickable Event</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                        <div class="external-event bg-green">Available Slot</div>
                                        {{--<div class="external-event bg-yellow">Go home</div>
                                        <div class="external-event bg-aqua">Do homework</div>
                                        <div class="external-event bg-light-blue">Work on UI design</div>
                                        <div class="external-event bg-red">Sleep tight</div>--}}
                                        {{--<div class="checkbox">--}}
                                            {{--<label for="drop-remove">--}}
                                            {{--<input type="checkbox" id="drop-remove">--}}
                                            {{--remove after drop--}}
                                            {{--</label>--}}
                                        {{--</div>--}}
                                        </div>
                                    </div><!-- /.box-body -->
                                </div><!-- /. box -->


                                {{--<div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Create Event</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                        <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                                        <ul class="fc-color-picker" id="color-chooser">
                                            <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                                        </ul>
                                        </div><!-- /btn-group -->
                                        <div class="input-group">
                                        <input id="new-event" type="text" class="form-control" placeholder="Event Title">
                                        <div class="input-group-btn">
                                            <button id="add-new-event" type="button" class="btn btn-primary btn-flat">Add</button>
                                        </div><!-- /btn-group -->
                                        </div><!-- /input-group -->
                                    </div>
                                </div>--}}


                                <!-- Radio Button List -->
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Select Time of Appointment</h3>
                                    </div>
                                    <div class="box-body">
                                        <div id="appointment-time-list">
                                           {{-- <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Time 1
                                                </label>
                                            </div>


                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    Time 2
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault3">
                                                <label class="form-check-label" for="flexRadioDefault3">
                                                    Time 3
                                                </label>
                                            </div>--}}
                                        </div>
                                    </div>
                                </div>



                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </section><!-- /.content -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('resources/plugin/fullcalendar/fullcalendar.min.js') }}"></script>
    <script>


        /*-----------------------------------------------------------------*/
        function calendar_display(appointmentId) {
            $('#calendar').val(appointmentId);

            var url = "{{ route('get-Facility-Details', ':id') }}";
            url = url.replace(':id', appointmentId);
            var appointedDates = [];

            $.get(url, function (data) {
                console.log('my data', data);

                /*for (var i = 0; i < data.length; i++) {
                    var date = data[i].appointed_date;
                    if (!appointedDates.includes(date)) {
                        appointedDates.push(date);
                    }
                }*/

                for (var i = 0; i < data.facility_data.length; i++) {
                    var date = data.facility_data[i].appointed_date;
                    var time = data.facility_data[i].appointed_time;

                    // Combine date and time to create a unique identifier
                    var dateTimeIdentifier = date + ' ' + time;

                    if (!appointedDates.includes(dateTimeIdentifier)) {
                        appointedDates.push(dateTimeIdentifier);
                    }
                }

                console.log('checking', appointedDates);
                doSomethingWithAppointedDates();

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });

            var calendarEvents = [];

            function doSomethingWithAppointedDates() {
                console.log("Value of the first appointment globally:", appointedDates[0]);

                calendarEvents = [];

                appointedDates.forEach(function (date) {
                    var event = {
                        title: 'Available Slot', // You can customize the title if needed
                        start: new Date(date),
                        allDay: true, // Set to true to make it an all-day event
                        backgroundColor: "#00a65a", // Success (green)
                        borderColor: "#00a65a", // Success (green)
                        className: 'available-slot-event' // Add the class here
                    };
                    calendarEvents.push(event);
                });
                refreshCalendar();
            }

            function refreshCalendar() {
                if ($('#calendar').fullCalendar('getView')) {
                    $('#calendar').fullCalendar('destroy');
                }

                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {
                        today: 'today',
                        month: 'month',
                        week: 'week',
                        day: 'day'
                    },
                    events: calendarEvents,
                    editable: false,
                    droppable: false,
                    eventClick: function (calEvent, jsEvent, view) {
                        // Handle the click event here
                        alert('You clicked on ' + calEvent.title);
                    },
                    drop: function (date, allDay) {
                        var originalEventObject = $(this).data('eventObject');

                        if (originalEventObject.title !== 'Available Slot') {
                            var copiedEventObject = $.extend({}, originalEventObject);

                            copiedEventObject.start = date;
                            copiedEventObject.allDay = allDay;
                            copiedEventObject.backgroundColor = $(this).css("background-color");
                            copiedEventObject.borderColor = $(this).css("border-color");

                            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                            if ($('#drop-remove').is(':checked')) {
                                $(this).remove();
                            }
                        }
                    }
                });
            }
            refreshCalendar();
        }

        /*----------------------------------------------------------------*/
        document.addEventListener('DOMContentLoaded', function () {
            $('#calendar').fullCalendar({
                // Your existing fullCalendar configuration...

                dayClick: function (date, jsEvent, view) {
                    var selectedDate = date.format('YYYY-MM-DD');

                    // Make an AJAX request to fetch available time slots
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("get-available-time-slots") }}',
                        data: {
                            selected_date: selectedDate
                        },
                        success: function (response) {
                            // Update the radio button list with available time slots
                            updateAvailableTimeSlots(response.time_slots);
                        },
                        error: function (error) {
                            console.error('Error fetching available time slots:', error);
                        }
                    });
                }
            });

            function updateAvailableTimeSlots(timeSlots) {
                var timeListDiv = $('#appointment-time-list');

                // Clear existing content
                timeListDiv.empty();

                // Add radio buttons for each available time slot
                for (var i = 0; i < timeSlots.length; i++) {
                    var timeSlot = timeSlots[i];
                    var radioButton = $('<div class="form-check">' +
                        '<input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault' + (i + 1) + '" value="' + timeSlot + '">' +
                        '<label class="form-check-label" for="flexRadioDefault' + (i + 1) + '">' +
                        timeSlot +
                        '</label>' +
                        '</div>');

                    timeListDiv.append(radioButton);
                }

                // Call the function to update the appointment time list
                updateAppointmentTimeList(timeSlots);
            }
        });

        function updateAppointmentTimeList(timeSlots) {
            // Assuming you have an element with id 'appointment-time-list'
            var appointmentTimeList = $('#appointment-time-list');

            // Clear existing content
            appointmentTimeList.empty();

            // Add radio buttons for each available time slot
            for (var i = 0; i < timeSlots.length; i++) {
                var timeSlot = timeSlots[i];
                var radioButton = $('<div class="form-check">' +
                    '<input class="form-check-input" type="radio" name="appointmentTime" value="' + timeSlot + '">' +
                    '<label class="form-check-label" for="flexRadioDefault' + (i + 1) + '">' +
                    'Time: ' + timeSlot +
                    '</label>' +
                    '</div>');

                appointmentTimeList.append(radioButton);
            }
        }


        /*----------------------------------------------------------------*/
        $(document).ready(function() {
            // Apply default background when the page loads
            $(".scroll-item .widget-user-header").css({
                "background-color": "#59AB91",
                "color": "white"
            });

            $(".btn-select").on("click", function() {
                console.log("Button is clicked!..")
                // Remove the "selected" class from all buttons
                $(".scroll-item").removeClass("selected");
                $(".btn-select").text("Select");
                $(".scroll-item .widget-user-header").css({
                    "background-color": "#59AB91",
                    "color": "white"
                });

                // Add the "selected" class to the clicked button
                var $parentItem = $(this).closest(".scroll-item");
                $parentItem.addClass("selected");

                // Change the text of the button based on its current state
                var buttonText = $parentItem.hasClass("selected") ? "Selected" : "Select";
                $(this).text(buttonText);

                // Apply the background color to .widget-user-header if selected
                if ($parentItem.hasClass("selected")) {
                    $parentItem.find(".widget-user-header").css({
                        "background-color": "#008E4D",
                        "color": "white"
                    });
                }
            });
        });
        /*-----------------------------------------------------------------*/
    </script>
@endsection

