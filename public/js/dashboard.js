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
var month = [];


//ajax the opened emails
$.ajax({
    url:"/ajax/tracking",
    type: "POST",
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
    month = formatDateArray(data[1]["Timestamps"]);

    var sent = data[0]["Values"];
    var opened = data[1]["Values"];
    var delivered = data[2]["Values"];
    var clicked = data[3]["Values"];
    var bounced = data[4]["Values"];

    var max = Math.max.apply(Math,opened)+2 ;


    "use strict";
    // ============================================================== 
    // Opened Email Tracking
    // ============================================================== 
     new Chartist.Line('#sales-overview2', {
        labels: month.reverse()
        , series: [
          {meta:"Opened Emails", data: opened}
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
    // Tracker Donut chart
    // ============================================================== 

    $('.stat-sent').html(sent.reduce(add, 0));
    $('.stat-delivered').html(delivered.reduce(add, 0) );
    $('.stat-bounced').html(bounced.reduce(add, 0) );

    $('.track-delivered').html(((delivered.reduce(add, 0)/sent.reduce(add, 0))*100).toFixed(1) + " %");
    $('.track-opened').html(((opened.reduce(add, 0)/sent.reduce(add, 0))*100).toFixed(1) + " %");
    $('.track-clicks').html(((clicked.reduce(add, 0)/sent.reduce(add, 0))*100).toFixed(1) + " %");
    $('.track-bounces').html(((bounced.reduce(add, 0)/sent.reduce(add, 0))*100).toFixed(1) + " %");

    $('.sent_sum').html(sent.reduce(add, 0));
    $('.opened_sum').html(opened.reduce(add, 0));
    $('.delievered_sum').html(delivered.reduce(add, 0));
    $('.clicked_sum').html(clicked.reduce(add, 0));
    $('.bounced_sum').html(bounced.reduce(add, 0));


    var chart = c3.generate({
        bindto: '#trackings',
        data: {
            columns: [
                ['Sent', sent.reduce(add, 0)],
                ['Opened', opened.reduce(add, 0)],
                ['Delievered', delivered.reduce(add, 0)],
                ['Clicked', clicked.reduce(add, 0)],
                ['Bounced', bounced.reduce(add, 0)],
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
            title:"Tracking",
            width:20,
            
        },
        
        legend: {
          hide: true
          //or hide: 'data1'
          //or hide: ['data1', 'data2']
        },
        color: {
              pattern: ['#398bf7',
                  '#7e49f2',
                  '#26c6da',
                  '#9ab2e5',
                  '#1ce59b']
        }
    });
  	  
    // ============================================================== 
    // Sent / opened Comparison
    // ==============================================================

    var max = Math.max.apply(Math,sent)+2 ;

            // ==============================================================
            // Sent / opened Comparison inside Ajax Success
            // ==============================================================

            var chart = new Chartist.Line('.website-visitor', {
                labels: formatDateArray(data[0]["Timestamps"]).reverse(),
                series: [
                    delivered.reverse()
                    , sent.reverse()
                ]}, {
                low: 0,
                high: max,
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






};

function add(a, b) {
    return a + b;
}

function formatDateArray(data) {
    var month= [];
    $.each( data, function( key, value ) {
        month.push($.format.date(value , "MMM-d"));
    });

    return month
}