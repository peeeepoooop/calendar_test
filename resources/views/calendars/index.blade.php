@extends('layouts.master')

@section('content')
    <div class="btn btn-success message-success d-none" style="position: absolute; top: 0; right: 0; z-index: 1050"></div>
    <div class="btn btn-danger message-error d-none" style="position: absolute; top: 0; right: 0; z-index: 1050"></div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Calendar</h5>
                <hr>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row p-1">
                            <label>Event</label>
                            <input name="event_name" type="text" class="form-control event_name" required>
                        </div>
                        <div class="row p-1">
                            <div class="col-md-6">
                                <div class="row">
                                    <label>From</label>
                                </div>
                                <div class="row">
                                    <input name="start_date" id="start_date" type="date" class="form-control datepicker" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label>To</label>
                                </div>
                                <div class="row">
                                    <input name="end_date" id="end_date" type="date" class="form-control datepicker" required>
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-12 p-1">
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="mon">Mon
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="tue">Tue
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="wed">Wed
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="thu">Thu
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="fri">Fri
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="sat">Sat
                            </label>
                            <label class="checkbox-inline pl-1">
                                <input type="checkbox" value="sun">Sun
                            </label>
                        </div>
                        <div class="row p-1">
                            <button class="btn btn-primary bt-sm" type="submit" id="save-btn">Save</button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row p-2">
                            <h2>
                                <span class="month"></span>
                                <span class="year"></span>
                            </h2>
                        </div>
                        <hr>
                        <div class="row">
                            <ul style="list-style-type: none;" class="col-md-12">
                                <div class="days"></div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#start_date').on('change', function () {
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                let date = new Date($(this).val());
                let year = date.getFullYear();
                let month = date.getMonth();
                let month_name = months[date.getMonth()];
                var getDay = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

                function checkLeap() {
                    if((year)%4 === 0 )
                        var days = '29' ;
                    else
                        var days = '28';
                }

                switch(month) {
                    case 1: checkLeap(); break;
                    case 0:
                    case 2:
                    case 4:
                    case 6:
                    case 7:
                    case 9:
                    case 11: var days = '31';
                        break;
                    case 3:
                    case 5:
                    case 8:
                    case 10: var days = '30';
                        break;
                }

                $('.year').text(year);
                $('.month').text(month_name);
                if (month) {
                    for(var i = 1; i<=days; i++){
                        var format = moment(year+'-'+month+'-'+ i).format('ddd')
                        $('.days').append(
                            '<li id="'+i+'" class="'+format.toLowerCase()+'"><div class="row">' +
                            '<div class="col-md-2">'+ i + ' ' + format+
                            '</div><div class="col-md-6"> ' +
                            '<div class="add_event"></div></div>' +
                            '</div></li>' +
                            '<hr>'
                        );
                    };
                }
            })
            $('#save-btn').on('click', function (e) {
                var checked = $('input[type=checkbox]:checked').map(function(_, el) {
                    return $(el).val();
                }).get();
                var name = $('.event_name').val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                if(name == "" || start_date == "" || end_date == ""){
                    $('.message-error').removeClass('d-none');
                    $('.message-error').text('Please fill the event name and date');
                    setTimeout(function () {
                        $('.message-error').addClass('d-none');
                    }, 3000)
                }else{
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "calendar/create",
                        type: "POST",
                        data: {
                            event_name: name,
                            start_date: start_date,
                            end_date: end_date,
                            week_days: checked
                        },
                        success: function (data) {
                            if(data.error == true){
                                $('.message-error').removeClass('d-none');
                                $('.message-error').text(data.message);
                                setTimeout(function () {
                                    $('.message-error').addClass('d-none');
                                }, 3000)
                            }
                            if(data.success == true){
                                var week_days = data.data.week_days;
                                var r_one = week_days.replace('["', '');
                                var r_two = r_one.replace('"]', '');
                                var r_three = r_two.replace(/"/g, '');
                                var days = r_three.split(',');
                                console.log(days)

                                days.forEach(getDay)

                                function getDay(item){
                                    var element = $('.'+item);
                                    element.addClass('alert-success');
                                    element.find('.add_event').text(data.data.event_name);
                                }

                                $('.message-success').removeClass('d-none');
                                $('.message-success').text(data.message);
                                setTimeout(function () {
                                    $('.message-success').addClass('d-none');
                                }, 3000)

                            }
                        }
                    })
                }
            })
        });
    </script>
@endsection