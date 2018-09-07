$('.custom-file-input').on('change',function(){
    //get the file name
    var fileName = $(this).val();
    fileName= fileName.replace("C:\\fakepath\\", ""); //Remove the C:/fakepath/
    //replace the "Choose a file" label
    $('.custom-file-control').html(fileName);
});

$('.send-email-button').append('  <i class="mdi mdi-send"></i>'); //add the send icon to the button

$('.add-custom-file-control-after').append('<span class="custom-file-control"></span>');

$('#profile_submit').click(function(){
    if ($('#password').val() != $('#password2').val() ){
        alert('password mismatch!');
    }
});

//Delete Button
$('.delete').click(function(){
    if (confirm('Are you sure you want to Delete the selected list  ?')) {
        var id = $(this).attr("data-optionid");

        $.ajax({
            url:"/delete-list",
            type: "POST",
            data: {
                "id": id
            },
            async: true,
            success: function (data)
            {
                console.log(data);
                location.href='/lists';
            },
            error: function(xhr, textStatus, errorThrown){
                console.log('request failed');
            }
        });


    } else {
        // Do nothing! :D
    }
});

//Send Button
$('.send').click(function(){
    var id = $(this).attr("data-optionid");
    location.href='/emails/'+id;
});

//Stop Button
$('#stop').click(function(){

    if (confirm('Are you sure you want to Stop the selected emailing campaign ?')) {
        var id = $(this).attr("data-orderid");
        var userid = $(this).attr("data-user-id");

        $.ajax({
            url:"/ajax/stop",
            type: "POST",
            data: {"id": id,"userid": userid },
            async: true,
            success: function (data)
            {
                location.href='/';
            },
            error: function(xhr, textStatus, errorThrown){
                console.log('request failed');
            }
        });


    }

});


//Tracking ajax
var userid = $(this).attr("data-user-id");
var year = $('.year').val();
var month = [];
var total =[];

//ajax the opened emails
$.ajax({
    url:"/ajax/opened_email",
    type: "POST",
    data: {"userid": userid, "year":year,"method":"opened" },
    async: true,
    success: function (data)
    {
        dashboard(data)

    },
    error: function(xhr, textStatus, errorThrown){
        console.log('Tracking request failed');
    }
});


$('.year').on('change',function(){
    location.href='/';
});

function dashboard(data) {
    month.unshift('start');
    total.unshift('0');
    $.each( data, function( key, value ) {
        month.push(value.month);
        total.push(value.total);
    });


    var max = Math.max.apply(Math,total)+2 ;

    "use strict";
    // ============================================================== 
    // Opened Email Tracking
    // ============================================================== 
     new Chartist.Line('#sales-overview2', {
        labels: month
        , series: [
          {meta:"Opened Emails", data: total}
      ]
    }, {
        low: 0
        , high:max
        , showArea: true
        , divisor: 10
        , lineSmooth:true
        , fullWidth: true
        , showLine: true
        , chartPadding: 30
        , axisX: {
            showLabel: true
            , showGrid: true
            , offset: 50
        }
        , plugins: [
        	Chartist.plugins.tooltip()
      	], 
      	// As this is axis specific we need to tell Chartist to use whole numbers only on the concerned axis
        axisY: {
        	onlyInteger: true
            , showLabel: true
            , scaleMinSpace: 50 
            , showGrid: true
            , offset: 10,
            labelInterpolationFnc: function(value) {
		      return (value / 1)
		    },

        }
        
    });
     // ============================================================== 
    // Visitor
    // ============================================================== 
    
    var chart = c3.generate({
        bindto: '#visitor',
        data: {
            columns: [
                ['Other', 30],
                ['Desktop', 10],
                ['Tablet', 40],
                ['Mobile', 50],
            ],
            
            type : 'donut',
            onclick: function (d, i) { console.log("onclick", d, i); },
            onmouseover: function (d, i) { console.log("onmouseover", d, i); },
            onmouseout: function (d, i) { console.log("onmouseout", d, i); }
        },
        donut: {
            label: {
                show: false
              },
            title:"Visits",
            width:20,
            
        },
        
        legend: {
          hide: true
          //or hide: 'data1'
          //or hide: ['data1', 'data2']
        },
        color: {
              pattern: ['#e6f1ec','#745af2', '#26c6da', '#1e88e5']
        }
    });
  	  
    // ============================================================== 
    // Sent / opened Comparison
    // ==============================================================

var sentMonthly = [];
var sentTotal = [];


    //ajax the sent emails
    $.ajax({
        url:"/ajax/opened_email",
        type: "POST",
        data: {"userid": userid, "year":year,"method":"sent" },
        async: true,
        success: function (data)
        {
            sentMonthly.unshift('start');
            sentTotal.unshift('0');
            $.each( data, function( key, value ) {
                sentMonthly.push(value.month);
                sentTotal.push(value.total);
            });
        var sentMax = Math.max.apply(Math,sentTotal)+2;
            // ==============================================================
            // Sent / opened Comparison inside Ajax Success
            // ==============================================================

            var chart = new Chartist.Line('.website-visitor', {
                labels: month,
                series: [
                    sentTotal // to be total sent emails per user and per month
                    , total
                ]}, {
                low: 0,
                high: sentMax,
                showArea: true,
                fullWidth: true,
                plugins: [
                    Chartist.plugins.tooltip()
                ],
                axisY: {
                    onlyInteger: true
                    , scaleMinSpace: 40
                    , offset: 20
                    , labelInterpolationFnc: function (value) {
                        return (value / 1) ;
                    }
                },
            });
            // Offset x1 a tiny amount so that the straight stroke gets a bounding box
            // Straight lines don't get a bounding box
            // Last remark on -> http://www.w3.org/TR/SVG11/coords.html#ObjectBoundingBox
            chart.on('draw', function(ctx) {
                if(ctx.type === 'area') {
                    ctx.element.attr({
                        x1: ctx.x1 + 0.001
                    });
                }
            });

            // Create the gradient definition on created event (always after chart re-render)
            chart.on('created', function(ctx) {
                var defs = ctx.svg.elem('defs');
                defs.elem('linearGradient', {
                    id: 'gradient',
                    x1: 0,
                    y1: 1,
                    x2: 0,
                    y2: 0
                }).elem('stop', {
                    offset: 0,
                    'stop-color': 'rgba(255, 255, 255, 1)'
                }).parent().elem('stop', {
                    offset: 1,
                    'stop-color': 'rgba(38, 198, 218, 1)'
                });
            });


            // ==============================================================
            // End the Sent/opened Comparison in Ajax
            // ==============================================================

        },
        error: function(xhr, textStatus, errorThrown){
            console.log('Tracking request failed');
        }
    });




};

